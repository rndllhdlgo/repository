<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\CollectionReceipt;
use DB;

class PageController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    private function get_count($type){
        $current_role = Role::where('id', auth()->user()->userlevel)->first()->name;
        $datas = DB::table($type)->whereIn('company', auth()->user()->companies->pluck('company'))->get();
        $count = 0;

        foreach($datas as $data){
            $status = $data->status;
            $stage = $data->stage;
            if($status == 'FOR VALIDATION'){
                if($current_role == 'BOSS' || $current_role == 'VIEWER'){
                    $count+=0;
                }
                else if(($stage == '1' && $current_role == 'ENCODER') || ($stage == '1' && $current_role == 'ADMIN')){
                    $count++;
                }
                else if($stage == '0' && $current_role == 'ENCODER'){
                    $count++;
                }
                else if($stage == '0' && $current_role == 'ADMIN'){
                    $count++;
                }
            }
            else if($status == 'INVALID'){
                if($current_role == 'ENCODER'){
                    $count++;
                }
            }
            else if($status == 'FOR CORRECTION' && $current_role == 'ADMIN'){
                $count++;
            }
        }

        return $count;
    }

    public function si(){
        $si_count = $this->get_count('sales_invoices');
        $cr_count = $this->get_count('collection_receipts');
        $bs_count = $this->get_count('billing_statements');
        $or_count = $this->get_count('official_receipts');
        $dr_count = $this->get_count('delivery_receipts');
        if(auth()->user()->department == 'WAREHOUSE'){
            return redirect('/');
        }
        else{
            return view('pages.si',compact('si_count','cr_count','bs_count','or_count','dr_count'));
        }
    }

    public function cr(){
        $si_count = $this->get_count('sales_invoices');
        $cr_count = $this->get_count('collection_receipts');
        $bs_count = $this->get_count('billing_statements');
        $or_count = $this->get_count('official_receipts');
        $dr_count = $this->get_count('delivery_receipts');
        if(auth()->user()->department == 'WAREHOUSE'){
            return redirect('/');
        }
        else{
            return view('pages.cr',compact('si_count','cr_count','bs_count','or_count','dr_count'));
        }
    }

    public function bs(){
        $si_count = $this->get_count('sales_invoices');
        $cr_count = $this->get_count('collection_receipts');
        $bs_count = $this->get_count('billing_statements');
        $or_count = $this->get_count('official_receipts');
        $dr_count = $this->get_count('delivery_receipts');
        if(auth()->user()->department == 'WAREHOUSE'){
            return redirect('/');
        }
        else{
            return view('pages.bs',compact('si_count','cr_count','bs_count','or_count','dr_count'));
        }
    }

    public function or(){
        $si_count = $this->get_count('sales_invoices');
        $cr_count = $this->get_count('collection_receipts');
        $bs_count = $this->get_count('billing_statements');
        $or_count = $this->get_count('official_receipts');
        $dr_count = $this->get_count('delivery_receipts');
        if(auth()->user()->department == 'WAREHOUSE'){
            return redirect('/');
        }
        else{
            return view('pages.or',compact('si_count','cr_count','bs_count','or_count','dr_count'));
        }
    }

    public function dr(){
        $si_count = $this->get_count('sales_invoices');
        $cr_count = $this->get_count('collection_receipts');
        $bs_count = $this->get_count('billing_statements');
        $or_count = $this->get_count('official_receipts');
        $dr_count = $this->get_count('delivery_receipts');
        return view('pages.dr',compact('si_count','cr_count','bs_count','or_count','dr_count'));
    }
}
