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
    $ds->checkSession() &&
    $ds->checkPermission('ds_admin_permission') &&
    isset($_POST['ds_group']) &&
    isset($_POST['ds_group_old']) &&
    isset($_POST['ds_group_desc']) &&
    isset($_POST['ds_group_perms'])
) {

    // If the group name isn't changing
    if($_POST['ds_group'] === $_POST['ds_group_old']) {

        // Update meta query
        $meta = 'UPDATE `ds_perm_groups` SET ' .
                '`ds_perm_group_desc` = ? WHERE `ds_perm_group` = ?';

        // Query meta data
        $meta_data = [
            $_POST['ds_group_desc'],
            $_POST['ds_group']
        ];

        // Delete old perms query
        $delete = 'DELETE FROM `ds_perm` WHERE `ds_group` = ?';

        // Query perm data
        $perm_data = [];

        // Insert new perms query
        $update = 'INSERT INTO `ds_perm` (`ds_group`, `ds_perm`) VALUES ';

        // Increment
        $i = 0;

        foreach($_POST['ds_group_perms'] as $k => $v) {

            // Skip zero values
            if(!$v['value']) continue;

            // Append string and add values
            $update .= '(?,?),';
            array_push($perm_data, $_POST['ds_group']);
            array_push($perm_data, $v['name']);

            // Increment
            $i++;

        }

        // Remove trailing comma
        $update = rtrim($update, ',');

        // Run queries
        $ds->query($meta, $meta_data);
        $ds->query($delete, $_POST['ds_group']);
        if($i) $ds->query($update, $perm_data);

        // On query success
        if(!$ds->db_error) {

            // Log the event
            $ds->logEvent('Group ' . $_POST['ds_group'] . ' Updated', 9);

            // Successful response
            die($ds->APIResponse('OK', 0, 'Group Updated'));

        }

        // On query failure
        else {

            die($ds->APIResponse());

        }

    }

    // If the group name is changing
    else {

        // If the group already exists
        if(array_key_exists($_POST['ds_group'], $ds->getPermissionGroups())) {

            // Failed response
            die($ds->APIResponse('PERM_FAIL', 3, 'Group already exists'));

        }

        // Group doesn't exist, update it
        else {

            // Update meta query
            $meta = 'UPDATE `ds_perm_groups` SET `ds_perm_group` = ?, ' .
                    '`ds_perm_group_desc` = ? WHERE `ds_perm_group` = ?';

            // Query meta data
            $meta_data = [
                $_POST['ds_group'],
                $_POST['ds_group_desc'],
                $_POST['ds_group_old']
            ];

            // Delete old perms query
            $delete = 'DELETE FROM `ds_perm` WHERE `ds_group` = ?';

            // Query perm data
            $perm_data = [];

            // Insert new perms query
            $update = 'INSERT INTO `ds_perm` (`ds_group`, `ds_perm`) VALUES ';

            // Increment
            $i = 0;

            foreach($_POST['ds_group_perms'] as $k => $v) {

                // Skip zero values
                if(!$v['value']) continue;

                // Append string and add values
                $update .= '(?,?),';
                array_push($perm_data, $_POST['ds_group']);
                array_push($perm_data, $v['name']);

                // Increment
                $i++;

            }

            // Remove trailing comma
            $update = rtrim($update, ',');

            // Run queries
            $ds->query($meta, $meta_data);
            $ds->query($delete, $_POST['ds_group']);
            if($i) $ds->query($update, $perm_data);

            // On query success
            if(!$ds->db_error) {

                // Log the event
                $ds->logEvent('Group ' . $_POST['ds_group'] . ' Updated', 9);

                // Successful response
                die($ds->APIResponse('OK', 0, 'Group Updated'));

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