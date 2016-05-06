<?php
// +-------------------------------------------------------------------------+
// |  Task Scheduler module - Scheduled tasks list                           |
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
$ds->validatePermission('ds_task_scheduler');

// Temporary areas
$tmp_dir = $_SERVER['DOCUMENT_ROOT'] . '/modules/task-scheduler/tmp';
$tmp_file = 'cron.tmp';

// Check permissions
if(!is_writable($tmp_dir))
    die('Module /tmp directory not writeable, module cannot load');
if(file_exists("$tmp_dir/$tmp_file") && !is_writable($tmp_file))
    die('cron.tmp not writeable, module cannot load');

// Template file to load
$file = '/modules/task-scheduler/templates/tasks.html';

// Load the template file
$template = $ds->loadTemplate($file);

// Render the template
echo $template;