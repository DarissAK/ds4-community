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

// Include dependencies
require_once $_SERVER['DOCUMENT_ROOT'] . '/server/lib/ds.class.php';

// Check for valid request
$ds->checkRequest('ds_admin_permission', ['id', 'name']);

// Formatted name
$name = htmlentities($_POST['name']);

// API responses
define('OK', "Permission $name Deleted");

// Query for deleting the permission
$query = 'DELETE FROM `ds_permissions` WHERE `permission_id` = ?';

// On query failure
if(!$ds->query($query, $_POST['id']))
    die($ds->APIResponse());

// Log the event
$ds->logEvent(OK, PERMISSION_DELETED);

// OK response
die($ds->APIResponse('OK', 0, OK));