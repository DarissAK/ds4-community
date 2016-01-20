<?php
// +-------------------------------------------------------------------------+
// |  Fatal error page                                                       |
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

// Destroy any active dynamic suite instances
if(isset($ds)) unset($ds);

?>
<html>
<head>
    <title>Error!</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
</head>
<body>
<div class="ds-error">
    <h1>A fatal error has occurred</h1>
    <h3>Please note the date and time and the steps that led up to the error
        and notify your system administrator at
        <a target="_self" href="mailto:<?php echo $cfg['errors_mailto'] ?>?Subject=DS%20Error">
        <?php echo $cfg['errors_mailto'] ?></a>
    </h3>
    <h3>Current Date & Time: <?php echo date($cfg['timestamp_format']) ?></h3>
    <h3><a target="_self" href="<?php echo $cfg['install_domain'] ?>">Click here to continue...</a></h3>
</div>