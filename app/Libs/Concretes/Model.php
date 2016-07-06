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

use App\Libs\Exceptions\MassAssignException;
use Exception;

/**
 * abstract class that encapsultes the methods of DB class in shape of static methods used for 
 * quick access to the database 
 * 
 * @author Nady Shalaby <nady80878@gmail.com>
 */
abstract class Model {

    /**
     * Represents the last inserted id
     * @var mixed 
     */
    private static $lastId = [];

    /**
     *
     * Represents the columns names and its values 
     * @var array  
     */
    protected $columns = [];

    /**
     * Magic getter for column value
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        if (isset($this->columns[$name])) {
            return $this->columns[$name];
        }
        return null;
    }

    /**
     * Magic setter for the column 
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        $this->columns[$name] = $value;
    }

    /**
     * used for inserting new row with current column values listed in columns[]
     * <b>NOTE:</b> the fetch mode must be <code>'fetch_mode' => PDO::FETCH_CLASS</code>
     * @depends PDO::FETCH_CLASS 
     */
    public function save() {
        self::insert($this->columns);
    }

    /**
     * used for updating the row that holds current column values listed in columns[]
     * <b>NOTE:</b> the fetch mode must be <code>'fetch_mode' => PDO::FETCH_CLASS</code>
     * @depends PDO::FETCH_CLASS 
     */
    public function edit() {
        $cls = get_called_class();
        $obj = new $cls;
        $table = self::getTableName($cls);
        if (isset($obj->table) && !empty($obj->table)) {
            $table = $obj->table;
        }
        $id = DB::getInstance($cls)->exec("show columns from {$table} where `Key` = 'PRI'")[0]->Field;
        self::update($this->columns, "{$id} = ?", [$this->columns[$id]]);
    }

    /**
     * used for deleting the row that holds current column values listed in columns[]
     * <b>NOTE:</b> the fetch mode must be <code>'fetch_mode' => PDO::FETCH_CLASS</code>
     * @depends PDO::FETCH_CLASS 
     */
    public function remove() {
        $cls = get_called_class();
        $obj = new $cls;
        $table = self::getTableName($cls);
        if (isset($obj->table) && !empty($obj->table)) {
            $table = $obj->table;
        }
        $id = DB::getInstance($cls)->exec("show columns from {$table} where `Key` = 'PRI'")[0]->Field;
        self::delete("{$id} = ?", [$this->columns[$id]]);
    }

    /**
     * Returns array of results with limit 
     * @param int $limit max number of result to be returned
     * @return array results
     */
    public static function all($limit = 0) {
        return self::where(null, null, $limit);
    }

    /**
     * Returns the row with the passed id value
     * @param mixed $value the value to the id of the row be fetched
     * @return array|null|obj
     */
    public static function id($value) {
        $cls = get_called_class();
        $obj = new $cls;
        $table = self::getTableName($cls);
        if (isset($obj->table) && !empty($obj->table)) {
            $table = $obj->table;
        }
        $id = DB::getInstance($cls)->exec("show columns from {$table} where `Key` = 'PRI'")[0]->Field;
        return self::where("{$id} = ?", [$value])[0];
    }

    /**
     * Return array of results that satisfied the passed params 
     * @param bool $distinct 
     * @param array $columns array of column names
     * @param string $condition where condition 
     * @param array $placeholderVals array of the placeholders values if 
     * @param string $groupBy columns names to be grouped by
     * @param string $having having condition 
     * @param string $orderBy columns names and type of order to each column
     * @param int $limit the maximum number of  rows to be returned
     * @return array|null
     */
    public static function select($distinct = false, $columns = [], $condition = '', $placeholderVals = [], $groupBy = '', $having = '', $orderBy = '', $limit = 0) {
        $cls = get_called_class();
        $obj = new $cls;
        $table = self::getTableName($cls);
        if (isset($obj->table) && !empty($obj->table)) {
            $table = $obj->table;
        }
        return DB::getInstance($cls)->select($distinct, $table, $columns, $condition, $placeholderVals, $groupBy, $having, $orderBy, $limit);
    }

    /**
     * Return array of results that satisfied the where clause
     * @param string $condition
     * @param array $placeholderVals place holder values
     * @param int $limit
     * @return array|null
     */
    public static function where($condition = '', $placeholderVals = [], $limit = 0) {
        return self::select(false, null, $condition, $placeholderVals, null, null, null, $limit);
    }

    /**
     * Returns the results that satisfied the having clause
     * @param array $groubBy names of the columns to be grouped by
     * @param string $condition having clause
     * @param array $placeholderVals the values of the placeholders
     * @param int $limit max number of results to be returned
     * @return array|null
     */
    public static function having(array $groubBy = [], $condition = '', $placeholderVals = [], $limit = 0) {
        return self::select(false, null, null, $placeholderVals, implode(",", $groubBy), $condition, null, $limit);
    }

    /**
     * Returns the results that satisfied the columns order
     * @param string $columns names of the columns to be ordered by
     * @param int $limit max number of results to be returned
     * @return array|null
     */
    public static function orderBy($columns = '', $limit = 0) {
        return self::select(false, null, null, null, null, null, $columns, $limit);
    }

    /**
     * Returns the results grouped by columns names
     * @param string $columns names of the columns to be grouped by
     * @param int $limit max number of results to be returned
     * @return array|null
     */
    public static function groubBy($columns = [], $limit = 0) {
        return self::select(false, null, null, null, implode(",", $columns), null, null, $limit);
    }

