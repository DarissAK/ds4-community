<?php
// +-------------------------------------------------------------------------+
// |  Dynamic Suite Login Script                                             |
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

// Include and create a new Dynamic Suite Instance
require_once $_SERVER['DOCUMENT_ROOT'] . '/server/lib/ds.class.php';

// Login API Responses
define('MANUAL_LOCKOUT_FAIL', $ds->cfg['lock_out_message']);
define('ACCT_FAIL',           'Invalid username or password');
define('ACTIVE_FAIL',         'Account Inactive');
define('LOCKOUT_FAIL',        'Too many login attempts');

// Invalid request
if(!isset($_POST['username']) || !isset($_POST['password']))
    die($ds->APIResponse());

// Response if the given username or password isn't valid
$bad_acct = $ds->APIResponse('ACCT_FAIL', 3, ACCT_FAIL);

// If the username or password fields are empty
if(empty($_POST['username']) || empty($_POST['password']))
    die($bad_acct);

// Query for getting the user account
$query = 'SELECT * FROM `ds_users` WHERE `username` = ?';

// On query failure
if(!$account = $ds->query($query, $_POST['username']))
    die($ds->APIResponse());

// If no account was found for the given username
if(!is_array($account) || count($account) > 1)
    die($bad_acct);

// Set the current account
$account = $account[0];

// Block login attempt if a manual lockout is in effect
// Administrators bypass lockout
// See Dynamic Suite Configuration documentation for more information
if($ds->cfg['manual_lockout'] && !$account['administrator'])
    die($ds->APIResponse('MANUAL_LOCKOUT_FAIL', 3, MANUAL_LOCKOUT_FAIL));

// Update login metadata and increment login attempts
$ds->updateLoginMetadata($account['user_id']);

// If the user's account is inactive, block login
if(!$account['status'])
    die($ds->APIResponse('ACTIVE_FAIL', 3, ACTIVE_FAIL));

// Get the timestamp value of the last login attempt
$last_attempt = strtotime($account['last_login_attempt']);

// If the user has greater than or equal to the allowed login attempts
// and hasn't waited out the login period
if(
    $account['login_attempts'] >= $ds->cfg['login_attempts'] &&
    time() - $last_attempt <= $ds->cfg['login_timeout']
)
    die($ds->APIResponse('LOCKOUT_FAIL', 3, LOCKOUT_FAIL));

// Run the login attempt
$login = $ds->attemptLogin(
    $account['username'],
    $account['user_id'],
    $account['password'],
    $_POST['password']
);

// Return the login status (success or fail)
!$login ? die($bad_acct) : die($login);