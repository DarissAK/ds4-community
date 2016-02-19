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

// If the user has the proper permissions and session
if($ds->checkPermission('ds_admin_permission')) {

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
        $perm_body = '';

        // Get all possible permissions
        $permissions = $ds->getPermissions();

        // Loop through all of the permissions and build the body
        foreach($permissions as $permission => $data) {
            $perm_body .= '<div class="form-group">';
            $perm_body .= "<label for='$permission'>{$data['description']}</label>";
            $perm_body .= "<select id='$permission' name='$permission' class='form-control'>";
            if(array_key_exists($permission, $groups[$ds->url[4]]['permissions'])) {
                $perm_body .= '<option value="1">Yes</option><option value="0">No</option>';
            }
            else {
                $perm_body .= '<option value="0">No</option><option value="1">Yes</option>';
            }
            $perm_body .= '</select></div>';
        }

        // Update the template
        $template =
            str_replace('{{group}}', $ds->url[4], $template);
        $template =
            str_replace('{{description}}', $groups[$ds->url[4]]['description'], $template);
        $template =
            str_replace('{{body}}', $perm_body, $template);

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
        foreach($groups as $group) {
            $tbody .= "<tr><td>{$group['group']}</td>";
            $tbody .= "<td>{$group['description']}</td></tr>";
        }

        // Update the template
        $template = str_replace('{{tbody}}', $tbody, $template);

    }

    // Render the template
    echo $template;

}

// Invalid page permissions
else {

    echo 'Permission denied';

}