<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function export_sample(){
        return view('template.export_sample');
    }
}
