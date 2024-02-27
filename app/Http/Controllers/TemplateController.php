<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\IpExport;
use Maatwebsite\Excel\Facades\Excel;

class TemplateController extends Controller
{

    public function export_blade(){
        return view('template.export');
    }

    public function export_action(Request $request){
        try {
            return Excel::download(new IpExport($request), 'ip_address.xlsx');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
