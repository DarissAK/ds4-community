<?php
// +-------------------------------------------------------------------------+
// |  Script for deleting user accounts                                      |
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
    !$ds->checkPermission('ds_admin_user') ||
    !isset($_POST['user_id']) ||
    !isset($_POST['username'])
)
    die($ds->APIResponse());

// API Responses
define('OK', 'User deleted successfully');

// Query for deleting users
$query = 'DELETE FROM `ds_users` WHERE `user_id` = ?';

// On query failure
if(!$ds->query($query, $_POST['user_id']))
    die($ds->APIResponse());

// Log the event
$ds->logEvent('User Deleted', USER_DELETED, $_POST['username']);

// Send the response
die($ds->APIResponse('OK', 0, OK));