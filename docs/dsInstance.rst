====================
The dsInstance Class
====================

.. contents::

Initialization
--------------
At the core of the Dynamic suite framework, there is a class called dsInstance.
dsInstance contains most of the logic that is used on every page, such as static
values (configuration values), or functions for running things like SQL queries.

The class dsInstance is loaded automatically into a variable called $ds when you
include the file ``/server/fn_init.php``.

The class is automatically included in all rendered pages/scripts withing Dynamic
Suite modules.

There are some things that happen automatically when a new instance is created
(when the file is included, or on page load).

Here is a list (in order) of what happens when it initializes:

- Check to see if a PHP session is running, and create one if not.
- Set the instance's global configuration to the data found in ``/config/config.php``
- Set ``$ds->domain`` and ``$ds->dir``.
- Initialize the URL (see ``$ds->urlInit()`` below)
- If a Dynamic Suite session is set, set the page title, and generate the page start
  including resources such as Javascript and CSS
- Generate the page end (closing HTML tags)
- Create a new database connection (if this fails, exit the script and display fatal error).
- If a session is set, set ``$ds->session``, ``$ds->username``, ``$ds->permissions``,
  ``$ds->account``, ``$ds->is_admin``, load all module configurations and validate them,
  set new title, Set new page start, generate the navigation bar, generate header ribbon,
  and generate the tabs ribbon (if any).


Class Variables
===============
These class members can be accessed and used by module developers. Some of them are only
really needed on page loads for the framework, but all are accessible.

$ds->cfg
~~~~~~~~
(Array) The current configuration loaded from /config/config.php

$ds->db_conn
~~~~~~~~~~~~
(Object) The database connection object (See PHP PDO)

$ds->db_stmt
~~~~~~~~~~~~
(Object) The last database statement object (See PHP PDO)

$ds->db_error
~~~~~~~~~~~~~
(Boolean) If the last database query returned an error or if any database error is present

$ds->db_error_info
~~~~~~~~~~~~~~~~~~
(String) Information about the last database error (if any)

$ds->url
~~~~~~~~
(Array) An array of the URL structure

Example: www.example.com/some/page

$ds->url[0] // some

$ds->url[1] // page

Note: This will strip off any GET data

$ds->html_base
~~~~~~~~~~~~~~
(String) The HTML base tag (for CSS)

$ds->html_css
~~~~~~~~~~~~~
(String) The HTML tags containing any included CSS for the current module

$ds->html_js
~~~~~~~~~~~~
(String) The HTML tags containing any included JS for the current module

$ds->html_title
~~~~~~~~~~~~~~~
(String) The HTML tag containing the current page's title

$ds->html_page_start
~~~~~~~~~~~~~~~~~~~~
(String) The HTML header/page start tags, such as the head, resources, etc

$ds->html_page_end
~~~~~~~~~~~~~~~~~~
(String) The HTML end tags (body, html end)

$ds->html_nav
~~~~~~~~~~~~~
(String) The HTML containing the navigation bar

$ds->html_header
~~~~~~~~~~~~~~~~
(String) The HTML containing the header ribbon

$ds->html_tabs
~~~~~~~~~~~~~~
(String) The HTML containing the tabs ribbon

$ds->domain
~~~~~~~~~~~
(String) The current domain where the framework is hosted

$ds->dir
~~~~~~~~
(String) The directory on the server where the framework is hosted

$ds->user_id
~~~~~~~~~~~~
(Int) The user ID of the currently logged in user

$ds->session
~~~~~~~~~~~~
(String) The session ID of the currently logged in user

$ds->account
~~~~~~~~~~~~
(Array) An array of the currently logged in user's account data

$ds->is_admin
~~~~~~~~~~~~~
(Boolean) If the current user is an administrator

$ds->permissions
~~~~~~~~~~~~~~~~
(Array) An array of the current user's permissions.

Note: This contains all possible permissions as well, but with a key added to the
array called ``has`` which indicates if they have the permission or not.

$ds->modules
~~~~~~~~~~~~
(Array) An array of all module configurations. The keys of this array are the module names

$ds->module
~~~~~~~~~~~
(Array) An array of the current module's configuration

Class Methods
=============
These class methods can be accessed and used by module developers. Some of them are only
really needed on page loads for the framework, but all are accessible.

$ds->dsError($error)
~~~~~~~~~~~~~~~~~~~~
(Void) Hard log ``$error`` to the log file with timestamps added automatically

$ds->query($query, $arguments)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
(Boolean | Array) Query the database.

``$arguments`` are optional, if there is a single argument, just include it as a single variable

Ex: ``$ds->query($query, $myVar);``

If you have multiple arguments, they must be in an array

Ex: ``$ds->query($query, [$var1, $var2]);``

When writing queries, you must use a ? as a placeholder, the arguments array will bind to
it in the proper order.

Ex: ``$ds->query('SELECT * FROM table WHERE column = ?', $myValue);``


$ds->dbError
~~~~~~~~~~~~
(Void) Set database error to TRUE, error message to the error, and hard log the error

$ds->logEvent($event, $type = 0, $affected = 'SYSTEM')
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
(Boolean) Log an event to the database lot table

``$event`` is a string that describes the event

``$type`` is a unique type for filtering on the events (default 0)

``$affected`` is the user that is affected (default SYSTEM)

Timestamps and creator will be automatically appended

