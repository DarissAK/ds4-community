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

    // Database Error object (PDOException)
    public $db_error;

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
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
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

        // On database connection failure
        catch (PDOException $e) {

            // Set error object
            $this->db_error = $e;

        }

    }

    /**
     * Generic SQL query function
     *
     * All data is returned as an associative array
     * For other return types, you can use PDO functions using the
     * PDO object "db_conn" within this class or call in a new
     * instance of the $sdc class
     *
     * Note: If this query returns an empty data set, it will return
     * a boolean of TRUE to not that the query succeeded, even though
     * there is no data.
     *
     * @param $query
     * @param bool $args
     * @return array|bool
     */
    public function query($query, $args = false) {

        // No query given
        if(empty($query))
            return false;

        // Prepare the statement
        try {
            $this->db_stmt = $this->db_conn->prepare($query);
        }

            // Prepare failed
        catch(PDOException $e) {
            return $this->sdcError($e);
        }

        // Bind arguments
        if($args) {

            // For single arguments
            $args = !is_array($args) ? [$args] : $args;

            // Argument count
            $argc = count($args);

            // Bind the arguments to the parameters
            for($i = 0; $i < $argc; $i++) {

                // Attempt to bind
                try {
                    $this->db_stmt->bindParam($i + 1, $args[$i]);
                }

                    // On bind failure
                catch(PDOException $e) {
                    return $this->sdcError($e);
                }

            }

        }

        // Execute the prepared statement
        try {

            // Execute statement
            $this->db_stmt->execute();

            // Only fetch for SELECT statements
            $data = substr($query, 0, 6) === 'SELECT'
                ? $this->db_stmt->fetchAll(PDO::FETCH_ASSOC)
                : [];

            // No Error
            $this->db_error = null;

            // Return the data
            return count($data) ? $data : true;

        }

        // Execute fail
        catch(PDOException $e) {
            return $this->sdcError($e);
        }

    }

    /**
     * Set error states
     *
     * @param $exception
     * @return bool
     */
    private function sdcError($exception) {
        $this->db_error = $exception;
        return false;
    }

}