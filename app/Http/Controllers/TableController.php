<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesInvoice;
use App\Models\DeliveryReceipt;
use DataTables;

class TableController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function si_data(){
        return DataTables::of(SalesInvoice::all())->make(true);
    }

    public function dr_data(){
        return DataTables::of(DeliveryReceipt::all())->make(true);
    }
}
