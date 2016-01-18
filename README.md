# ds4-community
Dynamic Suite 4 Framework - Community Edition

Copyright (C) 2016 Simplusoft LLC

Licensed under GPLv2

## About
This framework is designed to make login-only web applications in PHP. User's are managed by an administrator, rather than open registration. The framework acts as an interface between "modules" that the application developer may choose to write. By default, the framework comes with two modules that may be used as guides to create your application.

## Includes
The Dynamic Suite includes the following third party libraries:
* Bootstrap v3.3.5
* jQuery JavaScript Library v2.1.4
* Font Awesome 4.5.0 by @davegandy
* jQuery UI v1.11.4
* jquery.mask.js v1.13.4
* Spectrum Colorpicker v1.7.1
* Summernote wysiwyg editor on Bootstrap v0.6.16

## Installing
#### Requirements
* Apache2 with rewrite enabled
* Access to apache configuration (Virtual Hosts)
* PHP 5.6+
* PHP PDO compatable database (mysql/mariadb tested)
This framework was designed on a basic Debian LAMP stack, YMMV with other setups, although others should theoretically work.
#### Setup
* Set your apache rewrite rules and vhost (found in /apache)
* Create a database/user and execute create_tables.sql (found in /sql) to create your tables and default user
* Edit the configuration file to your requirements (found in /src/config)
* Start application development!

## Todo
* Create documentation on module development
* Clean up and refactor
