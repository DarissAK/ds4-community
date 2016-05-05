<?php
// +-------------------------------------------------------------------------+
// |  Administrator module - User list and edit user pages                   |
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
$ds->validatePermission('ds_admin_user');

// String containing the table body
$tbody = '';

// If the page is set to edit, and the user given in the URL exists
if(
    isset($ds->url[3]) &&
    $ds->url[3] === 'edit' &&
    isset($ds->url[4]) &&
    $user = $ds->getUserAcct($ds->url[4])
) {

    // Template file location
    $file = '/modules/administrator/templates/users/edit.html';

    // Load the template
    $template = $ds->loadTemplate($file);

    // Data to replace in template
    $user_id            = $user['user_id'];
    $username           = htmlentities($user['username']);
    $added              = $ds->timestampSQL2Format($user['added']);
    $added_by           = htmlentities($user['added_by']);
    $last_login_success = $ds->timestampSQL2Format($user['last_login_success']);
    $ip                 = $user['last_login_ip'];

    // Update template data
    $template = str_replace('{{id}}', $user_id, $template);
    $template = str_replace('{{username}}', $username, $template);
    $template = str_replace('{{added}}', $added, $template);
    $template = str_replace('{{added_by}}', $added_by, $template);
    $template = str_replace('{{last_login_success}}', $last_login_success, $template);
    $template = str_replace('{{last_login_ip}}', $ip, $template);

    // Get the user's status
    $status = $user['status']
        ? '<option value="1">Active</option><option value="0">Inactive</option>'
        : '<option value="0">Inactive</option><option value="1">Active</option>';
    $template = str_replace('{{status}}', $status, $template);

    // Get the groups and unset current group (if exists)
    $groups = $ds->getPermissionGroups();

    // Get the current user's group
    $current_group = !is_null($user['group'])
        ? htmlentities($groups[$user['group']]['name'])
        : '';

    if(array_key_exists($user['group'], $groups))
        unset($groups[$user['group']]);

    // Set 1st option to current group
    $options = "<option value='{$user['group']}'>$current_group</option>";

    // Create the group select options
    foreach($groups as $group => $data) {
        $options .= "<option value='$group'>";
        $options .= htmlentities($data['name']) . "</option>";
    }

    // Update the template
    $template = str_replace('{{group}}', $options, $template);

    // Begin administrator select area
    $administrator =
        '<div class="form-group"><label for="administrator">Administrator ' .
        '(<i>overrides permission group</i>)</label>' .
        '<select id="administrator-select" class="form-control">';

    // Add options to administrator select area
    $administrator = $user['administrator']
        ? $administrator .=
            '<option value="1">Yes</option><option value="0">No</option>' .
            '</select></div>'
        : $administrator .=
            '<option value="0">No</option><option value="1">Yes</option>' .
            '</select></div>';

    // Only administrators may set other administrators
    $template = $ds->is_admin
        ? str_replace('{{administrator}}', $administrator, $template)
        : str_replace('{{administrator}}', '', $template);

    // Render the template
    echo $template;

}

// If the view is for inactive users
elseif (
    isset($ds->url[3]) &&
    $ds->url[3] === 'inactive'
) {

    // Template file to load
    $file = '/modules/administrator/templates/users/list_inactive.html';

    // Load the template
    $template = $ds->loadTemplate($file);

    // Get inactive users
    $users = $ds->getUsers(false);

    // Loop through all inactive users
    foreach ($users as $user) {

        // Format inactive timestamp
        $inactive_date =
            $ds->timestampSQL2Format($user['inactive_time']);

        // Append rows to table body
        $tbody .= '<tr>';
        $tbody .= "<td data-user-id='{$user['user_id']}'>";
        $tbody .= htmlentities($user['username']) . "</td>";
        $tbody .= "<td>$inactive_date</td>";
        $tbody .= '</tr>';

    }

    // Location of the inactive users list
    $view_href = "{$ds->domain}/administrator/users/list";

    // Add the inactive user href to the template
    $template = str_replace('{{view_href}}', $view_href, $template);

    // Add the table body to the template, then render it
    echo str_replace('{{tbody}}', $tbody, $template);

}

// Default view, show active user list
else {

    // Template file to load
    $file = '/modules/administrator/templates/users/list_active.html';

    // Load the template
    $template =$ds->loadTemplate($file);

    // Get all of the active users
    $users = $ds->getUsers();

    // Loop through all active users
    foreach($users as $user) {

        // Format last login timestamp
        $last_login =
            $ds->timestampSQL2Format($user['last_login_success']);

        // Append rows to table body
        $tbody .= "<tr data-id='{$user['user_id']}'>";
        $tbody .= "<td>" . htmlentities($user['username']) . "</td>";
        $tbody .= "<td>" . htmlentities($user['group_name']) . "</td>";
        $tbody .= "<td>$last_login</td>";
        $tbody .= '</tr>';

    }

    // Location of the inactive users list
    $view_href = "{$ds->domain}/administrator/users/list/inactive";

    // Add the inactive user href to the template
    $template = str_replace('{{view_href}}', $view_href, $template);

    // Add the table body to the template, then display it
    echo str_replace('{{tbody}}', $tbody, $template);

}