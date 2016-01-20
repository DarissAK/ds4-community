<?php
// +-------------------------------------------------------------------------+
// |  Script for adding permission groups                                    |
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
    isset($_POST['ds_group_desc'])
) {

    // If the group already exists
    if(array_key_exists($_POST['ds_group'], $ds->getPermissionGroups())) {

        // Send failed response
        die($ds->APIResponse('GROUP_FAIL', 3, 'Group already exists'));

    }

    // Group doesn't exist
    else {

        // On add success
        if($ds->registerGroup($_POST['ds_group'], $_POST['ds_group_desc'])) {

            // Send the OK response
            die($ds->APIResponse('OK', 0, 'Group added'));

        }

        // On query failure
        else {

            die($ds->APIResponse());

        }

    }

}

// On invalid request
else {

    die($ds->APIResponse());

}
