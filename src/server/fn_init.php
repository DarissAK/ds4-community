<?php
// +-------------------------------------------------------------------------+
// |  Dynamic Suite Main Class - Creates a Dynamic Suite Instance            |
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

// Settings for CLI scripts
// Make sure the document root is set to your servers document root
$_SERVER['DOCUMENT_ROOT'] =
    isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '/path/to/ds4';
$_SERVER['REQUEST_URI'] =
    isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
$_SERVER['REMOTE_ADDR'] =
    isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';

// Include the configuration
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

// Include the optional database wrapper
require_once $_SERVER['DOCUMENT_ROOT'] . '/server/sdc.class.php';

// Include log definitions
require_once $_SERVER['DOCUMENT_ROOT'] . '/server/log_definitions.php';

class dsInstance
{

    // Primary Configuration Array
    public $cfg;

    // Database Connection Object
    public $db_conn;

    // Database Statement Object
    public $db_stmt;

    // Database Error State (if any)
    public $db_error = false;

    // Database Error Message (if any)
    public $db_error_info;

    // Clean URL for Apache rewrite
    public $url = [];

    // Page HTML base tag
    public $html_base = '';

    // Page HTML additional CSS resources
    public $html_css = '';

    // Page HTML additional JS resources
    public $html_js = '';

    // Page HTML title tag
    public $html_title = '';

    // Page HTML start
    public $html_page_start = '';

    // Page HTML end
    public $html_page_end = '';

    // Page HTML navigation bar
    public $html_nav = '';

    // Page HTML header ribbon
    public $html_header = '';

    // Page HTML tabs
    public $html_tabs = '';

    // Domain where this software is installed
    public $domain;

    // Directory on the server where
    // this software is installed
    public $dir;

    // Current user ID
    public $user_id;

    // Current session ID
    public $session;

    // Current user's account data
    public $account;

    // Current user administrator status
    public $is_admin;

    // Array of the current user's permissions
    public $permissions;

    // Array of all loaded module configurations
    // for the user. Modules with invalid permissions
    // will not be loaded
    public $modules = [];

    // Currently loaded (viewed) module configuration
    public $module;

