<?php

/**
*    SimpleSAMLphp WordpressAuth
*    Version 0.2.0
*
*    SimpleSAMLphp module to use Wordpress as a SAML 2.0 Identity Provider.
*
*    WordpressAuth is a SimpleSAMLphp authentication module, that allows to use
*    the Wordpress user database as the authentication source. The code was written
*    for MySQL/MariaDB.
*
*
*    Documentation: https://github.com/disisto/simplesamlphp-wordpressauth
*
*    Forked from https://github.com/OliverMaerz/WordpressAuth
*    forked from https://github.com/Financial-Edge/simplesamlphp-module-wordpressauth/
*
*    Licensed under GNU GPL v2.0 (https://github.com/disisto/simplesamlphp-wordpressauth/blob/master/LICENSE)
*
*    Copyright (c) 2023-2025 Roberto Di Sisto
*
*    Permission is hereby granted, free of charge, to any person obtaining a copy
*    of this software and associated documentation files (the "Software"), to deal
*    in the Software without restriction, including without limitation the rights
*    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
*    copies of the Software, and to permit persons to whom the Software is
*    furnished to do so, subject to the following conditions:
*
*    The above copyright notice and this permission notice shall be included in all
*    copies or substantial portions of the Software.
*
*    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
*    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
*    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
*    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
*    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
*    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
*    SOFTWARE.
*
**/

namespace SimpleSAML\Module\wordpressauth\Auth\Source;

use Exception;
use PDO;
use SimpleSAML\Error\Error;
use SimpleSAML\Logger;
use SimpleSAML\Module\core\Auth\UserPassBase;
use SimpleSAML\Module\wordpressauth\Vendor\PasswordHash;

class WordpressAuth extends UserPassBase {
    // The database DSN
    private $dsn;

    // The database username & password
    private $username;
    private $password;

    public function __construct($info, $config) {
        parent::__construct($info, $config);

        // Load DSN, username, password and userstable from configuration
        if (!is_string($config['dsn'])) {
            throw new Exception('Missing or invalid dsn option in config.');
        }
        $this->dsn = $config['dsn'];

        if (!is_string($config['username'])) {
            throw new Exception('Missing or invalid username option in config.');
        }
        $this->username = $config['username'];

        if (!is_string($config['password'])) {
            throw new Exception('Missing or invalid password option in config.');
        }
        $this->password = $config['password'];
    }

    /**
     * Verify password against WordPress hash
     * Supports:
     *   - WordPress 6.8+ BCrypt with $wp$ prefix (uses HMAC-SHA384 + Base64 before verification)
     *   - Standard BCrypt hashes: $2y$, $2a$, $2b$
     *   - Legacy phpass hashes: $P$, $H$
     *
     * @param string $password The plaintext password
     * @param string $stored_hash The hash from database
     * @return bool True if password matches
     */
    private function verifyPassword(string $password, string $stored_hash): bool {
        // WordPress 6.8+ prefixed BCrypt hash
        if (substr($stored_hash, 0, 4) === '$wp$') {
            Logger::debug('WordpressAuth: Detected $wp$ prefix, using WordPress 6.8+ HMAC-SHA384 method');

            // WordPress 6.8 uses HMAC-SHA384 + Base64 encoding before BCrypt verification
            $password_to_verify = base64_encode(hash_hmac('sha384', $password, 'wp-sha384', true));

            // Strip only $wp but keep the $ prefix (substr 3, not 4)
            $hash = substr($stored_hash, 3);

            Logger::debug('WordpressAuth: Verifying HMAC-SHA384(password) against BCrypt hash');
            return password_verify($password_to_verify, $hash);
        }

        // Standard BCrypt hashes ($2y$, $2a$, $2b$) without $wp$ prefix
        $bcrypt_prefixes = ['$2y$', '$2a$', '$2b$'];
        foreach ($bcrypt_prefixes as $prefix) {
            if (substr($stored_hash, 0, 4) === $prefix) {
                Logger::debug('WordpressAuth: Using password_verify() for standard BCrypt hash');
                return password_verify($password, $stored_hash);
            }
        }

        // Legacy phpass hashes ($P$, $H$)
        $phpass_prefixes = ['$P$', '$H$'];
        foreach ($phpass_prefixes as $prefix) {
            if (substr($stored_hash, 0, 3) === $prefix) {
                Logger::debug('WordpressAuth: Using PasswordHash for legacy phpass hash');
                $hasher = new PasswordHash(8, TRUE);
                return $hasher->CheckPassword($password, $stored_hash);
            }
        }

        // Unknown hash format
        Logger::warning('WordpressAuth: Unknown password hash format: ' . substr($stored_hash, 0, 10) . '...');
        return false;
    }

