<?php

namespace App\Http\Controllers;

use Illuminate\Mail\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\UserLogs;

class UserController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function users(Request $request){
        if(auth()->user()->userlevel == '1'){
            return view('pages/users');
        }
        else{
            return redirect('/');
        }
    }

    public function users_data(){
        $list = User::query()->selectRaw('users.id AS user_id, users.name AS user_name, users.department, users.userlevel, users.status AS user_status, users.email AS user_email, roles.name AS role_name, roles.id AS role')
            ->join('roles', 'roles.id', 'users.userlevel');
            if(auth()->user()->department != 'ADMIN'){
                $list->where('department', auth()->user()->department);
            }
            $list->orderBy('user_status', 'ASC')
            ->orderBy('role_name', 'ASC')
            ->orderBy('user_name', 'ASC')
            ->orderBy('users.id', 'ASC')
            ->get();
        return DataTables::of($list)->make(true);
    }

    public function users_reload(){
        if(User::count() == 0){
            return 'NULL';
        }
        $data_update = User::latest('updated_at')->first()->updated_at;
        return $data_update;
    }

    public function validate_users_save(Request $request){
        $email = User::query()->select()
            ->where('email', $request->email)
            ->count();
        if($email > 0){
            $data = array('result' => 'duplicate');
            return response()->json($data);
        }
        else{
            $data = array('result' => 'true');
            return response()->json($data);
        }
    }

    public function users_save(Request $request){
        $char = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array();
        $charLength = strlen($char) - 1;
        for($i = 0; $i < 8; $i++){
            $n = rand(0, $charLength);
            $pass[] = $char[$n];
        }
        $password = implode($pass);

        $name = strtoupper($request->name);

        $users = new User;
        $users->name = $name;
        $users->email = strtolower($request->email);
        $users->department = $request->department;
        $users->password = Hash::make($password);
        $users->userlevel = $request->role;
        $users->status = 'ACTIVE';
        $sql = $users->save();
        $id = $users->id;
        $users->assignRole($request->role);

        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';

            Password::broker()->sendResetLink(['email'=>strtolower($request->email)]);

            $userlogs = new UserLogs;
            $userlogs->username = auth()->user()->name;
            $userlogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
            $userlogs->activity = "ADDED USER: User successfully saved details of $name with UserID#$id.";
            $userlogs->save();
        }

        return response($result);
    }

    public function validate_users_update(Request $request){
        $email1 = User::where('id', $request->user_id)->first()->email;
        $email2 = strtolower($request->email);
        if($email1 != $email2){
            $email = User::query()->select()
                ->where('email', $email2)
                ->count();
        }
        else{
            $email = 0;
        }
        if($email > 0){
            return response('duplicate');
        }
        else{
            return response('true');
        }
    }

    public function users_update(Request $request){

        $name = strtoupper($request->name);
        $email = strtolower($request->email);
        $department = $request->department;
        $userlevel = $request->role;

        $name_orig = User::where('id',$request->user_id)->first()->name;
        $email_orig = User::where('id',$request->user_id)->first()->email;
        $department_orig = User::where('id',$request->user_id)->first()->department;
        $userlevel_orig = User::where('id',$request->user_id)->first()->userlevel;

        if($name != $name_orig){
            $name_change = "【Full Name: FROM '$name_orig' TO '$name'】";
        }
        else{
            $name_change = NULL;
        }
        if($email != $email_orig){
            $email_change = "【Email: FROM '$email_orig' TO '$email'】";
        }
        else{
            $email_change = NULL;
        }
        if($department != $department_orig){
            $department_change = "【Department: FROM '$department_orig' TO '$department'】";
        }
        else{
            $department_change = NULL;
        }
        if($userlevel != $userlevel_orig){
            $role_orig = Role::where('id', $userlevel_orig)->first()->name;
            $role_new = Role::where('id', $userlevel)->first()->name;
            $userlevel_change = "【User Level: FROM '$role_orig' TO '$role_new'】";
        }
        else{
            $userlevel_change = NULL;
        }

        if($name_change == NULL && $email_change == NULL && $department_change == NULL && $userlevel_change == NULL){
            return response('no changes');
        }

        $users = User::find($request->input('user_id'));
        $users->name = $name;
        $users->department = $department;
        $users->email = $email;
        $users->userlevel = $userlevel;
        $sql = $users->save();
        $users->syncRoles($request->role);

        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';

            $userlogs = new UserLogs;
            $userlogs->username = auth()->user()->name;
            $userlogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
            $userlogs->activity = "UPDATED USER: User successfully updated details of $name with the following CHANGES: $name_change $email_change $department_change $userlevel_change.";
            $userlogs->save();
        }
        return response($result);
    }

    public function users_status(Request $request){
        if($request->status == 'ACTIVE'){
            $status1 = 'ACTIVE';
            $status2 = 'INACTIVE';
        }
        else{
            $status1 = 'INACTIVE';
            $status2 = 'ACTIVE';
        }
        $name = strtoupper($request->name);

        $users = User::find($request->id);
        $users->status = $request->status;
        $sql = $users->save();

        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';

            $status = "【Status: FROM '$status2' TO '$status1'】";

            $userlogs = new UserLogs;
            $userlogs->username = auth()->user()->name;
            $userlogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
            $userlogs->activity = "USER UPDATED: User successfully updated details of $name with UserID#$request->id with the following CHANGES: $status.";
            $userlogs->save();
        }
        return response($result);
    }

    public function change_validate(Request $request){
        return Hash::check($request->current, auth()->user()->password) ? 'true' : 'false';
    }

    public function change_password(Request $request){
        do{
            $users = User::find(auth()->user()->id);
            $users->password = Hash::make($request->new);
            $sql = $users->save();
        }
        while(!$sql);

        if(!$sql){
            $result = 'false';
        }
        else{
            $result = 'true';

            $userlogs = new UserLogs;
            $userlogs->username = auth()->user()->name;
            $userlogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
            $userlogs->activity = "CHANGE PASSWORD: User successfully changed own account password.";
            $userlogs->save();
        }

        return response($result);
    }
}
