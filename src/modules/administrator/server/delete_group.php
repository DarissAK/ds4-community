<?php
// +-------------------------------------------------------------------------+
// |  Script for deleting groups                                             |
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

// On invalid request
if(
    !$ds->checkPermission('ds_admin_permission') ||
    !isset($_POST['group_id']) ||
    !isset($_POST['group'])
)
    die($ds->APIResponse());

// API Responses
define('OK', 'Group deleted successfully');

// Query for removing a group
$query = 'DELETE FROM `ds_group_meta` WHERE `group_id` = ?';

// On query failure
if(!$ds->query($query, $_POST['group_id']))
    die($ds->APIResponse());

// Log the event
$ds->logEvent("Group {$_POST['group']} Deleted", GROUP_DELETED);

// OK Response
die($ds->APIResponse('OK', 0, OK));