$ds->APIResponse($status, $severity, $message, $data)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
(JSON Array) Output a JSON API Response

``$status`` is the response string (ex: OK)

``$severity`` is a bootstrap status priority from 0-3

- 0: Success
- 1: Info
- 2: Warning
- 3: Error

``$message`` is a response message (i.e. what happened)

``$data`` option data to return

$ds->urlInit()
~~~~~~~~~~~~~~
(Void) Creates ``$ds->url``

Also sets ``$ds->html_base``

$ds->loadModules()
~~~~~~~~~~~~~~~~~~
(Void) Re-load all module configurations

Sets ``$ds->modules``, ``$ds->module``, ``$ds->html_css``, and ``$ds->html_js``

$ds->loadTemplate($file)
~~~~~~~~~~~~~~~~~~~~~~~~
(String) Loads a given ``$file``

Alias for ``file_get_contents()``

$ds->getUserAcct($user)
~~~~~~~~~~~~~~~~~~~~~~~
(Boolean | Array) Gets the account array for a given ``$user``

If the user is not found, it will return FALSE

$ds->getUsers($active = true)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
(Boolean | Array) Gets all of the user accounts

If ``$active`` is set to true (default), then only active users will be retrieved

If ``$active`` is set to false, then only inactive users will be retrieved

Returns FALSE on database error

$ds->getUserPerm($user)
~~~~~~~~~~~~~~~~~~~~~~~
(Array) Get an array of permissions for the given ``$user``

$ds->checkPermission($permission)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
(Boolean) Check if the current user has a given ``$permission``

If the user is set to an administrator (see ``$ds->is_admin``), it will always evaluate to
TRUE, even if they don't have the given permission.

If the ``$permission`` evaluates to FALSE, it will return TRUE (Used for no permissions in
module configurations).

It requires a valid session to return TRUE.

$ds->validatePermission($permission)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
(Void) Checks the given ``$permission`` for the current user, redirects them to the login
page on failure.

$ds->getPermissions()
~~~~~~~~~~~~~~~~~~~~~
(Boolean | Array) Get an array of all possible framework permissions.

Returns FALSE on database failure.

$ds->getPermissionGroups()
~~~~~~~~~~~~~~~~~~~~~~~~~~
(Boolean | Array) Get an array of all possible framework permission groups.

Returns FALSE on database failure.

$ds->updateLoginMetadata($user)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
(Boolean) Updates the login metadata for the given ``$user`` such as last login attempt,
login attempt IP address, and login attempt count.

Returns TRUE on success and FALSE on database failure.

$ds->resetLoginSuccess($user)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
(Boolean) Reset the login attempts for the given ``$user``

Returns TRUE on success and FALSE on database failure.

$ds->generateSession($user)
~~~~~~~~~~~~~~~~~~~~~~~~~~~
(Void) Generate a new session for the given ``$user``.

Saves the session in the ``$_SESSION`` array in the form of;

``$_SESSION['{session_id}_session_id']``

``$_SESSION['{session_id}_username']``

Also sets ``$ds->session`` and ``$ds->username``

$ds->attemptLogin($user, $pass, $test_pass)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
(Boolean | JSON Array) Attempts to authenticate the given ``$user`` with a given
``$test_pass`` and their currently stored ``$pass``

Returns an ``OK`` JSON response on success, and FALSE on failure.

$ds->setTitle()
~~~~~~~~~~~~~~~
(Void) Generates and sets ``$ds->html_title`` based on parameters in ``/config/config.php``

$ds->generatePageStart()
~~~~~~~~~~~~~~~~~~~~~~~~
(Void) Generates and sets ``$ds->html_page_start``

$ds->generatePageEnd()
~~~~~~~~~~~~~~~~~~~~~~
(Void) Generates and sets ``$ds->html_page_end``

$ds->generateNavbar()
~~~~~~~~~~~~~~~~~~~~~
(Void) Generates and sets ``$ds->html_nav``

$ds->generateHeader()
~~~~~~~~~~~~~~~~~~~~~
(Void) Generates and sets ``$ds->html_header``

$ds->generateTabs()
~~~~~~~~~~~~~~~~~~~
(Void) Generates and sets ``$ds->html_tabs``

$ds->checkSession()
~~~~~~~~~~~~~~~~~~~
(Boolean) Checks to see if a session is set.

$ds->validateSession()
~~~~~~~~~~~~~~~~~~~~~~
(Boolean) Checks to see if a session is set.

Returns TRUE on success, and FALSE on failure.

If it evaluates to FALSE, the current user will be redirected to the login page.

$ds->validateView()
~~~~~~~~~~~~~~~~~~~
(Boolean) Validates the current user's request for a view.

Redirects the user to a valid view and returns FALSE if their requested view isnt valid

Returns TRUE if they request a valid view.

$ds->getView()
~~~~~~~~~~~~~~
(String) Get the file location of the currently requested view

$ds->dateSQL2Format($date)
~~~~~~~~~~~~~~~~~~~~~~~~~~
(String) Formats a ``$date`` from SQL format to the format given for dates in ``/config/config.php``

$ds->timestampSQL2Format($time)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
(String) Formats a ``$time`` from SQL format to the format given for timestamps in ``/config/config.php``

$ds->timeSQL2Format($time)
~~~~~~~~~~~~~~~~~~~~~~~~~~
(String) Formats a ``$time`` from SQL format to the format given for times in ``/config/config.php``
