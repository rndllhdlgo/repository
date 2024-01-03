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

    public function index(){
        if(auth()->user()->department != 'WAREHOUSE'){
            return redirect('/si');
        }
        else{
            return redirect('/dr');
        }
    }

    public function logs(){
        return view('pages.logs');
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