    protected function login(string $username, string $password): array {
         // Connect to the database
         $db = new PDO($this->dsn, $this->username, $this->password);
         $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

         // Ensure that we are operating with UTF-8 encoding
         $db->exec("SET NAMES 'utf8'");

         // Fetch the table prefix from the database
         $query = "SHOW TABLES LIKE '%users'";
         $tables_st = $db->prepare($query);
         $tables_st->execute();

         $table_prefix = null;

         while ($table_row = $tables_st->fetch(PDO::FETCH_NUM)) {
           $table_name = $table_row[0];

           // Extract the prefix from the table name
           $suffix = 'users';
           $pos = strpos($table_name, $suffix);

           if ($pos !== false) {
             $table_prefix = substr($table_name, 0, $pos);
             break; // Stop after finding the first matching table
           }
         }

         if ($table_prefix === null) {
         // Fallback value, if no suitable prefix was found
         $table_prefix = 'wp_';
         }

         // Prepare statement (PDO)
         $sql = 'SELECT ID, user_login, user_pass, display_name, user_email FROM '.$table_prefix.'users WHERE user_login = :username';

         // Check if username is email and adjust flow to accommodate
         if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $sql = $sql.' OR user_email = :username';
            $sth = $db->prepare('SELECT user_login FROM '.$table_prefix.'users WHERE user_email = :username');
            $sth->execute(['username' => $username]);
            $db_username = $sth->fetchAll()[0]['user_login'] ?? null;
            $email = $username;
            $username = $db_username;
         }

         $st = $db->prepare($sql);

         if (!$st->execute(['username' => $username])) {
            throw new Exception("Failed to query database for user.");
         }

         // Retrieve the row from the database
         $row = $st->fetch(PDO::FETCH_ASSOC);
         if (!$row) {
            // User not found
            throw new Error('WRONGUSERPASS');
         }

         // Verify password using the enhanced method
         if (!$this->verifyPassword($password, $row['user_pass'])) {
            // Invalid password
            Logger::info('WordpressAuth: Invalid password for user: ' . $username);
            throw new Error('WRONGUSERPASS');
         }

         Logger::info('WordpressAuth: Successful login for user: ' . $username);

        // Define the meta keys in an array
        // Show keys even if values are empty/null
         $meta_keys = [
             'first_name',
             'last_name',
             'profile_photo',    // Plugin: Ultimate Member     (https://wordpress.org/plugins/ultimate-member/)
             'scopes'            // Plugin: Extra User Details  (https://wordpress.org/plugins/extra-user-details/)
             //$table_prefix.'capabilities'
             // ... add more keys as needed
          ];


          // Fetch meta_keys from wp_usermeta table defined above
          $meta_keys_placeholders = implode("', '", $meta_keys);
          $meta_sql = "SELECT meta_key, meta_value FROM ".$table_prefix."usermeta WHERE user_id = :id AND meta_key IN ('$meta_keys_placeholders')";

          $meta_st = $db->prepare($meta_sql);
          if (!$meta_st->execute(['id' => $row['ID']])) {
              throw new Exception("Failed to query database for user.");
          }

          $meta_rows = $meta_st->fetchAll();

          $attributes = [
            'uid'           => [$row['ID']],
            'username'      => [$username],
            'email'         => [$row['user_email']],
            'display_name'  => [$row['display_name']]
          ];

         foreach ($meta_rows as $meta_row) {
           $meta_key	    = $meta_row['meta_key'];
           $meta_value	    = $meta_row['meta_value'];

           if (in_array($meta_key, $meta_keys)) {
             $attribute_name = str_replace($table_prefix, '', $meta_key);
             $attributes[$attribute_name] = [$meta_value];
           }
        }


       // Return the attributes
       return $attributes;
    }
}
