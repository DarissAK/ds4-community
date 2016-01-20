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

// Include and create a new Dynamic Suite Instance
require_once($_SERVER['DOCUMENT_ROOT'] . '/server/fn_init.php');

if(
    $ds->checkSession() &&
    $ds->checkPermission('ds_admin_user') &&
    isset($_POST['ds_user'])
) {

    // User data query
    $query = 'UPDATE `ds_user` SET `ds_user_status` = ?, ' .
             '`ds_user_group` = ? WHERE `ds_user` = ?;';

    // User data
    $data = [
        $_POST['ds_user_status'],
        $_POST['ds_user_group'],
        $_POST['ds_user']
    ];

    // Execute the query
    $ds->query($query, $data);

    // Set inactive date if changing user to inactive
    if(!$_POST['ds_user_status']) {

        // Set inactive query
        $query = 'UPDATE `ds_user` SET `ds_user_inactive_timestamp` = NOW() ' .
                 'WHERE `ds_user` = ?';

        // Execute the query
        $ds->query($query, $_POST['ds_user']);

    }

    // If the administrator value was given
    if(isSet($_POST['ds_user_administrator'])) {

        // Update administrator status
        $query = 'UPDATE `ds_user` SET `ds_user_administrator` = ? ' .
                 'WHERE `ds_user` = ?;';

        // Administrator data
        $data = [
            $_POST['ds_user_administrator'],
            $_POST['ds_user']
        ];

        // Execute the query
        $ds->query($query, $data);

    }

    // If the password values were given
    if(
        !empty($_POST['ds_user_password_1']) &&
        !empty($_POST['ds_user_password_2'])
    ) {

        // If the passwords match
        if($_POST['ds_user_password_1'] === $_POST['ds_user_password_2']) {

            // Hash the given password
            $password =
                password_hash($_POST['ds_user_password_1'], PASSWORD_BCRYPT);

            // Password query
            $query = 'UPDATE `ds_user` SET `ds_user_password` = ? ' .
                     'WHERE `ds_user` = ?;';

            // Password data
            $data = [
                $password,
                $_POST['ds_user']
            ];

            // Execute the query
            $ds->query($query, $data);

        }

        // Passwords don't match, kill the script
        else {

            die($ds->APIResponse('PASSWORD_ERROR', 3, 'Invalid password given'));

        }

    }

    // Execute the user update
    if(!$ds->db_error) {

        // Log the event
        $ds->logEvent('User Updated', 3, $_POST['ds_user']);

        // Send OK response
        die($ds->APIResponse('OK', 0, "User {$_POST['ds_user']} Updated"));

    }

    // On query failure
    else {

        die($ds->APIResponse());

    }

} else {

    // Invalid request
    die($ds->APIResponse());

}