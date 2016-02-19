<?php
// +-------------------------------------------------------------------------+
// |  Administrator module - Permission list                                 |
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

    // Get the array of possible permissions
    $permissions = $ds->getPermissions();

    // Display edit permission page
    if(
        isset($ds->url[3]) &&
        $ds->url[3] === 'edit' &&
        isset($ds->url[4]) &&
        array_key_exists($ds->url[4], $permissions)
    ) {

        // Template file location
        $file = '/modules/administrator/templates/permission_edit.html';

        // Load the template
        $template = $ds->loadTemplate($file);

        // Get the permission and the description
        $permission  = $permissions[$ds->url[4]]['permission'];
        $description = $permissions[$ds->url[4]]['description'];

        // Update the template
        $template = str_replace('{{permission}}', $permission, $template);
        $template = str_replace('{{description}}', $description, $template);

    }

    // Display permission list
    else {

        // Template file location
        $file = '/modules/administrator/templates/permission_list.html';

        // Load the template
        $template = $ds->loadTemplate($file);

        // String to hold the table body value
        $tbody = '';

        // Create the table body for each permission
        foreach($permissions as $permission) {

            $tbody .= "<tr><td>{$permission['permission']}</td>";
            $tbody .= "<td>{$permission['description']}</td></tr>";

        }

        // Update the template
        $template = str_replace('{{tbody}}', $tbody, $template);

    }

    // Render the template
    echo $template;

}

// Invalid page permissions
else {

    echo 'Permission Denied';

}