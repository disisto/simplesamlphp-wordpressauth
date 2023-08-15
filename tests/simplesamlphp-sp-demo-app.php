<?php

/**
*    SimpleSAMLphp Service Provider (SP) Demo Application
*    Version 1.0.0
*
*    Published under: https://github.com/disisto/simplesamlphp-wordpressauth
*
*    Licensed under MIT
*
*    Copyright (c) 2023 Roberto Di Sisto
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
**/

###################################
########## SimpleSAMLphp ##########
###################################

// Define the service provider (SP) authentication source, e.g. "default-sp".
$authSource   = 'default-sp';

// Load local SimpleSAMLphp library
$sspAutoload  = '/var/simplesamlphp/src/_autoload.php';

// Define URL to download the profile pictures
// Check line 92


if (file_exists($sspAutoload)) {
  require_once $sspAutoload;
}
else {
  $error = '
            <div class="col-md-12 mt-5">
              <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                  </svg>
                    Error
                  </h4>
                  <p>File not found:</p>
                  <pre>'.$sspAutoload.'</pre>
                  <hr>
                  <p>Unable to load required library</p>
              </div>
            </div>
           ';
}

session_start();
// Initialize the SimpleSAMLphp authentication object
if (!isset($error)) {
  $auth = new \SimpleSAML\Auth\Simple($authSource);

  // Force login
  #$auth->requireAuth();

  // Check if the user is already authenticated
  $loginNeeded 	= false;
  if (!$auth->isAuthenticated()) {
      // User is not authenticated
      $loginNeeded 	= true;
  } else {
      // User is authenticated
      $attributes = $auth->getAttributes();

      foreach ($attributes as $key => $valueArray) {
        ${$key} = (!empty($valueArray[0]) ? $valueArray[0] : null);
    }
  }

  // Make sure you restore your own session data
  $session = \SimpleSAML\Session::getSessionFromRequest();
  $session->cleanup();
  session_write_close();
}

###################################
########## SimpleSAMLphp ##########
###################################

$styledOutput = '';

if (!empty($attributes)) {
    foreach ($attributes as $key => $valueArray) {
        $styledOutput .= '<div class="col-md-12 mt-1"><label class="labels">' . $key . '</label>';

        foreach ($valueArray as $value) {
            $styledOutput .= '<input type="text" class="form-control" placeholder="[no value]" value="' . htmlspecialchars($value) . '" disabled>';
        }

        $styledOutput .= '</div>';
    }
}

## $profile_photo is an attribute of WordPress plugins that allow saving profile pictures
## locally, instead of loading it from gravatar.com. Don't expect anything special if you 
## don't use plugins like 'Ultimate Member' and co.
if (empty($profile_photo)) {
  $profile_photo = 'https://secure.gravatar.com/avatar/'.md5($email).'?s=96&d=mm&r=g';
} 
else {
  // If you use plugins like 'Ultimate Member', paste the correct URL here to make it work.
  $profile_photo = 'https://your-domain.tld/wp-content/uploads/ultimatemember/'.$uid.'/'.$profile_photo;
}

if (isset($loginNeeded) && ($loginNeeded === true) ) {
  $loginNeeded    = '<a class="btn btn-outline-primary" href="'.htmlspecialchars($auth->getLoginURL()).'" role="button">Login</a>';
  $loginMessage   = '
                    <div class="col-md-12 mt-5">
                      <div class="alert alert-warning" role="alert">
                          <h4 class="alert-heading">Please login</h4>
                          <p>You must be logged in to get the attributes.</p>
                          <div class="d-flex justify-content-center"> <!-- Hier wird der Button zentriert -->
                              <a class="btn btn-primary me-2" href="'.htmlspecialchars($auth->getLoginURL()).'" role="button">Login</a>
                          </div>
                      </div>
                    </div>
                    ';
}
else {
  if (!isset($error)) {
    $loginNeeded    = '<a class="btn btn-outline-primary" href="'.htmlspecialchars($auth->getLogoutURL()).'" role="button">Logout</a>';
  }
}

