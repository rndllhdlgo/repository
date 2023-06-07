<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesInvoice;
use DataTables;

class TableController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function sales_invoice_data(){
        return DataTables::of(SalesInvoice::all())->make(true);
    }
}
