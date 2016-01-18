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
$_SESSION = array();

?>
<div class="ds-y-center ds-login">
    <div class="col-sm-4 col-sm-offset-4"><?php echo $cfg['system_login_header'] ?></div>
    <form target="_self">
        <div class="col-sm-4 col-sm-offset-4">
            <div></div>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                <input id="username" type="text" class="form-control" placeholder="Username">
            </div>
            <br />
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                <input id="password" type="password" class="form-control" placeholder="Password">
            </div>
            <br />
            <input type="submit" class="btn btn-default" value="Login" />
        </div>
    </form>
    <div class="col-sm-4 col-sm-offset-4"><?php echo $cfg['system_footer'] ?></div>
</div>