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
        try{
            $data = SalesInvoice::whereIn('company', auth()->user()->companies->pluck('company'))
                ->orderBy('status', 'ASC')
                ->orderBy('stage', 'DESC')
                ->orderBy('updated_at','DESC')
                ->get();
            return DataTables::of($data)->make(true);
        }
        catch(\Exception $error){
            return response()->json(['error' => 'Table SI not found'], 500);
        }
    }

    public function cr_data(){
        try{
            $data = CollectionReceipt::whereIn('company', auth()->user()->companies->pluck('company'))
                ->orderBy('status', 'ASC')
                ->orderBy('stage', 'DESC')
                ->orderBy('updated_at','DESC')
                ->get();
            return DataTables::of($data)->make(true);
        }
        catch(\Exception $error){
            return response()->json(['error' => 'Table CR not found'], 500);
        }
    }

    public function bs_data(){
        try{
            $data = BillingStatement::whereIn('company', auth()->user()->companies->pluck('company'))
                ->orderBy('status', 'ASC')
                ->orderBy('stage', 'DESC')
                ->orderBy('updated_at','DESC')
                ->get();
            return DataTables::of($data)->make(true);
        }
        catch(\Exception $error){
            return response()->json(['error' => 'Table BS not found'], 500);
        }
    }

    public function or_data(){
        try{
            $data = OfficialReceipt::whereIn('company', auth()->user()->companies->pluck('company'))
                ->orderBy('status', 'ASC')
                ->orderBy('stage', 'DESC')
                ->orderBy('updated_at','DESC')
                ->get();
            return DataTables::of($data)->make(true);
        }
        catch(\Exception $error){
            return response()->json(['error' => 'Table OR not found'], 500);
        }

    }

    public function dr_data(){
        try{
            $data = DeliveryReceipt::whereIn('company', auth()->user()->companies->pluck('company'))
                ->orderBy('status', 'ASC')
                ->orderBy('stage', 'DESC')
                ->orderBy('updated_at','DESC')
                ->get();
            return DataTables::of($data)->make(true);
        }
        catch(\Exception $error){
            return response()->json(['error' => 'Table DR not found'], 500);
        }
    }
}
