<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\QueryException;

use App\Models\User;
use App\Models\Role;
use App\Models\UserLogs;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

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

    public function index(){
        if(auth()->user()->department != 'WAREHOUSE'){
            return redirect('/si');
        }
        else{
            return redirect('/dr');
        }
    }

    public function logs(){
        $si_count = $this->get_count('sales_invoices');
        $cr_count = $this->get_count('collection_receipts');
        $bs_count = $this->get_count('billing_statements');
        $or_count = $this->get_count('official_receipts');
        $dr_count = $this->get_count('delivery_receipts');
        return view('pages.logs',compact('si_count','cr_count','bs_count','or_count','dr_count'));
    }

    public function index_data(){
        try{
            $list = UserLogs::query();
            if(auth()->user()->department == 'ACCOUNTING' || auth()->user()->department == 'WAREHOUSE'){
                $list->where('role', 'LIKE', auth()->user()->department.' - %');
            }
            $list->orderBy('user_logs.id', 'DESC');
            return DataTables::of($list)->make(true);
        }
        catch(\QueryException $th){
            return response()->json(['error' => 'Table logs not found'], 500);
        }
    }

    public function checkURL(Request $request){
        return Http::head(env('APP_URL').substr($request->file_url, 1))->successful() ? 'true' : 'false';
    }
}