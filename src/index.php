<?php
// +-------------------------------------------------------------------------+
// |  Dynamic Suite Main File                                                |
// |  All modules are rendered through this script                           |
// |                                                                         |
// |  To make changes to things such as head tags, edit the template found   |
// |  at /templates/page_start.php                                           |
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

// Include the dsInstance class (creates as $ds)
require_once($_SERVER['DOCUMENT_ROOT'] . '/server/fn_init.php');

// Validate the view
if($ds->validateView()) {

    // Render Page Start
    echo $ds->html_page_start;

    // If the current location is login, include the login page
    if($ds->clean_url[0] === 'login') {
        require_once($_SERVER['DOCUMENT_ROOT'] . '/pages/login.php');

        // Render Page End
        echo $ds->html_page_end;
    }

    // If the current location is error, include the error page
    elseif($ds->clean_url[0] === 'error') {
        require_once($_SERVER['DOCUMENT_ROOT'] . '/pages/error.php');
        echo '</body></html>';
    }

    // Render navbar and header
    else {

        // Render the navigation bar
        echo $ds->html_nav;

        // Render the body content
        echo "<div class='ds-body'>";

        // Render the HTML header ribbon
        echo "<div>$ds->html_header</div>";

        // Render the HTML tabs
        echo "<div>$ds->html_tabs</div>";

        // Render the module content
        echo "<div>";

        // If the current view content exists, include it
        if(file_exists($_SERVER['DOCUMENT_ROOT'] . $ds->getView())) {
            require_once($_SERVER['DOCUMENT_ROOT'] . $ds->getView());
        }

        // Content not found
        else {
            echo '<h3>Content not found</h3>';
            echo '<h5>Missing File: ' . $ds->getView()  . '</h5>';
        }

        // End Module content
        echo "</div>";

        // End body content
        echo "</div>";

        // Render Page End
        echo $ds->html_page_end;

    }

}