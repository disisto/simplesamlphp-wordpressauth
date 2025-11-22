# SimpleSAMLphp WordpressAuth

SimpleSAMLphp module to use WordPress as a SAML 2.0 Identity Provider.

WordpressAuth is a <a href="https://github.com/simplesamlphp/simplesamlphp">SimpleSAMLphp</a> authentication module, that allows to use the WordPress user database as the authentication source. The code was written for MySQL/MariaDB.

<img src="https://raw.githubusercontent.com/disisto/simplesamlphp-wordpressauth/master/img/simplesamlphp-sp-demo-app.gif">

---

## Content

- [Requirements](https://github.com/disisto/simplesamlphp-wordpressauth/wiki#requirements)
- [Installation](https://github.com/disisto/simplesamlphp-wordpressauth/wiki#installation)
  - [Download](https://github.com/disisto/simplesamlphp-wordpressauth/wiki#download)
     - [cURL](https://github.com/disisto/simplesamlphp-wordpressauth/wiki#curl)
     - [wget](https://github.com/disisto/simplesamlphp-wordpressauth/wiki#wget)
     - [git](https://github.com/disisto/simplesamlphp-wordpressauth/wiki#git)
  - [Enable Module](https://github.com/disisto/simplesamlphp-wordpressauth/wiki#enable-module)
  - [Adding database credentials](https://github.com/disisto/simplesamlphp-wordpressauth/wiki#adding-database-credentials)
  - [Switch authentication source](https://github.com/disisto/simplesamlphp-wordpressauth/wiki#switch-authentication-source)
- [Testing](https://github.com/disisto/simplesamlphp-wordpressauth/wiki#testing)
- [Credits](https://github.com/disisto/simplesamlphp-wordpressauth/wiki#credits)

---

## Requirements

| Component | Minimum | Tested | Notes |
|-----------|---------|--------|-------|
| SimpleSAMLphp | 2.x | 2.4.3 | 2.0.x should work |
| WordPress | 5.0+ | 6.8.3 | Requires v0.2.0 for WP 6.8+ |
| PHP | 7.4+ | 8.4 | - |
| Database | MySQL 5.7+<br>MariaDB 10.3+ | MariaDB 12.0.2 | - |

**Password Hash Support**: phpass (legacy), BCrypt (standard), BCrypt with `$wp$` prefix (WordPress 6.8+)

---

## Credits

Big thanks to <a href="https://github.com/OliverMaerz/WordpressAuth">Oliver Maerz</a> for the initial inspiration and <a href="https://github.com/Financial-Edge/simplesamlphp-module-wordpressauth/tree/master">Financial-Edge</a> for the extensions to the original.

---

This project is not affiliated with <a href="https://simplesamlphp.org/">SimpleSAMLphp</a>, <a href="https://wordpress.com/">WordPress</a> and/or <a href="https://mariadb.org/">MariaDB</a>.<br>All mentioned trademarks are the property of their respective owners.
