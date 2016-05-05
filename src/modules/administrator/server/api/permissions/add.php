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

// Include dependencies
require_once $_SERVER['DOCUMENT_ROOT'] . '/server/fn_init.php';

// Check for valid request
$ds->checkRequest('ds_admin_permission', ['name', 'description']);

// Format name
$name = htmlentities($_POST['name']);

// API Responses
define('NAME_FAIL',   'Permission already exists');
define('NAME_L_FAIL', 'Permission name too short');
define('DESC_L_FAIL', 'Description too short');
define('OK',          "Permission $name Added");

// Global Settings
define('MIN_NAME_LENGTH', 2);
define('MIN_DESC_LENGTH', 4);

// Data to add
$data = [
    $_POST['name'],
    $_POST['description']
];

// If the permission already exists
foreach($ds->getPermissions() as $permission) {
    if(!strcasecmp($permission['name'], $_POST['name']))
        die($ds->APIResponse('NAME_FAIL', 3, NAME_FAIL));
}

// If the name is too short
if(strlen($_POST['name']) < MIN_NAME_LENGTH)
    die($ds->APIResponse('NAME_L_FAIL', 3, NAME_L_FAIL));

// If the description is too short
if(strlen($_POST['description']) < MIN_DESC_LENGTH)
    die($ds->APIResponse('DESC_L_FAIL', 3, DESC_L_FAIL));

// Query for adding the permission
$query = 'INSERT INTO `ds_permissions` ' .
         '(`name`, `description`) VALUES (?, ?)';

// On query failure
if(!$ds->query($query, $data))
    die($ds->APIResponse());

// New permission ID
$id = $ds->db_conn->lastInsertId();

// Log the event
$ds->logEvent(OK, PERMISSION_ADDED);

// Response data
$data = [
    'id'          => $id,
    'name'        => $name,
    'description' => htmlentities($_POST['description'])
];

// OK Response
die($ds->APIResponse('OK', 0, OK, $data));