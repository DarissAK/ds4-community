<?php
// +-------------------------------------------------------------------------+
// |  Script for updating permission groups                                  |
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
    isset($_POST['group']) &&
    isset($_POST['group_old']) &&
    isset($_POST['description']) &&
    isset($_POST['permissions'])
) {

    // API Responses
    define('OK',        'Group Updated');
    define('PERM_FAIL', 'Group already exists');

    // If the group name isn't changing
    if($_POST['group'] === $_POST['group_old']) {

        // Update meta query
        $meta = 'UPDATE `ds_group_meta` SET ' .
                '`description` = ? WHERE `group` = ?';

        // Query meta data
        $meta_data = [
            $_POST['description'],
            $_POST['group']
        ];

        // Delete old perms query
        $delete = 'DELETE FROM `ds_group_data` WHERE `group` = ?';

        // Query perm data
        $perm_data = [];

        // Insert new perms query
        $update = 'INSERT INTO `ds_group_data` (`group`, `permission`) VALUES ';

        // Increment
        $i = 0;

        foreach($_POST['permissions'] as $data) {

            // Skip zero values
            if(!$data['value']) continue;

            // Append string and add values
            $update .= '(?,?),';
            array_push($perm_data, $_POST['group']);
            array_push($perm_data, $data['name']);

            // Increment
            $i++;

        }

        // Remove trailing comma
        $update = rtrim($update, ',');

        // Run queries
        $ds->query($meta, $meta_data);
        $ds->query($delete, $_POST['group']);
        if($i) $ds->query($update, $perm_data);

        // On query success
        if(!$ds->db_error) {

            // Log the event
            $ds->logEvent('Group ' . $_POST['group'] . ' Updated', GROUP_UPDATED);

            // Successful response
            die($ds->APIResponse('OK', 0, OK));

        }

        // On query failure
        else {

            die($ds->APIResponse());

        }

    }

    // If the group name is changing
    else {

        // If the group already exists
        if(array_key_exists($_POST['group'], $ds->getPermissionGroups())) {

            // Failed response
            die($ds->APIResponse('PERM_FAIL', 3, PERM_FAIL));

        }

        // Group doesn't exist, update it
        else {

            // Update meta query
            $meta = 'UPDATE `ds_group_meta` SET `group` = ?, ' .
                    '`description` = ? WHERE `group` = ?';

            // Query meta data
            $meta_data = [
                $_POST['group'],
                $_POST['description'],
                $_POST['group_old']
            ];

            // Delete old perms query
            $delete = 'DELETE FROM `ds_group_data` WHERE `group` = ?';

            // Query perm data
            $perm_data = [];

            // Insert new perms query
            $update = 'INSERT INTO `ds_group_data` (`group`, `permission`) VALUES ';

            // Increment
            $i = 0;

            foreach($_POST['permissions'] as $data) {

                // Skip zero values
                if(!$data['value']) continue;

                // Append string and add values
                $update .= '(?,?),';
                array_push($perm_data, $_POST['group']);
                array_push($perm_data, $data['name']);

                // Increment
                $i++;

            }

            // Remove trailing comma
            $update = rtrim($update, ',');

            // Run queries
            $ds->query($meta, $meta_data);
            $ds->query($delete, $_POST['group_old']);
            if($i) $ds->query($update, $perm_data);

            // On query success
            if(!$ds->db_error) {

                // Log the event
                $ds->logEvent('Group ' . $_POST['group'] . ' Updated', GROUP_UPDATED);

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