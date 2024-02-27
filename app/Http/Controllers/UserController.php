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
use App\Models\CompanyPermission;
use App\Models\Company;
use Illuminate\Database\QueryException;
use DB;
class UserController extends Controller
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

    public function users(Request $request){
        $si_count = $this->get_count('sales_invoices');
        $cr_count = $this->get_count('collection_receipts');
        $bs_count = $this->get_count('billing_statements');
        $or_count = $this->get_count('official_receipts');
        $dr_count = $this->get_count('delivery_receipts');
        if(auth()->user()->userlevel == '1'){
            return view('pages/users',compact('si_count','cr_count','bs_count','or_count','dr_count'));
        }
        else{
            return redirect('/');
        }
    }

    public function users_data(){
        try{
            $list = User::query()->selectRaw('users.id, users.id AS user_id, users.name AS user_name, users.department, users.userlevel, users.status AS user_status, users.email AS user_email, roles.name AS role_name, roles.id AS role')
            ->join('roles', 'roles.id', 'users.userlevel')
            ->with([
                'companies' => function ($query){
                    $query->select('company_id','company');
                }
            ]);
            if(auth()->user()->department != 'SUPERUSER'){
                $list->where('department', auth()->user()->department);
            }
        $list->where('users.status', '!=', 'DELETED')
            ->orderBy('users.status', 'ASC')
            ->orderBy('role_name', 'ASC')
            ->orderBy('user_name', 'ASC')
            ->orderBy('users.id', 'ASC')
            ->get();
        return DataTables::of($list)->make(true);
        }
        catch(\QueryException $error){
            return response()->json(['error' => 'Table account not found'], 500);
        }
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
        $users->company = $request->company;
        $users->department = $request->department;
        $users->password = Hash::make($password);
        $users->userlevel = $request->role;
        $users->status = 'ACTIVE';
        $sql = $users->save();
        $id = $users->id;
        $users->assignRole($request->role);
        $company_id = explode(',', $request->company);
        foreach($company_id as $id){
            CompanyPermission::create(['user_id' => $users->id, 'company_id' => $id]);
        }

        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';

            try{
                Password::broker()->sendResetLink(['email'=>strtolower($request->email)]);
            }
            catch(\Exception $e){
                $result = 'true - unsent';
            }

            $userlogs = new UserLogs;
            $userlogs->username = auth()->user()->name;
            $userlogs->role = auth()->user()->department.' - '.Role::where('id', auth()->user()->userlevel)->first()->name;
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

        $changes = 0;
        if($name != $name_orig){
            $name_change = "【Full Name: FROM '$name_orig' TO '$name'】";
            $changes++;
        }
        else{
            $name_change = NULL;
        }
        if($email != $email_orig){
            $email_change = "【Email: FROM '$email_orig' TO '$email'】";
            $changes++;
        }
        else{
            $email_change = NULL;
        }

        $user = User::find($request->input('user_id'))->companies->pluck('company');
        $company_old = CompanyPermission::where('user_id', $request->input('user_id'))->count();
        CompanyPermission::where('user_id', $request->input('user_id'))->delete();
        $company_id = explode(',', $request->company);
        foreach($company_id as $id){
            CompanyPermission::create(['user_id' => $request->input('user_id'), 'company_id' => $id]);
        }
        $updated_user = User::find($request->input('user_id'))->companies->pluck('company');
        $company_new = CompanyPermission::where('user_id', $request->input('user_id'))->count();
        $companyChanges = $user->diff($updated_user);
        if($company_old < $company_new){
            $company_change = "【Company: FROM '$user' TO '$updated_user'】";
            $changes++;
        }
        else{
            if($companyChanges->isNotEmpty()){
                $company_change = "【Company: FROM '$user' TO '$updated_user'】";
                User::where('id', $request->input('user_id'))->update(['updated_at' => date('Y-m-d H:i:s')]);
                $changes++;
            }
            else{
                $company_change = NULL;
            }
        }

        if($department != $department_orig){
            $department_change = "【Department: FROM '$department_orig' TO '$department'】";
            $changes++;
        }
        else{
            $department_change = NULL;
        }
        if($userlevel != $userlevel_orig){
            $role_orig = Role::where('id', $userlevel_orig)->first()->name;
            $role_new = Role::where('id', $userlevel)->first()->name;
            $userlevel_change = "【User Level: FROM '$role_orig' TO '$role_new'】";
            $changes++;
        }
        else{
            $userlevel_change = NULL;
        }

        if($changes == 0){
            return response('NO CHANGES');
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
            $userlogs->role = auth()->user()->department.' - '.Role::where('id', auth()->user()->userlevel)->first()->name;
            $userlogs->activity = "UPDATED USER: User successfully updated details of $name with the following CHANGES: $name_change $email_change $company_change $department_change $userlevel_change.";
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
            $userlogs->role = auth()->user()->department.' - '.Role::where('id', auth()->user()->userlevel)->first()->name;
            $userlogs->activity = "USER UPDATED: User successfully updated details of $name with UserID#$request->id with the following CHANGES: $status.";
            $userlogs->save();
        }
        return response($result);
    }

    public function users_email(Request $request){
        $user = User::where('id', $request->uid)->first();
        if(!filter_var(strtolower($user->email), FILTER_VALIDATE_EMAIL)){
            return 'false';
        }
        try{
            $email = Password::broker()->sendResetLink(['email'=>strtolower($user->email)]);
        }
        catch(\Exception $e){
            return 'false';
        }
        if($email){
            $userlogs = new UserLogs;
            $userlogs->username = auth()->user()->name;
            $userlogs->role = auth()->user()->department.' - '.Role::where('id', auth()->user()->userlevel)->first()->name;
            $userlogs->activity = "EMAIL SENT: User successfully sent password reset email to $user->name.";
            $userlogs->save();
        }
        return $email ? 'true' : 'false';
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
            $userlogs->role = auth()->user()->department.' - '.Role::where('id', auth()->user()->userlevel)->first()->name;
            $userlogs->activity = "CHANGE PASSWORD: User successfully changed own account password.";
            $userlogs->save();
        }

        return response($result);
    }

    public function users_company(Request $request){
        $user = User::find('2');
        $companies = $user->companies->pluck('company');

        $company = Company::find('2');
        $users = $company->users;
        return $users->pluck('name');

        return $company = CompanyPermission::where('user_id', 1)->where('company_id', 2)->update(['company_id' => 1]);
    }
}
