<div class="container-fluid text-default d-flex" style="height: 80px; line-height: 70px;">
	<a href="/" class="xD">
        <b class="text-default" style="font-size: 35px;">{{ env('APP_NAME') }}</b>
        <img class="mr-1" src="{{asset('image/banner.png')}}" style="margin-top: -17px;">
    </a>
	<table class="text-right ml-auto mb-2 align-self-end" style="font-size: 12px; line-height: 12px;">
		<thead>
			<tr>
				<td class="m-0 p-0">
					<span id="current_datetime">{{ Carbon\Carbon::now()->isoformat('dddd, MMMM DD, YYYY, h:mm:ss A') }}</span>
				</td>
				<td  class="m-0 p-0" rowspan="3">
					<i class="fa fa-user-circle fa-4x p-2" aria-hidden="true" role="button" onclick="$('#lblChangePassword').click()"></i>
				</td>
			</tr>
			<tr>
				<td class="m-0 p-0">
					<b>{{ auth()->user()->name }}</b>&nbsp;
                    [{{ App\Models\User::select('roles.name')->where('users.id', auth()->user()->id)->join('roles', 'roles.id', 'users.userlevel')->first()->name }}]
				</td>
			</tr>
			<tr>
				<td class="m-0 p-0">
					<span id="lblChangePassword" style="text-decoration: underline; cursor: pointer;">Change Password</span>
				</td>
			</tr>
		</thead>
	</table>
</div>
<nav class="navbar navbar-expand-sm bg-default w-100 navcontent" style="font-weight: bolder;">
	<div class="container-fluid">
		<ul class="navbar-nav">
            <li class="nav-item mr-1">
                <a class="nav-link {{ Request::is('si') ? 'navactive' : '' }}" href="/si">SALES INVOICE</a>
            </li>
            <li class="nav-item mr-1">
                <a class="nav-link {{ Request::is('dr') ? 'navactive' : '' }}" href="/dr">DELIVERY RECEIPT</a>
            </li>
            <li class="nav-item mr-1">
                <a class="nav-link {{ Request::is('users') ? 'navactive' : '' }}" href="/users">USER ACCOUNTS</a>
            </li>
			<li class="nav-item mr-1">
				<a class="nav-link {{ Request::is('logs') ? 'navactive' : '' }}" href="/logs"> USER LOGS</a>
			</li>
		</ul>
		<ul class="navbar-nav mr-right">
			<li class="nav-item">
				<a class="nav-link" href="/logout" style="font-size: 16px;" onclick="$('#loading').show();">
					LOGOUT<i class="fa fa-sign-out ml-2"></i>
				</a>
			</li>
		</ul>
	</div>
</nav>

<input type="hidden" id="current_user" value="{{auth()->user()->id}}" readonly>
<input type="hidden" id="current_role" value="{{ App\Models\User::select('roles.name')->where('users.id', auth()->user()->id)->join('roles', 'roles.id', 'users.userlevel')->first()->name }}" readonly>
<input type="hidden" id="current_date" value="{{date('Y-m-d')}}" readonly>
<input type="hidden" id="current_session" value="{{\Session::getId()}}" readonly>
<input type="hidden" id="current_token" value="{{\Illuminate\Support\Str::random(50)}}" readonly>
<input type="hidden" id="current_timeout" value="{{ env('APP_TIMEOUT') }}" readonly>