?>
<!doctype html>
<html lang="en" data-bs-theme="auto">
  <head><script src="https://getbootstrap.com/docs/5.3/assets/js/color-modes.js"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Roberto Di Sisto">
    <meta name="description" content="SimpleSAMLphp Wordpress authentication source https://github.com/disisto/simplesamlphp-wordpressauth">
    <title>SimpleSAMLphp WordpressAuth</title>
    <meta name="theme-color" content="#712cf9">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">

    <style>
      .bi {
        vertical-align: -.125em;
        fill: currentColor;
      }

      .labels {
        font-size: 13px;
      }

      pre {
        white-space: pre-wrap;
      }
    </style>

    
  </head>
  <body>
    <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
      <symbol id="check2" viewBox="0 0 16 16">
        <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
      </symbol>
      <symbol id="circle-half" viewBox="0 0 16 16">
        <path d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"/>
      </symbol>
      <symbol id="moon-stars-fill" viewBox="0 0 16 16">
        <path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"/>
        <path d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z"/>
      </symbol>
      <symbol id="sun-fill" viewBox="0 0 16 16">
        <path d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/>
      </symbol>
      <symbol id="person-check" viewBox="0 0 16 16">
        <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm1.679-4.493-1.335 2.226a.75.75 0 0 1-1.174.144l-.774-.773a.5.5 0 0 1 .708-.708l.547.548 1.17-1.951a.5.5 0 1 1 .858.514ZM11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/>
        <path d="M8.256 14a4.474 4.474 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10c.26 0 .507.009.74.025.226-.341.496-.65.804-.918C9.077 9.038 8.564 9 8 9c-5 0-6 3-6 4s1 1 1 1h5.256Z"/>
      </symbol>
      <symbol id="github" viewBox="0 0 16 16">
        <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.012 8.012 0 0 0 16 8c0-4.42-3.58-8-8-8z"/>
      </symbol>
    </svg>

