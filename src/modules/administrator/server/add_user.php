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

// Include and create a new Dynamic Suite Instance
require_once($_SERVER['DOCUMENT_ROOT'] . '/server/fn_init.php');

// On valid request
if(
    $ds->checkPermission('ds_admin_user') &&
    isset($_POST['user']) &&
    isset($_POST['password_1']) &&
    isset($_POST['password_2']) &&
    isset($_POST['status']) &&
    isset($_POST['group'])
) {

    // API responses
    define('USER_FAIL',     'Username already in use');
    define('PASSWORD_FAIL', 'Passwords do not match');
    define('OK',            'User added successfully');

    // User already exists
    if($ds->getUserAcct($_POST['user'])) {

        die($ds->APIResponse('USER_FAIL', 3, USER_FAIL));

    }

    // Passwords don't match
    elseif($_POST['password_1'] !== $_POST['password_2']) {

        die($ds->APIResponse('PASSWORD_FAIL', 3, PASSWORD_FAIL));

    }

    // No issues found, continue
    else {

        // Hash the password
        $password = password_hash($_POST['password_1'], PASSWORD_BCRYPT);

        // Add user query
        $query = 'INSERT INTO `ds_user`' .
                 '(`user`, `password`, `status`, `group`, `added_by`) ' .
                 'VALUES (?,?,?,?,?)';

        // Add user data
        $data = [
            $_POST['user'],
            $password,
            $_POST['status'],
            $_POST['group'],
            $ds->username
        ];

        // If the query is a success
        if($ds->query($query, $data)) {

            // Log the event
            $ds->logEvent('User Added', USER_ADDED, $_POST['user']);

            // OK response
            die($ds->APIResponse('OK', 0, OK));

        }

        // On query failure
        else {

            die($ds->APIResponse());

        }

    }

}

// Invalid request response
else {

    die($ds->APIResponse());

}