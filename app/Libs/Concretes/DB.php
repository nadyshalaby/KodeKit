<?php

/**
 * This file is part of kodekit framework
 * 
 * @copyright (c) 2015-2016, nady shalaby
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Libs\Concretes;

use App\Libs\Statics\Config;
use App\Libs\Statics\Url;
use PDO;
use PDOException;
use function isKeyStringArray;

/**
 * Database class that encapsulate the PDO methods
 */
class DB {

    /**
     * static variable used for all PDO connections
     * @var DB 
     */
    private static $_instance;

    /**
     * stores the PDO connection
     * @var PDO 
     */
    private $_pdo,
            /**
             * Represents the sql query
             * @var string
             */
            $_query,
            /**
             * Error flag
             * @var bool
             */
            $_error = false,
            /**
             * Holds the fetched results
             * @var array
             */
            $_results,
            /**
             * Holds the fetched rows count 
             * @var int
             */
            $_count = 0,
            /**
             * Holds the default fetch mode 
             * @var int
             */
            $_fetchMode = PDO::FETCH_OBJ,
            /**
             * holds the class name if fetch mode setted to PDO::FETCH_CLASS
             * @var string
             */
            $_className = '';

    /**
     * Constractor to establish the PDO connection to the database
     */
    private function __construct() {
        try {

            // setting up the main PDO connection
            $this->_pdo = new PDO("mysql:host=" . Config::app('mysql>host') . ";dbname=" . Config::app('mysql>db'), Config::app("mysql>username"), Config::app("mysql>password"));

            //grapping the <code>fetch_mode</code> flag from the config array
            $fetchMode = Config::app("mysql>fetch_mode");
            $fetchModes = [PDO::FETCH_OBJ, PDO::FETCH_ASSOC, PDO::FETCH_CLASS];
            if (!empty($fetchMode) && in_array($fetchMode, $fetchModes)) {
                $this->_fetchMode = $fetchMode;
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    /**
     * Returns an instantce of the DB class with alive connection
     * @param string [$className] the name of the class in case of <code>PDO::FETCH_CLASS</code> flag  
     * @return obj DB
     */
    public static function getInstance($className = '') {
        if (!isset(self::$_instance)) {
            self::$_instance = new DB($className);
        }
        self::$_instance->setClassName($className);
        return self::$_instance;
    }

    /**
     * Query the database to execute the given sql statement with the specified optional values
     * @param string $sql string represents the SQL query to be executed
     * @param array [$params] array holds the placeholders values
     * @return obj|boolean
     */
    public function exec($sql, $params = []) {
        $this->_error = false;
        if ($this->_query = $this->_pdo->prepare($sql)) {
            if (count($params)) {
                foreach ($params as $key => $value) {
                    if (is_int($key)) {
                        $key ++;
                    }
                    $this->_query->bindValue($key, $value);
                }
            }
            if ($this->_query->execute()) {
                if (!empty($this->_className) && class_exists($this->_className) && $this->_fetchMode === PDO::FETCH_CLASS) {
                    $this->_query->setFetchMode(PDO::FETCH_CLASS, $this->_className);
                } else {
                    $this->_fetchMode = PDO::FETCH_OBJ;
                    $this->_query->setFetchMode($this->_fetchMode);
                }
                $this->_results = $this->_query->fetchAll();
                $this->_count = $this->_query->rowCount();
            } else {
                $this->_error = true;
            }
        }
        if (empty($this->_results)) {
            return null;
        }
        return $this->_results;
    }

    /**
     * Helper Method Executes the Passed Action
     * 
     * @param string $action the type of action (eg. UPDATE, DELETE, ...)
     * @param string $table the table name
     * @param array $columns array of columns names[,values]
     * @param string $condition string represents where clause
     * @param array $placeholderVals array of placeholders values
     * @return array results 
     */
    private function action($action, $table, $columns = [], $condition = '', $placeholderVals = []) {
        $sql = "";
        switch ($action) {
            case 'INSERT':
                // constructing column names 
                $sql .= "INSERT INTO {$table} ";
                if (count($columns)) {
                    if (isKeyStringArray($columns)) {
                        $colNames = implode(', ', array_keys($columns));
                        $colVals = implode(', ', array_fill(0, count(array_values($columns)), '?'));
                        $sql .= "({$colNames}) VALUES ({$colVals}) ";
                    } else {
                        $colVals = implode(', ', '?');
                        $sql .= "VALUES ($colVals) ";
                    }
                    $placeholderVals = array_values($columns);
                }
                break;
            case 'UPDATE':
                // constructing column names 
                $sql .= "UPDATE {$table} SET ";
                if (count($columns)) {
                    if (isKeyStringArray($columns)) {
                        foreach ($columns as $key => $value) {
                            $sql .= " {$key} = ? ,";
                        }
                    }
                    $sql = substr($sql, 0, strlen($sql) - 1);
                }

                // constructing where condition
                if (empty($condition)) {
                    $condition = "1";
                    $placeholderVals = [];
                }

                // prepend the values of columns array to be updated
                $colVals = array_reverse(array_values($columns));
                foreach ($colVals as $key => $value) {
                    array_unshift($placeholderVals, $value);
                }
                $sql .= "WHERE {$condition}";
                break;
            case "DELETE" :
                $sql = "DELETE FROM {$table} ";
                // constructing where condition
                if (empty($condition)) {
                    $condition = "1";
                    $placeholderVals = [];
                }
                $sql .= "WHERE {$condition}";
                break;
        }
        return $this->exec($sql, $placeholderVals);
    }

    /**
     * Delete tuple from the given table according to the specified condition
     * @param string $table 
     * @param array $where 
     * @return obj
     */
    public function delete($table, $condition = '', array $placeholderVals = []) {
        return $this->action("DELETE", $table, null, $condition, $placeholderVals);
    }

    /**
     * Insert the values of fields associated with the fields array into the given table 
     * @param string $table 
     * @param array $fields 
     * @return obj|false
     */
    public function insert($table, $columns = []) {
        return $this->action("INSERT", $table, $columns, null, null);
    }

    /**
     * Update the values of specified fields  into the given table according the given condition
     * @param type $table
     * @param type $columns
     * @param type $condition
     * @param type $placeholderVals
     * @return type
     */
    public function update($table, $columns = [], $condition = '', $placeholderVals = []) {
        return $this->action("UPDATE", $table, $columns, $condition, $placeholderVals);
    }

    public function select($distinct = false, $table, $columns = [], $condition = '', $placeholderVals = [], $groupBy = '', $having = '', $orderBy = '', $limit = 0) {
        $sql = "SELECT ";

        if ($distinct) {
            $sql .= "DISTINCT ";
        }
        // constructing column names 
        if (count($columns)) {
            $sql .= implode(', ', $columns);
        } else {
            $sql .= "*";
        }
        // constructing where condition
        if (empty($condition)) {
            $condition = "1";
        }
        $sql .= " FROM {$table} WHERE {$condition}";

        if (!empty($groupBy)) {
            $sql .= " GROUP BY {$groupBy}";
        }

        if (!empty($having)) {
            $sql .= " HAVING {$having}";
        }

        if (!empty($orderBy)) {
            $sql .= " ORDER BY {$orderBy}";
        }

        if (is_array($limit)) {
            $limit = implode(', ', $limit);
            $sql .= " LIMIT {$limit}";
        }else  if (is_numeric($limit) && $limit > 0) {
            $sql .= " LIMIT {$limit}";
        }
        return $this->exec($sql, $placeholderVals);
    }

    public function setClassName($className = '') {
        $this->_className = $className;
    }

    /**
     * Return an boolean if there's an error
     * @return boolean
     */
    public function isError() {
        return $this->_error;
    }

    /**
     * Returns the Error Message if an error occured
     * @return string
     */
    public function getError() {
        return $this->_query->errorInfo();
    }

    /**
     * Returns the row count of the last result set fetched
     * @return integer count
     */
    public function count() {
        return $this->_count;
    }

    /**
     * Returns the ID of the last inserted row or sequence value 
     * @param string $name
     * @return type
     */
    public function lastId($name = null) {
        return $this->_pdo->lastInsertId($name);
    }

    /**
     * Build the database from the specified SQL file <code>dbfile</code> if the <code>dbrestore</code> flag is set
     * @return boolean
     */
    public function buildDB() {
        $filename = Url::resource("databases") . "/" . Config::app('mysql>dbfile');
        $dbrestore = Config::app('mysql>dbrestore');
        $op_data = '';
        if (file_exists($filename) && $dbrestore === true) {
            $lines = file($filename);
            foreach ($lines as $line) {
                if (substr($line, 0, 2) == '--' || $line == '') {//This IF Remove Comment Inside SQL FILE
                    continue;
                }
                $op_data .= $line;
                if (substr(trim($line), -1, 1) == ';') {//Breack Line Upto ';' NEW QUERY
                    $this->exec($op_data);
                    $op_data = '';
                }
            }
            if ($this->isError()) {
                return false;
            } else {
                return true;
            }
        }
        return true;
    }

}
