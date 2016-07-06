<?php

namespace App\Models;

use App\Libs\Concretes\Model;
use App\Libs\Statics\Hash;
use Carbon\Carbon;
use function slugify;

class UserModel extends Model {

    public $defaults;
    public $insertable = [
        'name',
        'email',
        'pass',
        'mobile',
        'tel',
        'address',
        'hash',
        'gender',
        'created_at',
        'updated_at',
    ];

    public function __construct() {
        $this->defaults = [
            'gender' => 'male',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    public function hasRole($email, $role) {
        return self::findBy(['email' => $email, 'role' => $role]);
    }

    // Helper methods
    public static function getSlug($name) {
        $slug = slugify($name);
        $new_slug = $slug;
        while (self::findBy(['slug' => $new_slug])) {
            $new_slug = $slug . '-' . rand(0, 99999);
        }
        return $new_slug;
    }

    public static function getHash() {
        $hash = Hash::unique(30);
        while (self::findBy(['hash' => $hash])) {
            $hash = Hash::unique(30);
        }
        return $hash;
    }

    public static function isUserExist($email) {
        return self::findBy(['email' => $email]);
    }

}
