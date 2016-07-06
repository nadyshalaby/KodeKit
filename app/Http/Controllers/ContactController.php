<?php

namespace App\Http\Controllers;

use App\Libs\Concretes\Controller;

class ContactController extends Controller {

    public function index() {
        return twig('contact.html');
    }

}
