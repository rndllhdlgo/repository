<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function si(){
        if(auth()->user()->department == 'WAREHOUSE'){
            return redirect('/');
        }
        else{
            return view('pages.si');
        }
    }

    public function cr(){
        if(auth()->user()->department == 'WAREHOUSE'){
            return redirect('/');
        }
        else{
            return view('pages.cr');
        }
    }

    public function bs(){
        if(auth()->user()->department == 'WAREHOUSE'){
            return redirect('/');
        }
        else{
            return view('pages.bs');
        }
    }

    public function or(){
        if(auth()->user()->department == 'WAREHOUSE'){
            return redirect('/');
        }
        else{
            return view('pages.or');
        }
    }

    public function dr(){
        return view('pages.dr');
    }
}
