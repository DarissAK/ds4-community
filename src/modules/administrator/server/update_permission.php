<?php
// +-------------------------------------------------------------------------+
// |  Script for updating permissions                                        |
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
    $ds->checkPermission('ds_admin_permission') &&
    isset($_POST['permission']) &&
    isset($_POST['description']) &&
    isset($_POST['permission_old'])
) {

    // API Responses
    define('OK',        'Permission Updated');
    define('PERM_FAIL', 'Permission already exists');

    // If the permission name isn't changing
    if($_POST['permission'] === $_POST['permission_old']) {

        // Meta update query
        $meta = 'UPDATE `ds_permissions` SET ' .
                '`description` = ? WHERE `permission` = ?';

        // Meta update data
        $meta_data = [
            $_POST['description'],
            $_POST['permission']
        ];

        // On query success
        if($ds->query($meta, $meta_data)) {

            // Log the event
            $ds->logEvent("Permission {$_POST['permission']} Updated", PERMISSION_UPDATED);

            // Successful response
            die($ds->APIResponse('OK', 0, OK));

        }

        // On query failure
        else {

            die($ds->APIResponse());

        }

    }

    // If the permission name is changing
    else {

        // If the permission already exists
        if(array_key_exists($_POST['permission'], $ds->getPermissions())) {

            // Failed response
            die($ds->APIResponse('PERM_FAIL', 3, PERM_FAIL));

        }

        // Permission doesn't exist, update it
        else {

            // Meta update query
            $meta = 'UPDATE `ds_permissions` SET `permission` = ?, ' .
                    '`description` = ? WHERE `permission` = ?';

            // Update any set groups as well
            $group = 'UPDATE `ds_group_data` SET `permission` = ? ' .
                     'WHERE `permission` = ?';

            // Meta update data
            $meta_data = [
                $_POST['permission'],
                $_POST['description'],
                $_POST['permission_old']
            ];

            // Group update data
            $group_data = [
                $_POST['permission'],
                $_POST['permission_old']
            ];


            // On query success
            if(
                $ds->query($meta, $meta_data) &&
                $ds->query($group, $group_data)
            ) {

                // Log the event
                $ds->logEvent("Permission {$_POST['permission']} Updated", PERMISSION_UPDATED);

                // Successful response
                die($ds->APIResponse('OK', 0, OK));

            }

            // On query failure
            else {

                die($ds->APIResponse());

            }

        }

    }

}

// On invalid request
else {

    die($ds->APIResponse());

}