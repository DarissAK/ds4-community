<?php
// +-------------------------------------------------------------------------+
// |  Script for updating user accounts                                      |
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
require_once $_SERVER['DOCUMENT_ROOT'] . '/server/lib/ds.class.php';

// Check for valid request
$ds->checkRequest(
    'ds_admin_user',
    [
        'id',
        'username',
        'old',
        'status',
        'group',
        'password_1',
        'password_2'
    ]
);

// Formatted username
$username = htmlentities($_POST['username']);

// API responses
define('USER_FAIL',       'Username already in use');
define('USER_L_FAIL',     'Username too short');
define('PASSWORD_L_FAIL', 'Password too short');
define('PASSWORD_FAIL',   'Passwords do not match');
define('OK',              'User updated');

// If the username is changing
if(strcasecmp($_POST['username'], $_POST['old'])) {

    // Query for seeing if the user already exists
    $test = 'SELECT * FROM `ds_users` WHERE `username` = ?';

    // If the username is already in use
    if(is_array($ds->query($test, $_POST['username'])))
        die($ds->APIResponse('USER_FAIL', 3, USER_FAIL));

}

// If the username is too short
if(strlen($_POST['username']) < 2)
    die($ds->APIResponse('USER_L_FAIL', 3, USER_L_FAIL));

// If the password is too short (and not empty)
if(!empty($_POST['password_1']) && strlen($_POST['password_1']) < 4)
    die($ds->APIResponse('PASSWORD_L_FAIL', 3, PASSWORD_L_FAIL));

// If the passwords do not match
if($_POST['password_1'] !== $_POST['password_2'])
    die($ds->APIResponse('PASSWORD_FAIL', 3, PASSWORD_FAIL));

// User data query
$query = 'UPDATE `ds_users` SET ' .
         '`username` = ?, `status` = ?, `group` = ?';

$group = empty($_POST['group']) ? null : $_POST['group'];

// User data
$data = [
    $_POST['username'],
    $_POST['status'],
    $group
];

// Add inactive time if setting status to inactive
if(!$_POST['status'])
    $query .= ', `inactive_time` = NOW()';

// Update administrator status if it exists
if(isset($_POST['administrator']) && $ds->is_admin) {
    $query .= ', `administrator` = ?';
    array_push($data, $_POST['administrator']);
}

// Update the password if non-empty passwords are given
if(!empty($_POST['password_1'])) {
    $query .= ', `password` = ?';
    array_push(
        $data,
        password_hash($_POST['password_1'], PASSWORD_BCRYPT)
    );
}

// Update the user for the query
$query .= ' WHERE `user_id` = ?';
array_push($data, $_POST['id']);

// If the query fails
if(!$ds->query($query, $data))
    die($ds->APIResponse());

// Log the event
$ds->logEvent('User Updated', USER_UPDATED, $_POST['username']);

// OK response
die($ds->APIResponse('OK', 0, OK, $username));