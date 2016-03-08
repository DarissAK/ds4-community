<?php
// +-------------------------------------------------------------------------+
// |  Simple Database Class for PHP PDO                                      |
// |  A simple PDO wrapper for running basic queries                         |
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

/**
 * Class sdc
 */
class sdc {

    // Database Connection Object
    public $db_conn;

    // Database Statement Object
    public $db_stmt;

    // Database Error State (if any)
    public $db_error = false;

    // Database Error Message (if any)
    public $db_error_info;

    /**
     * sdc constructor.
     *
     * @param $dsn
     * @param $user
     * @param $pass
     * @param array $options
     */
    public function __construct(
        $dsn,
        $user,
        $pass,
        $options = [
            PDO::ATTR_TIMEOUT => 1,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    ) {

        // Attempt to create a database connection
        try {
            $this->db_conn = new PDO(
                $dsn,
                $user,
                $pass,
                $options
            );
        }

        // On database connection (PDOException) failure
        catch (PDOException $e) {

            // Set database error state to true
            $this->db_error = true;

            // Set database error message
            $this->db_error_info = $this->db_conn->errorInfo();

        }

    }

    /**
     * Generic SQL query function
     *
     * All data is returned as an associative array unless options are specified
     * For other return types, you can use PDO functions using the
     * PDO object "db_conn" within this class
     *
     * Note: on failure, this method will return false
     *
     * @param $query
     * @param bool $args
     * @return array|bool
     */
    public function query($query, $args = false) {

        // If the query is empty, return false
        if(empty($query)) {

            // Set database error state to true
            $this->db_error = true;

            // Set database error message
            $this->db_error_info = $this->db_conn->errorInfo();

            // Return fail
            return false;

        }

        // Prepare the statement
        $this->db_stmt = $this->db_conn->prepare($query);

        // If the statement failed to prepare
        if(!$this->db_stmt) {

            // Set database error state to true
            $this->db_error = true;

            // Set database error message
            $this->db_error_info = $this->db_conn->errorInfo();

            // Return fail
            return false;

        }
        // If the statement prepared successfully
        else {

            // For single argument strings
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

                // Set database error state to true
                $this->db_error = true;

                // Set database error message
                $this->db_error_info = $this->db_conn->errorInfo();

                // Return fail
                return false;

            }

        }

    }

}