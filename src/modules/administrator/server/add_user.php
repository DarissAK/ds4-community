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
    $ds->checkSession() &&
    $ds->checkPermission('ds_admin_user') &&
    isset($_POST['ds_user']) &&
    isset($_POST['ds_user_password_1']) &&
    isset($_POST['ds_user_password_2']) &&
    isset($_POST['ds_user_status']) &&
    isset($_POST['ds_user_group'])
) {

    // User already exists
    if($ds->getUserAcct($_POST['ds_user'])) {

        die($ds->APIResponse('USER_FAIL', 3, 'Username already in use'));

    }

    // Passwords don't match
    elseif($_POST['ds_user_password_1'] !== $_POST['ds_user_password_2']) {

        die($ds->APIResponse('PASSWORD_FAIL', 3, 'Passwords do not match'));

    }

    // No issues found, continue
    else {

        // Hash the password
        $password = password_hash($_POST['ds_user_password_1'], PASSWORD_BCRYPT);

        // Add user query
        $query = 'INSERT INTO `ds_user` (' .
                 '`ds_user`, `ds_user_password`, `ds_user_status`, ' .
                 '`ds_user_group`, `ds_user_added_by`) ' .
                 'VALUES (?,?,?,?,?)';

        // Add user data
        $data = [
            $_POST['ds_user'],
            $password,
            $_POST['ds_user_status'],
            $_POST['ds_user_group'],
            $ds->username
        ];

        // Execute the query
        $ds->query($query, $data);

        // If the query is a success
        if(!$ds->db_error) {

            // Log the event
            $ds->logEvent('User Added', 2, $_POST['ds_user']);

            // OK response
            die($ds->APIResponse('OK', 0, 'User added successfully'));

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