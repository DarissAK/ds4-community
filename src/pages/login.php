<?php
// +-------------------------------------------------------------------------+
// |  Dynamic Suite Login Page                                               |
// |  Also serves as a logout script                                         |
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

// Log out any users that are still logged in
$_SESSION = [];

// Load the template
$template = file_get_contents($ds->dir . '/templates/login.html');

// Update the template
$template = str_replace('{{header}}', $ds->cfg['system_login_header'], $template);
$template = str_replace('{{footer}}', $ds->cfg['system_footer'], $template);

// Render the template
echo $template;