<?php

namespace App\Http\Controllers;

use App\Libs\Concretes\Controller;
use function twig;

class HomeController extends Controller {

    public function index() {
        return twig('index.html');
    }

}
