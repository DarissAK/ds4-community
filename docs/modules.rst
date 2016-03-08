==================
Module Development
==================

.. contents::

Module Structure
================
Modules are directories within the **module** directory with a specific name and
configuration file.

Each module must contain a JSON file with configuration settings to structure the module.

Here is an example configuration::

  {
    "my-module" : {
      "name"    : "My Module",
      "version" : "1.0",
      "author"  : "Simplusoft LLC",
      "about"   : "This is an example module",
      "perm"    : "my_permission",
      "active"  : true,
      "icon"    : "fa-calendar",
      "css" : {
        "0" : "/path/to/css/main.css"
      },
      "js" : {
        "0" : "/path/to/js/client-side.js"
      },
      "include" : {
        "0" : "/path/to/included/server.inc.php"
      },
      "content" : {
        "some-page" : {
          "name"    : "Some Page",
          "perm"    : "my_page_permission",
          "active"  : true,
          "icon"    : "fa-info",
          "content" : {
            "some-tab" : {
              "name"    : "Some Tab",
              "perm"    : "my_tab_permission",
              "active"  : true,
              "content" : "/modules/my-module/pages/main.php"
            }
          }
        }
      }
    }
  }

The first entry into the array must be the name of the module, this is the same name as the
directory that the json file is in.

This array contains the following directives:

(String) name
-------------
- The friendly name of the module, as seen on navigation bar

(String) version
----------------
- The version of the module

(String) author
---------------
- The module author

(String) about
--------------
- A short description of the module

(String)perm
------------
- The permission required to load the module (description, not id)

(Boolean) active
----------------
- If the module is active or not

(String) icon
-------------
- The Font Awesome class for the navigation bar icon

(Array) css
-----------
- An array of strings containing css file locations to be included

(Array) js
----------
- An array of strings containing js file locations to be included

(Array) include
---------------
- An array of strings containing php file locations to be included

(Array) content
---------------
- An array of pages within the module

Content is made up of an array of pages to load. Each page is its own array.
The main array key is the name of the page (as seen in the request URL).
The array contains the following keys:

(String) name
-------------
- The name of the page as seen in the navigation bar and the header ribbon

(String) perm
-------------
- The permission required to load the page (description, not id)

(String) icon
-------------
- The Font Awesome class for the navigation bar icon and header ribbon icon

(Boolean) active
----------------
- If the page is active or not

(Array | String) content
------------------------
- An array of tabs within the page, or a file location for the page (if no tabs)

Page content is made up an array of tabs to load. Each tab is its own array.
The main array is the name of the tab (as seen in the request URL).
The array contains the following keys:

(String) name
-------------
- The name of the tab as seen on the tab

(String) perm
-------------
- The permission required to load the tab (description, not id)

(Boolean) active
----------------
- If the tab is active or not

(String) content
----------------
- The file location of the tab