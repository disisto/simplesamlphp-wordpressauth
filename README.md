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

- SimpleSAMLphp ```2.0```
  - Tested with SimpleSAMLphp ```2.0.5```
- WordPress
  - Tested with WordPress ```6.3```*
- MariaDB/MySQL
  - Tested with MariaDB ```11.0.3```*

*Backward compatible.

---

## Credits

Big thanks to <a href="https://github.com/OliverMaerz/WordpressAuth">Oliver Maerz</a> for the initial inspiration and <a href="https://github.com/Financial-Edge/simplesamlphp-module-wordpressauth/tree/master">Financial-Edge</a> for the extensions to the original.

---

This project is not affiliated with <a href="https://simplesamlphp.org/">SimpleSAMLphp</a>, <a href="https://wordpress.com/">WordPress</a> and/or <a href="https://mariadb.org/">MariaDB</a>.<br>All mentioned trademarks are the property of their respective owners.
