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
// |  NOTICE: All information contained herein is, and remains the property  |
// |  of Simplusoft Corporation and its suppliers, if any. The intellectual  |
// |  and technical concepts contained herein are proprietary to Simplusoft  |
// |  Corporation and its suppliers and are protected by trade secret or     |
// |  copyright law. Dissemination of this information or reproduction of    |
// |  this material is strictly forbidden unless prior written permission    |
// |  is obtained.                                                           |
// +-------------------------------------------------------------------------+

// Begin debug time
$debug_start = microtime();

// Include the ds class (creates as $ds)
require_once $_SERVER['DOCUMENT_ROOT'] . '/server/lib/ds.class.php';

// Validate the view
if($ds->validateView()) {

    // Render Page Start
    echo $ds->html_page_start;

    // If the current location is login, include the login page
    if($ds->url[0] === 'login') {
        require_once $ds->dir . '/pages/login.php';
        echo $ds->html_page_end;
    }

    // If the current location is error, include the error page
    elseif($ds->url[0] === 'error') {
        require_once $ds->dir . '/pages/error.php';
    }

    // Render navbar and header
    else {

        // Get the page timeout length
        $timeout = $ds->getTimeout();

        // Render the navigation bar
        echo $ds->html_nav;

        // Render the body content
        echo "<div id='ds-body-main' data-timeout='$timeout'>";

        // Render the HTML header ribbon
        echo "<div id='ds-body-header'>$ds->html_header</div>";

        // Render the HTML tabs (if any)
        if(!empty($ds->html_tabs))
            echo "<div id='ds-body-tabs'>$ds->html_tabs</div>";

        // Render the module content
        echo "<div id='ds-body-content'>";

        // If the current view content exists, include it
        if(file_exists($ds->dir . $ds->getView())) {

            // Look for included header files
            if(
                isset($ds->module['include']) &&
                is_array($ds->module['include'])
            ) {

                // Loop through all available header files
                foreach($ds->module['include'] as $file) {

                    // If the header file exists, include it
                    if(file_exists($ds->dir . $file)) {
                        /** @noinspection PhpIncludeInspection */
                        require_once $ds->dir . $file;

                    }

                }

            }

            // Load the current view
            require_once $ds->dir . $ds->getView();

        }

        // Content not found
        else {

            echo '<h3>Content not found</h3>';
            echo '<h5>Missing File: ' . $ds->getView() . '</h5>';

        }

        // End Module content
        echo "</div>";

        // End body content
        echo "</div>";

        // If debug is turned on
        if($cfg['debug']) {

            // Get end time
            $debug_time = round((microtime() - $debug_start) * 1000, 4);

            // Insert debug time
            echo "<div class='ds-debug'>";
            echo "<div>$debug_time</div>";
            echo "</div>";

        }

        // Render Page End
        echo $ds->html_page_end;

    }

}