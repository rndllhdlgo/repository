<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RepositoryController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function si(){
        return view('pages.si');
    }
    public function dr(){
        return view('pages.dr');
    }
}
