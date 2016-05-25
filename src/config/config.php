<?php
// +-------------------------------------------------------------------------+
// |  Core configuration file                                                |
// |  Review documentation for more information on possible values           |
// +-------------------------------------------------------------------------+
// |  Copyright 2016 Simplusoft LLC                                          |
// |  All Rights Reserved.                                                   |
// +-------------------------------------------------------------------------+
// |  This program is free software; you can redistribute it and/or modify   |
// |  it under the terms of the GNU General Public License as published by   |
// |  the Free Software Foundation version 2.                                |
// |                                                                         |
// |  This program is distributed in the hope  that  it will be useful, but  |
// |  WITHOUT  ANY  WARRANTY;   without   even   the  implied  warranty  of  |
// |  MERCHANTABILITY  or  FITNESS  FOR  A PARTICULAR PURPOSE.  See the GNU  |
// |  General Public License for more details.                               |
// |                                                                         |
// |  You should have received a copy of the  GNU  General  Public  License  |
// |  along  with  this  program;   if  not,  write  to  the  Free Software  |
// |  Foundation,  Inc.,  51  Franklin  Street,  Fifth  Floor,  Boston,  MA  |
// |  02110-1301, USA.                                                       |
// +-------------------------------------------------------------------------+

$cfg = [];

// +-------------------------------------------------------------------------+
// |  Developer Settings                                                     |
// +-------------------------------------------------------------------------+

// Display Errors. Production Value: 0; Development Value: 1
ini_set("display_errors", 1);

// Zend X-Debug HTML Errors
ini_set('html_errors', 1);

// Hard error log file.
// Make sure the file is writeable
$cfg['log_dir'] = '/path/to/error.log';

// PDO Exception Timeout
// The time in seconds it takes before a database connection attempt is
// dropped. 1 second is usually a good value, you may have to increase it if
// your database server has poor latency to your web host.
$cfg['pdo_exception_timeout'] = 1;

// Error Reporting Type(s) For a list of possible values see:
// http://php.net/manual/en/function.error-reporting.php
error_reporting(E_ALL);

// Manually lock out all users
// Will not lock out administrators
$cfg['manual_lockout'] = FALSE;

// +-------------------------------------------------------------------------+
// |  Version / Application Settings                                         |
// +-------------------------------------------------------------------------+

// System version and copyright (at login)
$cfg['system_footer'] = 'Dynamic Suite 4.1 Community Edition ' .
                        '&#8212; &#169; 2016 Simplusoft LLC';

// System header text (app name)
$cfg['system_header'] = 'DS4 Community';

// Header for the login form
// Usually your application name or organization name
$cfg['system_login_header'] = 'My Organisation';

// System HTML title
// If set to FALSE, prefix + module name will be used
$cfg['system_title'] = FALSE;

// System HTML title default
$cfg['system_title_default'] = 'Dynamic Suite';

// System HTML title prefix
$cfg['system_title_prefix'] = 'DS4';

// System version
$cfg['system_version'] = '4.1.1';

// +-------------------------------------------------------------------------+
// |  Database Settings                                                      |
// +-------------------------------------------------------------------------+

// This framework was developed and tested on MySQL, although since it uses
// PHP PDO, theoretically other database types should work.
// YMMV with other database deployments such as MSSQL, PostgreSQL, etc

// PDO database type (Tested: 'mysql')
$cfg['db_type'] = 'mysql';

// The host address of the database
$cfg['db_host'] = 'localhost';

// The name of the database
$cfg['db_name'] = 'database';

// Database username
$cfg['db_user'] = 'username';

// Database password
$cfg['db_pass'] = 'password';

// Database DSN
$cfg['db_dsn'] = $cfg['db_type'] . ":host=" . $cfg['db_host'] . ";dbname=" .
                 $cfg['db_name'];

// +-------------------------------------------------------------------------+
// |  Regional Settings                                                      |
// +-------------------------------------------------------------------------+

// ISO 639-1 Language code of the application
$cfg['language'] = 'en';

// Charset to use for the application
$cfg['charset'] = 'UTF-8';

// Timezone where the application is primarily used. Visit the link for a list
// of values: http://php.net/manual/en/timezones.php
date_default_timezone_set('America/Los_Angeles');

// The date format used. For a list of possible values, visit:
// http://php.net/manual/en/function/date.php
$cfg['date_format'] = 'm/d/Y';

// The time format used. For a list of possible values, visit:
// http://php.net/manual/en/function/date.php
$cfg['time_format'] = 'g:i A';

// The timestamp format used. For a list of possible values, visit:
// http://php.net/manual/en/function/date.php
$cfg['timestamp_format'] = 'm/d/Y \a\t g:i:s A';

// +-------------------------------------------------------------------------+
// |  Install Settings                                                       |
// +-------------------------------------------------------------------------+

// The domain where the application is installed. Must include the protocol.
// Do not include tailing slashes
$cfg['install_domain'] = 'https://www.example.com';

// A unique session instance identifier
// Unless you have a specific reason, this should be your organization name
// or another unique identifier. If you are running multiple instances of
// the Dynamic Suite on the same server/host, each instance must have a
// different session ID to avoid collision
$cfg['session_id'] = 'myOrg';

// +-------------------------------------------------------------------------+
// |  Contact Settings                                                       |
// +-------------------------------------------------------------------------+

// Contact for account related settings, usually the email address for your
// organizations HR or IT manager/application administrator
$cfg['account_mailto'] = 'hr@example.com';

// Report errors and feedback to this email address
$cfg['errors_mailto'] = 'developer@example.com';

// +-------------------------------------------------------------------------+
// |  Module Settings                                                        |
// +-------------------------------------------------------------------------+

// The default module that a user will be directed to at login.
// You can specify specific pages here by using slashes (ex: module/page)
$cfg['mod_default'] = 'about/credits';

// An array of modules to load
$cfg['ds_modules'] = [
    'about',
    'test',
    'task-scheduler',
    'administrator'
];

// +-------------------------------------------------------------------------+
// |  Options Settings                                                       |
// +-------------------------------------------------------------------------+

// The number of login attempts available before you get locked out
$cfg['login_attempts'] = 5;

// The timeout (in seconds) before you can try to log in again after you
// reach the maximum number of attempts
$cfg['login_timeout'] = 480;

// +-------------------------------------------------------------------------+
// |  Display Settings                                                       |
// +-------------------------------------------------------------------------+

// The message that pops up when lock_out is set to TRUE
// Note: Administrators bypass lockout
$cfg['lock_out_message'] = 'Down for Maintenance';
