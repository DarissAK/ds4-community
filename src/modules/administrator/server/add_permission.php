<?php
// +-------------------------------------------------------------------------+
// |  Script for adding permissions                                          |
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
    isSet($_POST['ds_perm']) &&
    isSet($_POST['ds_perm_desc'])
) {

    // If the permission already exists
    if(array_key_exists($_POST['ds_perm'], $ds->getPermissions())) {

        // Send failed response
        die($ds->APIResponse('PERM_FAIL', 3, 'Permission already exists'));

    }

    // Permission doesn't exist
    else {

        // On add success
        if($ds->registerPermission($_POST['ds_perm'], $_POST['ds_perm_desc'])) {

            // Send the OK response
            die($ds->APIResponse('OK', 0, 'Permission added'));

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
