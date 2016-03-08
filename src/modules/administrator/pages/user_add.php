<?php
// +-------------------------------------------------------------------------+
// |  Administrator module - Add user page                                   |
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

// If permission or session is not valid
$ds->validatePermission('ds_admin_user');

// Get permission groups
$groups = $ds->getPermissionGroups();

// Option string
$options = '<option></option>';

// Build the option string for each permission group
foreach($groups as $group => $data) {
    $options .= "<option value='$group'>{$data['name']}</option>";
}

// Template file to load
$file = '/modules/administrator/templates/user_add.html';

// Load the template
$template = $ds->loadTemplate($file);

// Update the template
$template = str_replace('{{group}}', $options, $template);

// Render the template
echo $template;