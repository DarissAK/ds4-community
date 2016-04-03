<?php
// +-------------------------------------------------------------------------+
// |  Administrator module - Group list                                      |
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

// Invalid permissions or session
$ds->validatePermission('ds_admin_permission');

// Load the groups
$groups = $ds->getPermissionGroups();

// Display edit group page
if(
    isset($ds->url[3]) &&
    $ds->url[3] === 'edit' &&
    isset($ds->url[4]) &&
    array_key_exists($ds->url[4], $groups)
) {

    // Template file
    $file = '/modules/administrator/templates/group_edit.html';

    // Load the template
    $template = $ds->loadTemplate($file);

    // Permissions body string
    $body = '';

    // Get all possible permissions
    $permissions = $ds->getPermissions();

    // Loop through all of the permissions and build the body
    foreach($permissions as $id => $permission) {

        // Set the checked state
        $checked = array_key_exists($permission['name'],
            $groups[$ds->url[4]]['permissions'])
            ? 'checked '
            : '';

        // Update the body
        $body .= '<div class="checkbox permission-check">';
        $body .= '<label>';
        $body .= "<input type='checkbox' name='perm_$id' $checked />";
        $body .= '<div>' . htmlentities($permission['description']) . '</div>';
        $body .= '</label>';
        $body .= '</div>';

    }

    // Set the current group
    $group = $groups[$ds->url[4]];

    // Update the template
    $template =
        str_replace('{{id}}', $group['group_id'], $template);
    $template =
        str_replace('{{name}}', htmlentities($group['name']), $template);
    $template =
        str_replace('{{description}}', htmlentities($group['description']), $template);
    $template =
        str_replace('{{body}}', $body, $template);

}

// Display group list
else {

    // Template file
    $file = '/modules/administrator/templates/group_list.html';

    // Load the template
    $template = $ds->loadTemplate($file);

    // Table body string
    $tbody = '';

    // Create the table body
    foreach($groups as $group_data) {

        // Row Data
        $id          = $group_data['group_id'];
        $name        = htmlentities($group_data['name']);
        $description = htmlentities($group_data['description']);

        // Create the row
        $tbody .= "<tr data-id='$id'><td>$name</td>";
        $tbody .= "<td>$description</td></tr>";

    }

    // Update the template
    $template = str_replace('{{tbody}}', $tbody, $template);

}

// Render the template
echo $template;
