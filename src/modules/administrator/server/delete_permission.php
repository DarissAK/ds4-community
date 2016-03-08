<?php
// +-------------------------------------------------------------------------+
// |  Script for deleting permissions                                        |
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
require_once $_SERVER['DOCUMENT_ROOT'] . '/server/fn_init.php';

// On valid request
if(
    !$ds->checkPermission('ds_admin_permission') ||
    !isset($_POST['permission_old']) ||
    !isset($_POST['permission_id'])
)
    die($ds->APIResponse());

// API Responses
define('OK', 'Permission deleted successfully');

// Query for deleting the permission
$query = 'DELETE FROM `ds_permissions` WHERE `permission_id` = ?';

// On query failure
if(!$ds->query($query, $_POST['permission_id']))
    die($ds->APIResponse());

// Old name for logs
$old = $_POST['permission_old'];

// Log the event
$ds->logEvent("Permission $old Deleted", PERMISSION_DELETED);

// OK Response
die($ds->APIResponse('OK', 0, OK));