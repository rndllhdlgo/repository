<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function dr(){
        return view('pages.dr');
    }
}
