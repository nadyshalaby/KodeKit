<?php

namespace App\Models;

use App\Libs\Concretes\Model;
use Carbon\Carbon;

class MessageModel extends Model {

    public $defaults;
    public $insertable = [
        'complain_id',
        'user_id',
        'title',
        'body',
        'report',
        'viewed',
        'created_at',
        'updated_at',
    ];

    public function __construct() {
        $this->defaults = [
            'viewed' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

}
