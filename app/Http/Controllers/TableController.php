<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\SalesInvoice;
use App\Models\CollectionReceipt;
use App\Models\BillingStatement;
use App\Models\OfficialReceipt;
use App\Models\DeliveryReceipt;
use DataTables;

class TableController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function si_data(){
        $data = SalesInvoice::whereIn('company', auth()->user()->companies->pluck('company'))
            ->orderBy('status', 'ASC')
            ->orderBy('stage', 'DESC')
            ->orderBy('updated_at','DESC')
            ->get();
        return DataTables::of($data)->make(true);
    }

    public function cr_data(){
        $data = CollectionReceipt::whereIn('company', auth()->user()->companies->pluck('company'))
            ->orderBy('status', 'ASC')
            ->orderBy('stage', 'DESC')
            ->orderBy('updated_at','DESC')
            ->get();
        return DataTables::of($data)->make(true);
    }

    public function bs_data(){
        $data = BillingStatement::whereIn('company', auth()->user()->companies->pluck('company'))
            ->orderBy('status', 'ASC')
            ->orderBy('stage', 'DESC')
            ->orderBy('updated_at','DESC')
            ->get();
        return DataTables::of($data)->make(true);
    }

    public function or_data(){
        $data = OfficialReceipt::whereIn('company', auth()->user()->companies->pluck('company'))
            ->orderBy('status', 'ASC')
            ->orderBy('stage', 'DESC')
            ->orderBy('updated_at','DESC')
            ->get();
        return DataTables::of($data)->make(true);
    }

    public function dr_data(){
        $data = DeliveryReceipt::whereIn('company', auth()->user()->companies->pluck('company'))
            ->orderBy('status', 'ASC')
            ->orderBy('stage', 'DESC')
            ->orderBy('updated_at','DESC')
            ->get();
        return DataTables::of($data)->make(true);
    }
}
