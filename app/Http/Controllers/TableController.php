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
        return DataTables::of(SalesInvoice::all())->make(true);
    }

    public function cr_data(){
        return DataTables::of(CollectionReceipt::all())->make(true);
    }

    public function bs_data(){
        return DataTables::of(BillingStatement::all())->make(true);
    }

    public function or_data(){
        return DataTables::of(OfficialReceipt::all())->make(true);
    }

    public function dr_data(){
        return DataTables::of(DeliveryReceipt::all())->make(true);
    }
}
