<?php

/**
 * This file is part of kodekit framework
 * 
 * @copyright (c) 2015-2016, nady shalaby
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Libs\Statics;

use App\Libs\Concretes\DB;
use function escape;

abstract class Validation {

    private static $_errors = [];

    /**
     * Checks if the given array follows the specified rules on each field passed.eg
     * <b>Example:</b>
     * <pre>
     * 	Validation::check($array,[
     * 	                              		'password' => [
     *    	                               		'required' => true,
     *                                                  'field' => 'nr_password', // st_password,nr_password,username,url,color,ip,tag,email,phone;
     * 		                               		'min' => 2,
     * 		                               		'max' => 20,
     * 		                               		'unique' => 'users',
     * 		                               		'alpha' =>ture,
     * 		                               		'alpha_space' =>ture,
     * 		                               		'unicode' =>ture,
     * 		                               		'unicode_space' =>ture,
     * 		                               		'num' =>ture,
     * 	 	                              		'alpha_num' => true,
     * 		                               		'regexp' =>'/[0-9]+/',
     * 	 	                              		'matches' => 'password_again',
     * 	 	                              		'equals' => ['password1','password2','password3'],
     * 		                               ],
     * 		                     ]);
     * 	if (Validation::passed()){
     * 		echo 'Ok';
     * 	}else{
     * 		echo '<pre>',print_r(Validation::getErrors()),'</pre>';
     * 	}
     * </pre>
     * @param array $data 
     * @param array $param_rules 
     * @return obj|boolean
     */
    public static function check(array $data, array $param_rules = []) {
        if (count($param_rules)) {
            self::$_errors = [];
            foreach ($param_rules as $param => $rules) {
                $param = escape(trim($param));
                $param_value = null;
                if (isset($data[$param])) {
                    $param_value = escape(trim($data[$param]));
                }
                $title = $param;
                if (isset($rules['title']) && !empty($rules['title'])) {
                    $title = $rules['title'];
                }
                foreach ($rules as $rule => $rule_value) {
                    switch ($rule) {
                        case 'required':
                            if ($rule_value === true && empty($param_value)) {
                                self::addError($param, "{$title} is required!");
                            }
                            break;
                        case 'min':
                            if ($rule_value && !empty($param_value) && strlen($param_value) < $rule_value) {
                                self::addError($param, "{$title} must be at least {$rule_value} chars!");
                            }
                            break;
                        case 'max':
                            if ($rule_value && !empty($param_value) && !empty($param_value) && strlen($param_value) > $rule_value) {
                                self::addError($param, "{$title} must be maximum {$rule_value} chars!");
                            }
                            break;
                        case 'matches':
                            if ($rule_value && !empty($param_value) && strcmp($param_value, $data[$rule_value]) != 0) {
                                $sec_title = $rule_value;
                                if (isset($param_rules[$rule_value]['title']) && !empty($param_rules[$rule_value]['title'])) {
                                    $sec_title = $param_rules[$rule_value]['title'];
                                }
                                self::addError($param, "{$title} & {$sec_title} don't match!");
                            }
                            break;
                        case 'equals':
                            if (count($rule_value) && !empty($param_value) && !in_array($param_value, $rule_value)) {
                                self::addError($param, "{$title} must be one of  [ " . implode(', ', $rule_value) . " ]!");
                            }
                            break;
                        case 'alpha':
                            if ($rule_value === true && !empty($param_value) && !empty($param_value) && !preg_match('/^[a-zA-Z]+$/', $param_value, $matches)) {
                                self::addError($param, "{$title} must be alphabetic chars!");
                            }
                            break;
                        case 'alpha_space':
                            if ($rule_value === true && !empty($param_value) && !empty($param_value) && !preg_match('/^[ a-zA-Z]+$/', $param_value, $matches)) {
                                self::addError($param, "{$title} must be alphabetic chars and spaces! ");
                            }
                            break;
                        case 'unicode':
                            if ($rule_value === true && !empty($param_value) && !empty($param_value) && !preg_match('/^[a-zA-Z\pL]+$/u', $param_value, $matches)) {
                                self::addError($param, "{$title} must be alphabetic chars!");
                            }
                            break;
                        case 'unicode_space':
                            if ($rule_value === true && !empty($param_value) && !empty($param_value) && !preg_match('/^[ a-zA-Z\pL]+$/u', $param_value, $matches)) {
                                self::addError($param, "{$title} must be alphabetic chars and spaces! ");
                            }
                            break;
                        case 'num':
                            if ($rule_value === true && !empty($param_value) && !ctype_digit($param_value)) {
                                self::addError($param, "{$title} must be numeric chars!");
                            }
                            break;
                        case 'alpha_num':
                            if ($rule_value === true && !empty($param_value) && !preg_match('/(?:[a-zA-Z]+[0-9 ]+)|(?:[0-9 ]+[a-zA-Z]+)/', $param_value)) {
                                self::addError($param, "{$title} must contain alphabetic and numeric chars!");
                            }
                            break;
                        case 'regexp':
                            if ($rule_value && !empty($param_value) && !preg_match($rule_value, $param_value)) {
                                self::addError($param, "{$title} must be matches this pattern {$rule_value} !");
                            }
                            break;
                        case 'unique':
                            if ($rule_value && !empty($param_value)) {
                                if (!empty(DB::getInstance()->select(false, $rule_value, null, "$param = ?", [$param_value]))) {
                                    self::addError($param, "{$title} already exists!");
                                }
                            }
                            break;
                        case 'field':
                            if (!empty($param_value)) {
                                switch ($rule_value) {
                                    case 'st_password':
                                        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/', $param_value)) {
                                            self::addError($param, "The password must contains at least one capital letter, one small letter, one digit, and one special character!");
                                        }
                                        break;
                                    case 'nr_password':
                                        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $param_value)) {
                                            self::addError($param, "The password must contains at least one capital letter, one small letter, one digit!");
                                        }
                                        break;
                                    case 'username':
                                        if (!preg_match('/^[a-zA-Z0-9_-]{3,20}$/', $param_value)) {
                                            self::addError($param, "The username may contains alphanumeric, dashes and underscores only , min = 3 and max = 20!");
                                        }
                                        break;
                                    case 'url':
                                        if (!preg_match('/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/', $param_value)) {
                                            self::addError($param, "The url is invalid!");
                                        }
                                        break;
                                    case 'color':
                                        if (!preg_match('/^#?([a-f0-9]{6}|[a-f0-9]{3})$/', $param_value)) {
                                            self::addError($param, "The color is invalid!");
                                        }
                                        break;
                                    case 'ip':
                                        if (!preg_match('/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/', $param_value)) {
                                            self::addError($param, "The ip is invalid!");
                                        }
                                        break;
                                    case 'tag':
                                        if (!preg_match('/^<([a-z]+)([^<]+)*(?:>(.*)<\/\1>|\s+\/>)$/', $param_value)) {
                                            self::addError($param, "The tag is invalid!");
                                        }
                                        break;
                                    case 'email':
                                        if (!preg_match('/([\w-\.]+)@((?:[\w]+\.)+)([a-zA-Z]{2,4})/', $param_value)) {
                                            self::addError($param, "The {$title} must be a valid one!");
                                        }
                                        break;
                                    case 'phone':
                                        if (!preg_match('%^(?:(?:\(?(?:00|\+)([1-4]\d\d|[1-9]\d?)\)?)?[\-\.\ \\\/]?)?((?:\(?\d{1,}\)?[\-\.\ \\\/]?){0,})(?:[\-\.\ \\\/]?(?:#|ext\.?|extension|x)[\-\.\ \\\/]?(\d+))?$%i', $param_value) || strlen($param_value) < 10) {
                                            self::addError($param, "The {$title} must be a valid one!");
                                        }
                                        break;
                                }
                            }
                            break;
                    }
                }
            }
        }
    }

    public static function passed() {
        if (empty(self::$_errors)) {
            return true;
        }
        return false;
    }

    private static function addError($field, $error) {
        self::$_errors[$field] [] = $error;
    }

    public static function getErrors($param = '') {
        if (!empty($param) && key_exists($param, self::$_errors)) {
            return self::$_errors[$param];
        }
        return array_values(self::$_errors);
    }

    public static function getAllErrorMsgs() {
        $msgs = [];
        foreach (self::$_errors as $param => $msg_arr) {
            foreach ($msg_arr as $msg) {
                $msgs [] = $msg;
            }
        }
        return $msgs;
    }

}