    /**
     * Returns boolean that tells if any rows found that matches the passed columns  
     * @param array $columns names of the columns and its values
     * @return bool 
     */
    public static function findBy(array $columns) {
        $cols = array_keys($columns);
        $condition = implode(' = ? AND ', $cols) . ' = ?';
        if (!empty(self::where($condition, array_values($columns)))) {
            return true;
        }
        return false;
    }

    /**
     * Returns any rows that matches the passed columns  
     * @param array $columns names of the columns and its values
     * @return array|null 
     */
    public static function with(array $columns, $limit = 0) {
        $cols = array_keys($columns);
        $condition = implode(' = ? AND ', $cols) . ' = ?';
        return self::where($condition, array_values($columns),$limit);
    }

    /**
     * Inserting new row with passed column values
     * <b>NOTE:</b> the insertable[] must be intialized with names of the allowed columns to be inserted
     * @param type $columns
     * @return bool
     * @throws MassAssignException in case of not listed the columns names in insertable[] to be in serted
     */
    public static function insert(array $columns = []) {
        $cls = get_called_class();
        $obj = new $cls;
        $table = self::getTableName($cls);
        $insertable = [];
        if (isset($obj->table) && !empty($obj->table)) {
            $table = $obj->table;
        }

        // making sure that the user listed the allowed columns to be inserted 
        // in other words the allowed columns to be inserted must be listed in the insertable[]
        // assigning the defaults for each columns if exists
        if (isset($obj->insertable) && !empty($obj->insertable)) {
            foreach ($obj->insertable as $value) {
                if (isset($columns[$value])) { // if the column listed in $insertable[] array
                    $insertable[$value] = $columns[$value];
                } else if (isset($obj->defaults[$value])) { // if the column listed in imsertable[] and have default value in defaults[]
                    $insertable[$value] = $obj->defaults[$value];
                } else {
                    $insertable[$value] = null;
                }
            }
        } else {
            throw new Exception("MassAssignException");
        }

        DB::getInstance($cls)->insert($table, $insertable);
        self::$lastId[$cls] = DB::getInstance()->lastId();

        return !self::isError();
    }

    /**
     * Updating the rows that match the passed condition with the values of passed columns[]
     * if no condition passed all the table rows will be updated
     * @param array $columns
     * @param string $condition where clause
     * @param array $placeholderVals values of placeholders array
     * @return bool 
     */
    public static function update($columns = [], $condition = '', $placeholderVals = []) {
        $cls = get_called_class();
        $obj = new $cls;
        $table = self::getTableName($cls);
        if (isset($obj->table) && !empty($obj->table)) {
            $table = $obj->table;
        }

        // assigning the defaults for each columns if exists
        foreach ($obj->defaults as $key => $value) {
            if (isset($columns[$key]) && empty($columns[$key])) {
                $columns[$key] = $value;
            }
        }
        DB::getInstance($cls)->update($table, $columns, $condition, $placeholderVals);
        return !self::isError();
    }

    /**
     * Deleting the rows that match the passed condition 
     * if no passed condition all the table rows will be deleted
     * @param string $condition where clause
     * @param array $placeholderVals values of the placeholders array
     * @return bool
     */
    public static function delete($condition = '', $placeholderVals = []) {
        $cls = get_called_class();
        $obj = new $cls;
        $table = self::getTableName($cls);
        if (isset($obj->table) && !empty($obj->table)) {
            $table = $obj->table;
        }

        DB::getInstance($cls)->delete($table, $condition, $placeholderVals);
        return !self::isError();
    }

    /**
     * Returns the first row that matched the passed condition
     * @param string $condition where clause
     * @param array $placeholderVals values of the placeholders array
     * @return array|null
     */
    public static function first($condition = '', $placeholderVals = []) {
        $data = self::where($condition, $placeholderVals);
        if (empty($data)) {
            return null;
        }
        return $data[0];
    }

    /**
     * Returns the last row that matched the passed condition
     * @param string $condition where clause
     * @param array $placeholderVals values of the placeholders array
     * @return array|null
     */
    public static function last($condition = '', $placeholderVals = []) {

        $data = self::where($condition, $placeholderVals);
        if (empty($data)) {
            return null;
        }
        return $data[count($data) - 1];
    }

    /**
     * Updating the rows that match the passed condition with the values of passed columns[]
     * if no condition passed all the table rows will be updated
     * if the update failed a new row will be inserted with the current values of passed columns[]
     * @param array $columns
     * @param string $condition where clause
     * @param array $placeholderVals values of placeholders array
     * @return bool 
     */
    public static function updateOrInsert($columns = [], $condition = '', $placeholderVals = []) {
        if (!self::update($columns, $condition, $placeholderVals)) {
            return self::insert($columns);
        }
        return true;
    }

    /**
     * Checks if there's error or not
     * @return bool
     */
    public static function isError() {
        return DB::getInstance()->isError();
    }

    /**
     * Returns error information
     * @return array
     */
    public static function getError() {
        return DB::getInstance()->getError();
    }

    /**
     * Returns the rows count of the last fetched results
     * @return int 
     */
    public static function count() {
        return DB::getInstance()->count();
    }

    /**
     * Returns the last inserted id
     * @param string $name
     * @return mixed
     */
    public static function lastId() {
        $cls = get_called_class();
        if (isset(self::$lastId[$cls])) {
            return self::$lastId[$cls];
        }
        return null;
    }

    /**
     * Returns the table names based on the class name
     * @param string $cls
     * @return string 
     */
    private static function getTableName($cls) {
        $cls = explode('\\', $cls);
        $cls = str_replace("Model", '', $cls);
        $cls = strtolower($cls[count($cls) - 1]);
        return "{$cls}s";
    }

}
