==================
Module Development
==================

.. contents::

Module Structure
================
Modules are directories within the **module** directory with a specific name and
configuration file.

Example:

``
    modules (Module directory within the root)
        my_module (The module name should be the directory name)
            my_module.json (JSON config file, same name as parent directory)
``

The JSON configuration contains the structure for your module.