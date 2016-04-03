<?php
// +-------------------------------------------------------------------------+
// |  Script for adding new user accounts                                    |
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

// Include dependencies
require_once $_SERVER['DOCUMENT_ROOT'] . '/server/fn_init.php';

// Check for valid request
$ds->checkRequest(
    'ds_admin_user',
    [
        'username',
        'password_1',
        'password_2',
        'status',
        'group'
    ]
);

// API responses
define('USER_L_FAIL',     'Username too short');
define('USER_FAIL',       'Username already in use');
define('PASSWORD_L_FAIL', 'Password too short');
define('PASSWORD_FAIL',   'Passwords do not match');
define('OK',              'User added successfully');

// Username too short
if(strlen($_POST['username']) < 2)
    die($ds->APIResponse('USER_L_FAIL', 3, USER_L_FAIL));

// Query for testing if the user exists
$test = 'SELECT * FROM `ds_users` WHERE `username` = ?';

// Username already exists
if(is_array($ds->query($test, $_POST['username'])))
    die($ds->APIResponse('USER_FAIL', 3, USER_FAIL));

// Password too short
if(strlen($_POST['password_1']) < 4)
    die($ds->APIResponse('PASSWORD_L_FAIL', 3, PASSWORD_L_FAIL));

// Passwords do not match
if($_POST['password_1'] !== $_POST['password_2'])
    die($ds->APIResponse('PASSWORD_FAIL', 3, PASSWORD_FAIL));

// Hash the password
$password = password_hash($_POST['password_1'], PASSWORD_BCRYPT);

// Set the group
$group = empty($_POST['group']) ? null : $_POST['group'];

// Add user data
$data = [
    $_POST['username'],
    $password,
    $_POST['status'],
    $group,
    $ds->account['username']
];

// Add user query
$query = 'INSERT INTO `ds_users` ' .
         '(`username`, `password`, `status`, `group`, `added_by`) ' .
         'VALUES (?,?,?,?,?)';

// If the query fails
if(!$ds->query($query, $data))
    die($ds->APIResponse());

// New user's ID
$id = $ds->db_conn->lastInsertId();

// Log the event
$ds->logEvent('User Added', USER_ADDED, $_POST['username']);

// OK response (including new user's ID)
die($ds->APIResponse('OK', 0, OK, $id));