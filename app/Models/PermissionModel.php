<?php

namespace App\Models;

use App\Libs\Concretes\Model;
use Carbon\Carbon;

class PermissionModel extends Model {

    public $defaults;
    public $insertable = [
        'user_id',
        'permission',
        'created_at',
        'updated_at',
    ];

    public function __construct() {
        $this->defaults = [
            'permission' => 'normal',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

}