<div class="col-lg-8 mx-auto p-4 py-md-5">
  <header class="d-flex align-items-center pb-3 mb-5 border-bottom">
    <a href="" class="d-flex align-items-center text-body-emphasis text-decoration-none">
      <svg class="bi me-sm-1" width="40" height="32"><use xlink:href="#person-check"/></svg>
      <span class="fs-4 d-none d-sm-inline me-2">SimpleSAMLphp</span>
      <span class="fs-4">WordpressAuth</span>
    </a>

    <div class="ms-auto d-flex align-items-center">
      <?= (isset($loginNeeded) ? $loginNeeded : null); ?>
    </div>

    <div class="btn-group mx-2">
      <button class="btn btn-outline-primary dropdown-toggle d-flex align-items-center"
        id="bd-theme"
        type="button"
        aria-expanded="false"
        data-bs-toggle="dropdown"
        aria-label="Toggle theme (auto)">
        <svg class="bi my-1 theme-icon-active" width="1em" height="1em"><use href="#circle-half"></use></svg>
        <span class="visually-hidden" id="bd-theme-text">Toggle theme</span>
      </button>

      <ul class="dropdown-menu dropdown-menu-end dropdown-menu-theme shadow" aria-labelledby="bd-theme-text">
        <li>
        <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
          <svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em"><use href="#sun-fill"></use></svg>
            Light
          <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
        </button>
        </li>
        <li>
        <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
          <svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em"><use href="#moon-stars-fill"></use></svg>
            Dark
          <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
        </button>
        </li>
        <li>
        <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto" aria-pressed="true">
          <svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em"><use href="#circle-half"></use></svg>
            Auto
          <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
        </button>
        </li>
      </ul>
    </div>
  </header>

  <main>

    <!-- TAB PILLS-->
    <div class="container mt-4">
      <div class="row">
        <div class="col-md-4"></div>

        <div class="col-md-4">
          <ul class="nav nav-pills d-flex justify-content-center justify-content-lg-start ps-3" id="myTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="tab-styled-tab" data-bs-toggle="tab" data-bs-target="#tab-styled" type="button" role="tab" aria-controls="tab-styled" aria-selected="true">Styled</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="tab-raw-tab" data-bs-toggle="tab" data-bs-target="#tab-raw" type="button" role="tab" aria-controls="tab-raw" aria-selected="false">Raw</button>
            </li>
          </ul>
        </div>

        <div class="col-md-4"></div>
      </div>
    </div>



    <!-- TAB CONTENT-->
    <div class="tab-content" id="myTabsContent">
      <div class="tab-pane fade show active" id="tab-styled" role="tabpanel" aria-labelledby="tab-styled-tab">
        <!-- STYLED -->
        <div class="container rounded mt-2 mb-5">

        <div class="row">
          <div class="col-md-4">
              <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                <img class="rounded-circle mt-5" width="150" alt="Profile Picture" src="<?= $profile_photo; ?>">
                <span class="font-weight-bold mt-2"><?= (!empty($display_name) ? $display_name : '[display_name no value]') ?></span>
                <span class="text-muted labels"><?= (!empty($email) ? $email : '[email no value]') ?></span>
              </div>
          </div>
          <div class="col-md-5">
              <div class="p-3 py-5">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                      <h4 class="text-right">Profile Attributes</h4>
                  </div>
                  <div class="row mt-2">
                      <div class="col-md-6"><label class="labels">First Name</label><input type="text" class="form-control" placeholder="[no value]" value="<?= (!empty($first_name) ? $first_name : null) ?>" disabled></div>
                      <div class="col-md-6"><label class="labels">Last Name</label><input type="text" class="form-control"  placeholder="[no value]" value="<?= (!empty($last_name) ? $last_name : null) ?>" disabled></div>
                  </div>
                  <div class="row mt-1">
                    <?= (!empty($error) ? $error : null).
                        (!empty($loginMessage) ? $loginMessage : null).
                        $styledOutput; ?>
                  </div>
              </div>
          </div>
          <div class="col-md-1">
            <div class="d-flex flex-column align-items-center text-center p-3 py-5"></div>
          </div>
        </div>

        </div>
        <!-- STYLE-->
      </div>
      <div class="tab-pane fade" id="tab-raw" role="tabpanel" aria-labelledby="tab-raw-tab">
        <!-- RAW -->
        <div class="container rounded mt-2 mb-5">

          <div class="row">
            <div class="col-md-2">
            </div>
            <div class="col-md-7">
              <div class="p-3"></div>
                <div class="card">
                  <div class="card-header">
                    print_r($attributes);
                  </div>
                  <div class="card-body bg-dark text-white">
                   <pre><?php (isset($attributes) ? print_r($attributes) : null ); ?></pre>
                  </div>
                </div>
                <?= (!empty($error) ? $error : null).
                    (!empty($loginMessage) ? $loginMessage : null) ?>
              </div>
            </div>
            <div class="col-md-1">
              <div class="d-flex flex-column align-items-center text-center p-3 py-5"></div>
            </div>
          </div>

        </div>
        <!-- RAW-->
      </div>
   

  </main>
  <footer class="pt-5 my-5 text-body-secondary border-top">
    &copy; <?=  ((date('Y') == '2023') ? date('Y') : '2023-'.date('Y')); ?>
    <a href="https://github.com/disisto/simplesamlphp-wordpressauth" title="GitHub" target="_blank" rel="noopener noreferrer nofollow" class="text-muted text-decoration-none">
      <svg class="bi" width="16" height="16"><use xlink:href="#github"/></svg>
       SimpleSAMLphp WordpressAuth
    </a>
  </footer>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>

  </body>
</html>