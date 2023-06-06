<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

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
        $role = Role::query()->select()->get()->sortBy('name');
        return view('pages.logs', compact('role'));
    }

    public function index_data()
    {
        $list = UserLogs::query()
            ->select()
            ->orderBy('user_logs.id', 'DESC')->get();
        return DataTables::of($list)->make(true);
    }

    public function logs_reload(){
        $logs = UserLogs::select()->count();
        return $logs;
    }
}
