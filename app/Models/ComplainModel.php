<?php

namespace App\Models;

use App\Libs\Concretes\Model;
use Carbon\Carbon;

class ComplainModel extends Model {

    public $defaults;
    public $insertable = [
        'user_id',
        'diagnostic',
        'description',
        'status',
        'created_at',
        'updated_at',
    ];

    public function __construct() {
        $this->defaults = [
            'status' => 'bending',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

}
