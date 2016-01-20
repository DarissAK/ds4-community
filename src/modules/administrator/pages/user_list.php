<?php
// +-------------------------------------------------------------------------+
// |  Users module - User list and edit user pages                           |
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
if(
    $ds->checkSession() &&
    $ds->checkPermission('ds_admin_user')
) {

    // String containing the table body
    $tbody = '';

    if (
        isset($ds->url[3]) &&
        $ds->url[3] === 'edit' &&
        isset($ds->url[4]) &&
        $user = $ds->getUserAcct($ds->url[4])
    ) {

        // Load the template
        $template =
            $ds->loadTemplate('/modules/administrator/templates/user_list_edit.html');

        // Update template data
        $template =
            str_replace('%DS_USER%',
                $user['ds_user'], $template);
        $template =
            str_replace('%DS_USER_ADDED%',
                $ds->timestampSQL2Format($user['ds_user_added']), $template);
        $template =
            str_replace('%DS_USER_ADDED_BY%',
                $user['ds_user_added_by'], $template);
        $template =
            str_replace('%DS_USER_LAST_LOGIN_SUCCESS%',
                $ds->timestampSQL2Format($user['ds_user_last_login_success']), $template);
        $template =
            str_replace('%DS_USER_LAST_LOGIN_IP%',
                $user['ds_user_last_login_ip'], $template);

        // Get the user's status
        $ds_user_status = $user['ds_user_status']
            ? '<option value="1">Active</option><option value="0">Inactive</option>'
            : '<option value="0">Inactive</option><option value="1">Active</option>';
        $template = str_replace('%DS_USER_STATUS%', $ds_user_status, $template);

        // Get the groups and unset current group
        $groups = $ds->getPermissionGroups();
        unset($groups[$user['ds_user_group']]);

        // Set 1st option to current group
        $ds_user_group = "<option value='{$user['ds_user_group']}'>" .
            "{$user['ds_user_group']}</option>";

        // Create the group select options
        foreach ($groups as $k => $v) {
            $ds_user_group .= "<option value='$k'>$k</option>";
        }

        // Update the template
        $template = str_replace('%DS_USER_GROUP%', $ds_user_group, $template);

        // Get the user's administrator status
        $ds_user_administrator = $user['ds_user_administrator']
            ? '<div class="form-group"><label for="ds-user-administrator">Administrator ' .
            '(<i>overrides permission group</i>)</label>' .
            '<select id="ds-user-administrator" name="ds_user_administrator" ' .
            'class="form-control"><option value="1">Yes</option><option value="0">No</option>' .
            '</select></div>'
            : '<div class="form-group"><label for="ds-user-administrator">Administrator ' .
            '(<i>overrides permission group</i>)</label>' .
            '<select id="ds-user-administrator" name="ds_user_administrator" ' .
            'class="form-control"><option value="0">No</option><option value="1">Yes</option>' .
            '</select></div>';

        // Only administrators may set other administrators
        if ($ds->is_admin) {
            $template =
                str_replace('%DS_USER_ADMINISTRATOR%', $ds_user_administrator, $template);
        } // Get rid of the administrator input
        else {
            $template =
                str_replace('%DS_USER_ADMINISTRATOR%', '', $template);
        }

        // Render the template
        die($template);

    } elseif (
        isset($ds->url[3]) &&
        $ds->url[3] === 'inactive'
    ) {

        // Query for getting all of the inactive users
        $query = 'SELECT * FROM `ds_user` ' .
                 'WHERE `ds_user_status` = 0 ' .
                 'ORDER BY `ds_user`';

        // Execute the query and continue if success
        if ($users = $ds->query($query)) {

            // Loop through all users
            foreach ($users as $user) {

                // Format last login timestamp
                $inactive_date =
                    $ds->timestampSQL2Format($user['ds_user_inactive_timestamp']);

                $tbody .= '<tr>';
                $tbody .= "<td>{$user['ds_user']}</td>";
                $tbody .= "<td>$inactive_date</td>";
                $tbody .= '</tr>';
            }

            // Load the template
            $template =
                $ds->loadTemplate('/modules/administrator/templates/user_list_inactive.html');

            // Location of the inactive users list
            $view_href = $cfg['install_domain'] . '/administrator/users/list';

            // Add the inactive user href to the template
            $template =
                str_replace('%VIEW_HREF%', $view_href, $template);

            // Add the table body to the template, then display it
            die(str_replace('%TBODY%', $tbody, $template));

        } else {

            die('Error loading users');

        }

    } else {

        // Query for getting all of the active users
        $query = 'SELECT * FROM `ds_user` ' .
                 'WHERE `ds_user_status` = 1 ' .
                 'ORDER BY `ds_user`';

        // Execute the query and continue if success
        if ($users = $ds->query($query)) {

            foreach ($users as $user) {

                $last_login =
                    $ds->timestampSQL2Format($user['ds_user_last_login_success']);

                $tbody .= '<tr>';
                $tbody .= "<td>{$user['ds_user']}</td>";
                $tbody .= "<td>{$user['ds_user_group']}</td>";
                $tbody .= "<td>$last_login</td>";
                $tbody .= '</tr>';
            }

            // Load the template
            $template =
                $ds->loadTemplate('/modules/administrator/templates/user_list_active.html');

            // Location of the inactive users list
            $view_href = $cfg['install_domain'] . '/administrator/users/list/inactive';

            // Add the inactive user href to the template
            $template =
                str_replace('%VIEW_HREF%', $view_href, $template);

            // Add the table body to the template, then display it
            die(str_replace('%TBODY%', $tbody, $template));

        } else {

            die('Error loading users');

        }

    }

}

else {

    die('Permission denied');

}