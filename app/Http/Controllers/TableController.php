<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesInvoice;
use App\Models\CollectionReceipt;
use App\Models\BillingStatement;
use App\Models\OfficialReceipt;
use App\Models\DeliveryReceipt;
use DataTables;

class TableController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function si_data(){
        $data = SalesInvoice::orderBy('id', 'DESC')->get();
        return DataTables::of($data)->make(true);
    }

    public function cr_data(){
        $data = CollectionReceipt::orderBy('id', 'DESC')->get();
        return DataTables::of($data)->make(true);
    }

    public function bs_data(){
        $data = BillingStatement::orderBy('id', 'DESC')->get();
        return DataTables::of($data)->make(true);
    }

    public function or_data(){
        $data = OfficialReceipt::orderBy('id', 'DESC')->get();
        return DataTables::of($data)->make(true);
    }

    public function dr_data(){
        $data = DeliveryReceipt::orderBy('id', 'DESC')->get();
        return DataTables::of($data)->make(true);
    }
}