    /**
     * dsInstance constructor.
     *
     * Generate a new Dynamic Suite Instance
     *
     * @param $cfg
     */
    function __construct($cfg) {

        // If no session is started, start one
        if(!isset($_SESSION))
            session_start();

        // Set configuration to given configuration array
        $this->cfg = $cfg;

        // Set current domain
        $this->domain = $cfg['install_domain'];

        // Set the document root
        $this->dir = $_SERVER['DOCUMENT_ROOT'];

        // Initialize the clean url array and set HTML base tag value
        $this->urlInit();

        // If there is a session, these are set later
        if(!$this->checkSession()) {

            // Set the HTML title tag
            $this->setTitle();

            // Generate the page start
            $this->generatePageStart();

        }

        // Generate the page end;
        $this->generatePageEnd();

        // Attempt to create a database connection
        try {
            $this->db_conn = new PDO(
                $cfg['db_dsn'],
                $cfg['db_user'],
                $cfg['db_pass'],
                [
                    PDO::ATTR_TIMEOUT => $cfg['pdo_exception_timeout'],
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        }

        // On database connection (PDOException) failure
        catch (PDOException $e) {

            // Hard log the database connection error
            $this->dsError($e->getMessage());

            // Set database error state
            $this->db_error = true;
        }

        // If a valid Dynamic Suite user session is set
        if($this->checkSession() && !$this->db_error) {

            // Get the current session ID
            $this->session =
                $_SESSION[$this->cfg['session_id'] . '_session_id'];

            // Get the current user id
            $this->user_id =
                $_SESSION[$this->cfg['session_id'] . '_user_id'];

            // Get the permissions for the current user
            $this->permissions = $this->getUserPerm($this->user_id);

            // Get the current user's account
            $this->account = $this->getUserAcct($this->user_id);

            // Check to see if the user is an administrator
            $this->is_admin = $this->account['administrator']
                ? true
                : false;

            // Load current and all possible modules and
            // current module (if exists)
            $this->loadModules();

            // Generate the title tag for the current module
            $this->setTitle();

            // Generate the page start
            $this->generatePageStart();

            // Generate the navigation bar
            $this->generateNavbar();

            // Generate the header bar
            $this->generateHeader();

            // Generate any tabs
            $this->generateTabs();

        }

    }

    /**
     * Hard file error logging
     *
     * @param $error
     */
    public function dsError($error) {

        // Shortcut if it is an SQL error
        $error = $error === 'sql'
            ? "SQL ERROR: [{$this->db_error_info[0]}] " .
              "[{$this->db_error_info[1]}] " .
              "{$this->db_error_info[2]}"
            : $error;

        // Log the error, make sure that the php user can write to the log directory
        file_put_contents($this->cfg['log_dir'],
            "[DYNAMIC SUITE ERROR] {$_SERVER['REMOTE_ADDR']} " . date('Y-m-d H:i:s') .
            " | " . $error  . PHP_EOL, FILE_APPEND);
    }

    /**
     * Generic SQL query function
     *
     * All data is returned as an associative array
     * For other return types, you can use PDO functions using the
     * PDO object "db_conn" within this class
     *
     * Note: on failure, this method will return an empty array
     *
     * @param $query
     * @param bool $args
     * @return array|bool
     */
    public function query($query, $args = false) {

        // If the query is empty, return false
        if(empty($query)) {

            // Set the database error state, message, and log the error
            $this->dbError();

            // Return fail
            return false;

        }

        // Prepare the statement
        $this->db_stmt = $this->db_conn->prepare($query);

        // If the statement failed to prepare
        if(!$this->db_stmt) {

            // Set the database error state, message, and log the error
            $this->dbError();

            // Return fail
            return false;

        }

        // If the statement prepared successfully
        else {

            // For single arguments
            $args = !is_array($args) ? [$args] : $args;

            // Argument count
            $argc = count($args);

            // For every argument
            for($i = 0; $i < $argc; $i++) {

                // Bind the argument to the parameter
                $this->db_stmt->bindParam($i + 1, $args[$i]);
            }

            // Execute the prepared statement
            $this->db_stmt->execute();

            // If the SQLSTATE error code is not set
            if(!$this->db_conn->errorInfo()[1]) {

                // Fetch the queried data (Associative)
                $data = $this->db_stmt->fetchAll(PDO::FETCH_ASSOC);

                // No Error
                $this->db_error = false;

                // Return the data
                return count($data) ? $data : true;

            }

            // If the query failed
            else {

                // Set the database error state, message, and log the error
                $this->dbError();

                // Return fail
                return false;

            }

        }

    }

    /**
     * Set the database error state, message, and log the error
     */
    private function dbError() {

        // Set database error state to true
        $this->db_error = true;

        // Set database error message
        $this->db_error_info = $this->db_conn->errorInfo();

        // Hard log the SQL error
        $this->dsError('sql');

    }

    /**
     * Log Generic Events
     *
     * See Log documentation for more information
     *
     * @param $type
     * @param $affected
     * @param $event
     * @return bool
     */
    public function logEvent($event, $type = 0, $affected = 'SYSTEM') {

        // Log query
        $query = 'INSERT INTO `ds_logs` ' .
                 '(`type`, `creator`, `affected`,`event`, `ip`, `session`) ' .
                 'VALUES (?,?,?,?,?,?)';

        // Event creator
        $creator = is_null($this->account)
            ? 'SYSTEM'
            : $this->account['username'];

        // Log data
        $data = [
            $type,
            $creator,
            $affected,
            $event,
            $_SERVER['REMOTE_ADDR'],
            $this->session
        ];

        // Execute the log query
        $this->query($query, $data);

        // Log return status
        return !$this->db_error ? true : false;

    }

    /**
     * Return response for Dynamic Suite API calls
     *
     * If no arguments are passed, it will return as "Internal Error" response
     *
     * @param string $status   - Status Code
     * @param int    $severity - Severity (0-3)
     * @param string $message  - Message
     * @param null   $data     - Optional Data
     *
     * @return array
     */
    public function APIResponse(
        $status   = 'IN_ERROR',
        $severity = 3,
        $message  = 'An internal error occurred, ' .
                    'please contact your system administrator',
        $data     = null
    ) {
        return json_encode(
            [
                'status'   => $status,
                'severity' => $severity,
                'message'  => $message,
                'data'     => $data
            ]
        );
    }

    /**
     * URL initialization
     *
     * Also sets the HTML base tag value
     */
    private function urlInit() {

        // If the URI contains "?" GET character
        if(strpos($_SERVER['REQUEST_URI'], '?')) {
            $url = explode('/',
                   trim(substr($_SERVER['REQUEST_URI'],0,
                   strpos($_SERVER['REQUEST_URI'],'?')),'/')
            );
        }

        // Normal URI
        else {
            $url = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        }

        // Set clean URL array
        $this->url = $url;

        // Set the HTML base
        $this->html_base = $this->cfg['install_domain'] . '/';

    }

    /**
     * Set a k, v ordered array of valid modules within the module directory that are
     * loaded from $cfg['ds_modules']. The array key will be the directory (module
     * short) name, where the value will be the module's configuration array
     *
     * This method will NOT warn if a module configuration has failed, it will just
     * skip the module all together. Make sure that you follow the documentation for
     * a list of required module configuration parameters
     *
     */
    public function loadModules() {

        // Array of loaded modules
        $modules = $this->cfg['ds_modules'];

        // Module directory
        $mod_dir = $this->dir . '/modules';

        // Loop through all of the loaded modules
        foreach($modules as $mod) {

            // Reserved module names
            if($mod === 'login' || $mod === 'error')
                continue;

            // If the item found in the directory isn't a directory,
            // Or the module directory isn't in the loaded modules
            // array, continue to next loaded module
            if(!is_dir("$mod_dir/$mod"))
                continue;

            // Get the module configuration file
            $cfg = json_decode(file_get_contents("$mod_dir/$mod/$mod.json"), true);

            // Continue if the pre module configuration is invalid
            if(is_null($cfg) || !is_array($cfg) || !is_array($cfg[key($cfg)]))
                continue;

            // The current module attempting to load
            $current = key($cfg);

            // The module configuration
            $cfg = $cfg[key($cfg)];

            // Make sure that the module configuration is valid
            // and at least one page is defined
            if(
                $mod === $current &&
                isset($cfg['name']) &&
                isset($cfg['perm']) &&
                $this->checkPermission($cfg['perm']) &&
                isset($cfg['active']) &&
                $cfg['active'] &&
                isset($cfg['content']) &&
                is_array($cfg['content']) &&
                key($cfg['content']) &&
                is_array($cfg['content'][key($cfg['content'])]) &&
                isset($cfg['content'][key($cfg['content'])]['name']) &&
                isset($cfg['content'][key($cfg['content'])]['perm']) &&
                isset($cfg['content'][key($cfg['content'])]['active']) &&
                isset($cfg['content'][key($cfg['content'])]['content'])
            ) {

                // Set the module short name (same as key)
                $cfg['short'] = $mod;

                // If pages are set and arrays
                if(isset($cfg['content']) && is_array($cfg['content'])) {

                    // Loop through all pages
                    foreach($cfg['content'] as $page => $page_cfg) {

                        // If the page configuration isn't valid
                        if(
                            !isset($page_cfg['name']) ||
                            !isset($page_cfg['perm']) ||
                            !isset($page_cfg['active']) ||
                            !isset($page_cfg['content'])
                        ) {
                            unset($cfg['content'][$page]);
                        }

                        // If the page isn't active
                        elseif(!$page_cfg['active']) {
                            unset($cfg['content'][$page]);
                        }

                        // If the user doesn't have permission to view the page
                        elseif(!$this->checkPermission($page_cfg['perm'])) {
                            unset($cfg['content'][$page]);
                        }

                        // Page configuration looks good
                        else {

                            // If any tabs exist
                            if(is_array($page_cfg['content'])) {

                                // Loop through all tabs
                                foreach($page_cfg['content'] as $tab => $tab_cfg) {

                                    // If the tab configuration isn't valid
                                    if(
                                        !isset($tab_cfg['name']) ||
                                        !isset($tab_cfg['perm']) ||
                                        !isset($tab_cfg['active']) ||
                                        !isset($tab_cfg['content'])
                                    ) {
                                        unset($page_cfg['content'][$tab]);
                                    }

                                    // If the tab isn't active
                                    elseif(!$tab_cfg['active']) {
                                        unset($cfg['content'][$page]['content'][$tab]);
                                    }

                                    // If the user doesn't have permission to view the tab
                                    elseif(!$this->checkPermission($tab_cfg['perm'])) {
                                        unset($page_cfg['content'][$tab]);
                                    }

                                    // Looks good, continue
                                    else {

                                        continue;

                                    }

                                }

                                // If no valid tabs are found, goto next module
                                if(!count($page_cfg['content']))
                                    unset($cfg['content'][$page]);

                            }

                        }

                    }

                    // If not valid pages are found, goto next module
                    if(!count($cfg['content']))
                        continue;

                }

                // Update the current module if matched
                if(
                    isset($this->url[0]) &&
                    $this->url[0] === $mod
                )
                    $this->module = $cfg;

                // Add the module to the master configuration array
                $this->modules[$mod] = $cfg;

            }

        }

        // If a module is set, and the module given is not the error or login page
        if(
            isset($this->url[0]) &&
            $this->url[0] !== 'error' &&
            $this->url[0] !== 'login'
        ) {

            // Module level CSS
            if(isset($this->module['css']) && is_array($this->module['css'])) {

                // Loop through any included files found
                foreach($this->module['css'] as $css) {

                    // If the file exists, include it
                    if(file_exists($this->dir . $css))
                        $this->html_css .= "<link href='$css' rel='stylesheet' />";

                }

            }

            // Search for any included javascript files
            if(isset($this->module['js']) && is_array($this->module['js'])) {

                // Loop through any included files found
                foreach($this->module['js'] as $js) {

                    // If the file exists, include it
                    if(file_exists($this->dir . $js))
                        $this->html_js .= "<script src='$js'></script>";

                }

            }

            // Page level CSS/JS (page given)
            if(
                isset($this->url[1]) &&
                isset($this->module['content'][$this->url[1]])
            ) {

                // Set content location
                $content = $this->module['content'][$this->url[1]];

                // If the module content css is found in the URL, and an array
                if(isset($content['css']) && is_array($content['css'])) {

                    // Loop through the included css list
                    foreach($content['css'] as $css) {

                        // If the file exists, include it
                        if(file_exists($this->dir . $css))
                            $this->html_css .= "<link href='$css' rel='stylesheet' />";

                    }

                }

                // If the module content js is found in the URL, and an array
                if(isset($content['js']) && is_array($content['js'])) {

                    // Loop through the included js list
                    foreach($content['js'] as $js) {

                        // If the file exists, include it
                        if(file_exists($this->dir . $js))
                            $this->html_js .= "<script src='$js'></script>";

                    }

                }

            }

            // Page level CSS/JS (no page given)
            elseif(
                isset($this->module['content']) &&
                is_array($this->module['content']) &&
                isset($this->module['content'][key($this->module['content'])])
            ) {

                // Set content location
                $content = $this->module['content'][key($this->module['content'])];

                // If the module content css is found in the JSON, and an array
                if(isset($content['css']) && is_array($content['css'])) {

                    // Loop through the included css list
                    foreach($content['css'] as $css) {

                        // If the file exists, include it
                        if(file_exists($this->dir . $css))
                            $this->html_css .= "<link href='$css' rel='stylesheet' />";

                    }

                }

                // If the module content js is found in the URL, and an array
                if(isset($content['js']) && is_array($content['js'])) {

                    // Loop through the included js list
                    foreach($content['js'] as $js) {

                        // If the file exists, include it
                        if(file_exists($this->dir . $js))
                            $this->html_js .= "<script src='$js'></script>";

                    }

                }

            }

            // Tab level CSS/JS (tab given)
            if(
                isset($this->url[2]) &&
                isset($this->module['content'][$this->url[1]]['content'][$this->url[2]])
            ) {

                // Set content location
                $content = $this->module['content'][$this->url[1]]['content'][$this->url[2]];

                // If the module content css is found in the URL, and an array
                if(isset($content['css']) && is_array($content['css'])) {

                    // Loop through the included css list
                    foreach($content['css'] as $css) {

                        // If the file exists, include it
                        if(file_exists($this->dir . $css))
                            $this->html_css .= "<link href='$css' rel='stylesheet' />";

                    }

                }

                // If the module content js is found in the URL, and an array
                if(isset($content['js']) && is_array($content['js'])) {

                    // Loop through the included css list
                    foreach($content['js'] as $js) {

                        // If the file exists, include it
                        if(file_exists($this->dir . $js))
                            $this->html_js .= "<script src='$js'></script>";

                    }

                }

            }

            // Tab level CSS/JS (no tab given)
            elseif(
                isset($this->url[1]) &&
                isset($this->module['content'][$this->url[1]]) &&
                is_array($this->module['content'][$this->url[1]]['content']) &&
                isset($this->module['content'][$this->url[1]]['content']
                    [key($this->module['content'][$this->url[1]]['content'])])
            ) {

                // Set content location
                $content = $this->module['content'][$this->url[1]]['content']
                    [key($this->module['content'][$this->url[1]]['content'])];

                // If the module content css is found in the JSON, and an array
                if(isset($content['css']) && is_array($content['css'])) {

                    // Loop through the included css list
                    foreach($content['css'] as $css) {

                        // If the file exists, include it
                        if(file_exists($this->dir . $css))
                            $this->html_css .= "<link href='$css' rel='stylesheet' />";

                    }

                }

                // If the module content js is found in the JSON, and an array
                if(isset($content['js']) && is_array($content['js'])) {

                    // Loop through the included css list
                    foreach($content['js'] as $js) {

                        // If the file exists, include it
                        if(file_exists($this->dir . $js))
                            $this->html_js .= "<script src='$js'></script>";

                    }

                }

            }

        }

    }

    /**
     * Load the contents (template) for a given file (directory)
     *
     * If no root is given, the project root will be used
     *
     * @param $file
     * @param bool $root
     * @return string
     */
    public function loadTemplate($file, $root = false) {

        // If no root is given
        if(!$root)
            return file_get_contents($this->dir . $file);

        // Load and return the template
        return file_get_contents($root . $file);

    }

    /**
     * Get the account for a given user ID
     *
     * @param $user_id
     *
     * @return array|bool
     */
    public function getUserAcct($user_id) {

        // Query to get the user's account
        $query = 'SELECT * FROM `ds_users` WHERE `user_id` = ?';

        // On query failure
        if(!$account = $this->query($query, $user_id))
            return false;

        // Account found
        if(is_array($account) && count($account) === 1)
            return $account[0];

        // No account found
        return false;

    }

    /**
     * Get a list of active or inactive users
     * Will return false on query fail, array on anything else
     *
     * If you give no arguments, it will get active users,
     * if you pass it false, it will get inactive users
     *
     * @param bool $active
     * @return array|bool
     */
    public function getUsers($active = true) {

        // User list type, active or inactive
        $type = $active ? 1 : 0;

        // Query for getting all of the users and group name
        $query = 'SELECT `users`.*, `groups`.`name` AS `group_name` ' .
                 'FROM `ds_users` AS `users` ' .
                 'LEFT JOIN `ds_group_meta` AS `groups` ' .
                 'ON `groups`.`group_id` = `users`.`group` ' .
                 'WHERE `users`.`status` = ?';

        // On query failure
        if(!$users = $this->query($query, [$type]))
            return false;

        return is_array($users) ? $users : [];

    }

    /**
     * Get the permissions for a given user id
     *
     * @param $id
     * @return array
     */
    public function getUserPerm($id) {

        // Permission array
        $return = [];

        // Query for getting the permission for the given user based on their group
        $query = 'SELECT `m`.*, `p`.`group_id`, ' .
                 '(SELECT `administrator` FROM `ds_users` ' .
                 'WHERE `user_id` = ?) AS `administrator` ' .
                 'FROM `ds_permissions` `m` ' .
                 'LEFT JOIN `ds_group_data` `p` ON `p`.`group_id` = ' .
                 '(SELECT `group` FROM `ds_users` WHERE `user_id` = ?) ' .
                 'AND `m`.`permission_id` = `p`.`permission_id`';

        // On query error
        if(!$permissions = $this->query($query, [$id, $id]))
            return false;

        // Loop through the returned permissions
        foreach($permissions as $k => $v) {

            // If the user has the permission or is an administrator
            if(!is_null($v['group_id']) || $v['administrator']) {

                // Set new k,v pair to TRUE
                $permissions[$k]['has'] = true;

            }

            // If the user doesn't have the permission
            else {

                // Set the new k,v pair to FALSE
                $permissions[$k]['has'] = false;

            }

            // Update the return array
            $return[$v['permission_id']] = $permissions[$k];

            // Get rid of ds_user and ds_user_administrator columns
            unset($return[$v['permission_id']]['group_id']);
            unset($return[$v['permission_id']]['administrator']);

        }

        // Return permissions for the user
        return $return;

    }

    /**
     * Checks the given permission for the current user
     * Also checks for a session
     *
     * @param $permission
     * @return bool
     */
    public function checkPermission($permission) {

        // Return false if no session is set
        if(!$this->checkSession())
            return false;

        // Administrators bypass all permissions
        if($this->is_admin)
            return true;

        // If no permissions exist
        if(!$this->permissions)
            return false;

        // If the given permission is FALSE, return true.
        // Used with module configurations to indicate no permissions
        if(!$permission)
            return true;

        // If the permission exists, and the user has it
        foreach($this->permissions as $data) {
            if($data['name'] === $permission) {
                if($data['has']) return true;
            }
        }

        // No conditions matched
        return false;

    }

    /**
     * Kills script execution if the permission check fails
     *
     * @param $permission
     * @param string $response
     */
    public function validatePermission(
        $permission,
        $response = 'Permission Denied'
    ) {

        // Die if the check fails
        if(!$this->checkPermission($permission)) {
            die($response);
        }

    }

    /**
     * Get an array of all possible permissions
     *
     * @return array|bool
     */
    public function getPermissions() {

        // Return array
        $return = [];

        // Query to get all possible permissions
        $query = 'SELECT * FROM `ds_permissions`';

        // On query failure
        if(!$permissions = $this->query($query))
            return false;

        // Create return array with permission id as key
        if(is_array($permissions)) {
            foreach($permissions as $permission) {
                $return[$permission['permission_id']] = $permission;
            }
        }

        // Return the permissions
        return $return;

    }

    /**
     * Get an array of possible permission groups
     *
     * @return array|bool
     */
    public function getPermissionGroups() {

        // Array of data to return
        // Key is the group name
        $groups = [];

        // Group meta query
        $meta = 'SELECT * FROM `ds_group_meta`';

        // Group data query
        $data = 'SELECT * FROM `ds_group_data` LEFT JOIN ' .
                '`ds_permissions` ON `ds_group_data`.`permission_id` = ' .
                '`ds_permissions`.`permission_id`';

        // Return the array of groups on success
        if(
            is_array($meta = $this->query($meta)) &&
            $data = $this->query($data)
        ) {

            // Create the return array
            foreach($meta as $group) {

                $groups[$group['group_id']] = $group;
                $groups[$group['group_id']]['permissions'] = [];

                // Add permissions (if any)
                if(is_array($data)) {

                    // Loop through all of the permissions
                    foreach($data as $permission) {

                        // If the current group equals the current permission data
                        if($group['group_id'] === $permission['group_id']) {

                            // Add the current permission to the group
                            $groups[$group['group_id']]['permissions']
                                [$permission['name']] = $permission['description'];

                        }

                    }

                }

            }

            // Return the groups
            return $groups;

        }

        // Case not matched
        return false;

    }

    /**
     * Update login metadata on login attempt
     *
     * @param $id
     *
     * @return bool
     */
    public function updateLoginMetadata($id) {

        // Query for updating loin metadata
        $query = 'UPDATE `ds_users` SET ' .
                 '`last_login_attempt` = NOW(),' .
                 '`login_attempts` = `login_attempts` + 1, ' .
                 '`last_login_ip` = ? ' .
                 'WHERE `user_id`=?';

        // On query failure
        if(!$this->query($query, [$_SERVER['REMOTE_ADDR'], $id]))
            return false;

        // Return success
        return true;

    }

    /**
     * Reset the login attempts for the given user and sets last successful
     * login time
     *
     * @param $id
     *
     * @return bool
     */
    public function resetLoginSuccess($id) {

        // Query for resetting login attempts
        $query = 'UPDATE `ds_users` SET `login_attempts` = 0, ' .
                 '`last_login_success` = NOW() WHERE `user_id` = ?';

        // On query failure
        if(!$this->query($query, $id))
            return false;

        // Return success
        return true;

    }

    /**
     * Generate a Dynamic Suite Session for the given user id
     *
     * @param $id
     * @return bool
     */
    public function generateSession($id) {

        // Generate the session
        $_SESSION[$this->cfg['session_id'] . '_session_id'] =
            $id . '$' . time();

        // Get the current session ID
        $this->session =
            $_SESSION[$this->cfg['session_id'] . '_session_id'];

        // Generate the session user id
        $_SESSION[$this->cfg['session_id'] . '_user_id'] =
            $id;

        // Get the current user id
        $this->user_id =
            $_SESSION[$this->cfg['session_id'] . '_user_id'];

    }

    /**
     * Attempt to log in a user. On success, assign a session and location
     *
     * @param $username
     * @param $id
     * @param $pass
     * @param $test_pass
     * @return array|bool
     */
    public function attemptLogin($username, $id, $pass, $test_pass) {

        // If the password doesn't verify
        if(!password_verify($test_pass, $pass))
            return false;

        // Reset the user's login attempts
        $this->resetLoginSuccess($id);

        // Generate the session
        $this->generateSession($id);

        // Log the login event
        $this->logEvent('User Login', USER_LOGIN, $username);

        // Redirect the user to the proper location
        $location = !is_null($this->cfg['mod_default'])
            ? $this->cfg['mod_default']
            : 'login';

        // Return login success
        return $this->APIResponse('OK', 1, USER_LOGIN, $location);

    }

    /**
     * Set the HTML title tag data
     */
    public function setTitle() {

        // If title override is present
        if($this->cfg['system_title']) {
            $this->html_title =
                htmlentities($this->cfg['system_title']);
        }

        // If no location is given, set title default
        elseif(!isset($this->url[0])) {
            $this->html_title =
                htmlentities($this->cfg['system_title_default']);
        }

        // If location is login, set title login
        elseif($this->url[0] === 'login') {
            $this->html_title =
                htmlentities($this->cfg['system_title_prefix']) . ' :: Login';
        }

        // If current module exists, set title module
        elseif($this->module) {
            $this->html_title =
                htmlentities($this->cfg['system_title_prefix']) . ' :: ' .
                htmlentities($this->module['name']);
        }

        // Default case, set title default
        else {
            $this->html_title =
                htmlentities($this->cfg['system_title_default']);
        }

    }

    /**
     * Generate the page start, includes head tag and included resources
     */
    public function generatePageStart() {

        // Get the main page start template
        $start = $this->loadTemplate('/templates/page_start.html');

        // Set the page title
        $start = str_replace('{{title}}', $this->html_title, $start);

        // Set the HTML base
        $start = str_replace('{{base}}', $this->html_base, $start);

        // Include any CSS
        $start = str_replace('{{css}}', $this->html_css, $start);

        // Include any JS
        $start = str_replace('{{js}}', $this->html_js, $start);

        // Set the page start
        $this->html_page_start = $start;

    }

    /**
     * Generate the page end, includes closing BODY and HTML tags
     */
    public function generatePageEnd() {

        // Get and set the main page end template
        $this->html_page_end =
            $this->loadTemplate('/templates/page_end.html');

    }

    /**
     * Generate the Dynamic Suite primary navigation bar
     *
     */
    public function generateNavbar() {

        // Get the navigation bar template
        $nav = $this->loadTemplate('/templates/nav.html');

        // Set the navigation bar header
        $nav = str_replace(
            '{{header}}',
            htmlentities($this->cfg['system_header']),
            $nav
        );

        // Set navigation bar login location
        $nav = str_replace(
            '{{location}}',
            $this->cfg['install_domain'] . '/login',
            $nav
        );

        // String to hold the navigation bar content
        $content = '';

        // For every valid module, add an entry on the navigation bar
        foreach($this->modules as $mod => $mod_cfg) {

            // If there is an icon in the config, include it
            $icon = isset($mod_cfg['icon']) ? $mod_cfg['icon'] : '';

            // Drop down accordion for multi-paged modules
            if(count($mod_cfg['content']) > 1) {

                // Drop down HTML (bootstrap)
                $content .= "<li><a data-toggle='collapse' data-target='#ds-nav-$mod'>";
                $content .= "<i class='fa $icon'></i> {$mod_cfg['name']}";
                $content .= "<i class='fa fa-chevron-right'></i></a>";
                $content .= "<ul class='nav collapse' id='ds-nav-$mod' role='menu'>";

                // For every page, add an entry
                foreach($mod_cfg['content'] as $page => $page_cfg) {

                    // Page HREF link
                    $href = $this->cfg['install_domain'] . "/$mod/$page";

                    // Page entry HTML
                    $content .= "<li><a id='ds-nav-$mod-$page' href='$href' target='_self'>";
                    $content .= "<i class='fa fa-angle-double-right'></i>{$page_cfg['name']}</a></li>";

                }

                // End multi-paged content
                $content .= "</ul></li>";

            }

            // If there is only one page in the module
            else {

                // Include the module's page
                $page = key($mod_cfg['content']);

                // Page HREF link
                $href = $this->cfg['install_domain'] . "/$mod/$page";

                // Page entry HTML
                $content .= "<li><a id='ds-nav-$mod' href='$href' target='_self'>";
                $content .= "<i class='fa $icon'></i> {$mod_cfg['name']}</a></li>";

            }

        }

        // Set the navigation bar content
        $nav = str_replace('{{content}}', $content, $nav);

        // Set the navigation bar
        $this->html_nav = $nav;

    }

    /**
     * Generate the module header bar
     */
    public function generateHeader() {

        // The string containing the header HTML
        $header = '';

        if($this->module) {

            // If the module page has an icon, include it
            if(
                isset($this->url[1]) &&
                isset($this->module['content'][$this->url[1]]['icon'])
            ) {
                $icon =
                    "<i class='fa {$this->module['content'][$this->url[1]]['icon']}'></i>";
            }

            // No page icon, but a module icon exists
            elseif(isset($this->module['icon'])) {

                $icon =
                    "<i class='fa {$this->module['icon']}'></i>";

            }

            // No icon exists
            else {

                $icon = '';

            }

            // Pre-append the icon
            $header .= $icon;

            // Set the header primary module name
            $header .= $this->module['name'];

            // If no page is set, include the first page name
            if(!isset($this->url[1])) {
                $header .= ' &#8212; ' .
                    $this->module['content'][key($this->module['content'])]['name'];
            }

            // If the page is set, include the page name
            elseif(
                array_key_exists($this->url[1], $this->module['content']) &&
                is_array($this->module['content'][$this->url[1]])
            ) {
                $header .= ' &#8212; ' .
                    $this->module['content'][$this->url[1]]['name'];
            }

        }

        // No module found/unknown location
        else {
            $header .= $this->cfg['system_title_default'];
        }

        // Set the HTML header
        $this->html_header = $header;

    }

    /**
     * Generate any tabs
     */
    public function generateTabs() {

        // String containing any navigation tabs
        $tabs = '';

        // If the location is a valid module
        if(array_key_exists($this->url[0], $this->modules)) {

            // Shorthand location
            $loc = $this->url;

            // If a page is given and the page has tabs
            if(
                isset($this->url[1]) &&
                isset($this->modules[$loc[0]]['content'][$loc[1]]) &&
                is_array($this->modules[$loc[0]]['content'][$loc[1]]['content'])
            ) {

                // Begin tabs
                $tabs .= "<ul class='nav nav-tabs ds-tabs'>";

                // Loop through all tabs and include HTML elements
                foreach($this->modules[$loc[0]]['content'][$loc[1]]['content'] as $tab => $tab_cfg) {

                    $href = $this->cfg['install_domain'] . "/{$loc[0]}/{$loc[1]}/$tab";

                    $tabs .= "<li role='presentation' id='ds-nav-{$loc[0]}-{$loc[1]}-$tab'>" .
                             "<a href='$href' target='_self'>{$tab_cfg['name']}</a></li>";

                }

                // End tabs
                $tabs .= "</ul>";

            }

        }

        // Set the HTML tabs
        $this->html_tabs = $tabs;

    }

    /**
     * Check to see if there is a valid session
     *
     * @return bool
     */
    public function checkSession() {

        // If a valid Dynamic Suite user session is set
        return
            isset($_SESSION[$this->cfg['session_id'] . '_session_id']) &&
            isset($_SESSION[$this->cfg['session_id'] . '_user_id'])
                ? true
                : false;

    }

    /**
     * Validate a user's session, redirect to login on failure
     *
     * @return bool
     */
    public function validateSession() {

        // If a session is not found
        if(!$this->checkSession()) {
            header('Location: ' . $this->domain . '/login');
            return false;
        }

        // Session found
        return true;

    }

    /**
     * Validate the current view and redirect on fail
     */
    public function validateView() {

        // If the location is login or error, return TRUE
        if(
            $this->url[0] === 'login' ||
            $this->url[0] === 'error'
        ) {
            return true;
        }

        // If database error is present
        elseif($this->db_error) {
            header('Location: ' . $this->domain . '/error');
            return false;
        }

        // If no session is found, goto login and return FALSE
        elseif(!$this->checkSession()) {
            header('Location: ' . $this->domain . '/login');
            return false;
        }

        // If the given location (module) isn't found, goto login and
        // return FALSE
        elseif(!array_key_exists($this->url[0], $this->modules)) {
            header('Location: ' . $this->domain . '/login');
            return false;
        }

        // If the given page isn't valid, goto 1st page
        elseif(
            !isset($this->url[1]) ||
            empty($this->url) ||
            !array_key_exists($this->url[1],
            $this->modules[$this->url[0]]['content'])
        ) {
            reset($this->modules[$this->url[0]]['content']);
            $page = key($this->modules[$this->url[0]]['content']);
            $loc = '/' . $this->url[0] . '/' . $page;
            header('Location: ' . $this->domain . $loc);
            return false;
        }

        // If the current location is in the module configuration,
        // return TRUE
        else {
            return true;
        }

    }

    /**
     * Return the current view (file) location
     *
     * @return string
     */
    public function getView() {

        // The current module for getting the view
        $mod = $this->modules;

        // Current location array (shortened)
        $loc = $this->url;

        // If a tab is given and the tab exists, include it
        if(
            isset($loc[2]) &&
            isset($mod[$loc[0]]['content'][$loc[1]]['content'][$loc[2]])
        ) {
            return $mod[$loc[0]]['content'][$loc[1]]['content'][$loc[2]]['content'];
        }

        // No tab given
        else {

            // If the page has tabs, include the 1st one
            if(is_array($mod[$loc[0]]['content'][$loc[1]]['content'])) {
                $tab = key($mod[$loc[0]]['content'][$loc[1]]['content']);
                return $mod[$loc[0]]['content'][$loc[1]]['content'][$tab]['content'];
            }

            // If no tabs are given, include page
            else {
                return $mod[$loc[0]]['content'][$loc[1]]['content'];
            }

        }

    }

    /**
     * Convert a Y-m-d date to the configured date format
     *
     * @param $date
     * @return bool|string
     */
    public function dateSQL2Format($date) {

        // Return blank if timestamp is blank, or a formatted
        // date if not
        return $date === '0000-00-00' || empty($date)
            ? 'Never'
            : date($this->cfg['date_format'], strtotime($date));

    }

    /**
     * Convert a SQL timestamp to a formatted timestamp
     *
     * @param $time
     * @return bool|string
     */
    public function timestampSQL2Format($time) {

        // Return blank if timestamp is blank, or a formatted
        // timestamp if not
        return $time === '0000-00-00 00:00:00' || empty($time)
            ? 'Never'
            : date($this->cfg['timestamp_format'], strtotime($time));

    }

    /**
     * Convert an SQL time to a formatted time
     *
     * @param $time
     * @return bool|string
     */
    public function timeSQL2Format($time) {

        // Return blank if time is blank, or a formatted
        // time if not
        return  $time === '00:00:00' || empty($time)
            ? 'Never'
            : date($this->cfg['time_format'], strtotime($time));

    }

}

// Create a new Dynamic Suite Instance
$ds = new dsInstance($cfg);