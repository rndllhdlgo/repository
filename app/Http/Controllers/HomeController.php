<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

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

    public function index_data()
    {
        $list = UserLogs::query();
        if(auth()->user()->department == 'ACCOUNTING' || auth()->user()->department == 'WAREHOUSE'){
            $list->where('role', 'LIKE', auth()->user()->department.' - %');
        }
        $list->orderBy('user_logs.id', 'DESC');
        return DataTables::of($list)->make(true);
    }

    public function logs_reload(){
        $logs = UserLogs::select()->count();
        return $logs;
    }
}
