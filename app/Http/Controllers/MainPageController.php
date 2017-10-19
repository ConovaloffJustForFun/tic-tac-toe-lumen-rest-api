<?php

namespace App\Http\Controllers;

class MainPageController extends Controller
{
    public function __construct()
    {
    }

    public function index() {
        return view('gameTable', ['name' => 'James']);
    }
}
