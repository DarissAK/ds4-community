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

// Include dependencies
require_once $_SERVER['DOCUMENT_ROOT'] . '/server/fn_init.php';

// Check for valid request
$ds->checkRequest(
    'ds_admin_permission',
    ['id', 'name', 'old', 'description']
);

// Formatted name
$name = htmlentities($_POST['name']);

// API Responses
define('NAME_FAIL',   'Group already exists');
define('NAME_L_FAIL', 'Group name too short');
define('DESC_L_FAIL', 'Description too short');
define('OK',          "Group {$_POST['name']} Updated");

// Global Settings
define('MIN_GROUP_LENGTH', 2);
define('MIN_DESC_LENGTH',  4);

// If the name is changing
if(strcasecmp($_POST['name'], $_POST['old'])) {

    // Test query
    $test = 'SELECT * FROM `ds_group_meta` WHERE `name` = ?';

    // Name is already in use
    if(is_array($ds->query($test, $_POST['name'])))
        die($ds->APIResponse('NAME_FAIL', 3, NAME_FAIL));

    // Name is too short
    if(strlen($_POST['name']) < MIN_GROUP_LENGTH)
        die($ds->APIResponse('NAME_L_FAIL', 3, NAME_L_FAIL));

}

// Description is too short
if(strlen($_POST['description']) < MIN_DESC_LENGTH)
    die($ds->APIResponse('DESC_L_FAIL', 3, DESC_L_FAIL));

// Query for updating metadata
$query = 'UPDATE `ds_group_meta` SET `name` = ?, ' .
         '`description` = ? WHERE `group_id` = ?';

// New metadata
$data = [
    $_POST['name'],
    $_POST['description'],
    $_POST['id']
];

// Update the metadata
if(!$ds->query($query, $data))
    die($ds->APIResponse());

// Clear old permissions
$query = 'DELETE FROM `ds_group_data` WHERE `group_id` = ?';
if(!$ds->query($query, $_POST['id']))
    die($ds->APIResponse());

// If there are permissions to add
if(
    isset($_POST['permissions']) &&
    is_array($_POST['permissions']) &&
    !empty($_POST['permissions'])
) {

    // Begin query for adding data
    $query = 'INSERT INTO `ds_group_data` ' .
             '(`group_id`, `permission_id`) VALUES ';

    // Group data
    $data = [];

    // Loop through all of the given permissions
    foreach($_POST['permissions'] as $permission) {

        // Get the permission ID
        $group_id      = $_POST['id'];
        $permission_id = explode('_', $permission['name'])[1];

        // Update the query
        $query .= '(?, ?),';

        // Add the data
        array_push($data, $group_id);
        array_push($data, $permission_id);

    }

    // Trim off the trailing comma
    $query = rtrim($query, ',');

    // Update the data
    if(!$ds->query($query, $data))
        die($ds->APIResponse());

}

// Log the event
$ds->logEvent(OK, GROUP_UPDATED);

// OK response
die($ds->APIResponse('OK', 0, OK, $name));