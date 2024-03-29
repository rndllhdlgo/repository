<div class="container-fluid text-default d-flex" style="height: 80px; line-height: 70px;">
	<a href="/" class="xD">
        <b class="text-default" style="font-size: 35px;">{{ env('APP_NAME') }}</b>
        <img class="ml-2" src="{{asset('image/banner.png')}}" style="margin-top: -20px; zoom: 80%;">
    </a>
	<table class="text-right ml-auto mb-2 align-self-end" style="font-size: 12px; line-height: 12px;">
		<thead>
			<tr>
				<td class="m-0 p-0">
					<span id="current_datetime">{{ Carbon\Carbon::now()->isoformat('dddd, MMMM DD, YYYY, h:mm:ss A') }}</span>
				</td>
				<td  class="m-0 p-0" rowspan="3">
					<i class="fa fa-user-circle fa-4x p-2" aria-hidden="true" role="button" onclick="$('#lblChangePassword').click()" title="User ID: {{ auth()->user()->id }}&#10;Email: {{ auth()->user()->email }}"></i>
				</td>
			</tr>
			<tr id="current_companies" title="{{ implode(', ', auth()->user()->companies->pluck('company')->all())}}">
				<td class="m-0 p-0">
					<b>{{ auth()->user()->name }}</b>&nbsp;
                    [{{ auth()->user()->department }} / {{ App\Models\User::select('roles.name')->where('users.id', auth()->user()->id)->join('roles', 'roles.id', 'users.userlevel')->first()->name }}]
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
<nav class="navbar navbar-expand-sm bg-default w-100 navcontent" style="font-weight: bolder; zoom:90%;">
	<div class="container-fluid">
		@if(auth()->user()->department == 'SALES')
			<ul class="navbar-nav">
				<li class="nav-item mr-1">
					<a class="nav-link {{ Request::is('si') ? 'navactive' : '' }}" href="/si">SALES INVOICE<span id="si_notif" style="border: 1px solid white !important;" class="badge rounded-pill bg-danger ml-2"></span></a>
				</li>
				<li class="nav-item mr-1">
					<a class="nav-link {{ Request::is('dr') ? 'navactive' : '' }}" href="/dr">DELIVERY RECEIPT<span id="dr_notif" style="border: 1px solid white !important;" class="badge rounded-pill bg-danger ml-2"></span></a>
				</li>
			</ul>
		@else
			<ul class="navbar-nav">
				@if(auth()->user()->department != 'WAREHOUSE')
					<li class="nav-item mr-1">
						<a class="nav-link {{ Request::is('si') ? 'navactive' : '' }}" href="/si">SALES INVOICE<span id="si_notif" style="border: 1px solid white !important;" class="badge rounded-pill bg-danger ml-2">@if (auth()->user()->userlevel == "1") {{$si_count}} @endif</span></a>
					</li>
					<li class="nav-item mr-1">
						<a class="nav-link {{ Request::is('cr') ? 'navactive' : '' }}" href="/cr">COLLECTION RECEIPT<span id="cr_notif" style="border: 1px solid white !important;" class="badge rounded-pill bg-danger ml-2">@if (auth()->user()->userlevel == "1") {{$cr_count}} @endif</span></a>
					</li>
					<li class="nav-item mr-1">
						<a class="nav-link {{ Request::is('bs') ? 'navactive' : '' }}" href="/bs">BILLING STATEMENT<span id="bs_notif" style="border: 1px solid white !important;" class="badge rounded-pill bg-danger ml-2">@if (auth()->user()->userlevel == "1") {{$bs_count}} @endif</span></a>
					</li>
					<li class="nav-item mr-1">
						<a class="nav-link {{ Request::is('or') ? 'navactive' : '' }}" href="/or">OFFICIAL RECEIPT<span id="or_notif" style="border: 1px solid white !important;" class="badge rounded-pill bg-danger ml-2">@if (auth()->user()->userlevel == "1") {{$or_count}} @endif</span></a>
					</li>
				@endif
				<li class="nav-item mr-1">
					<a class="nav-link {{ Request::is('dr') ? 'navactive' : '' }}" href="/dr">DELIVERY RECEIPT<span id="dr_notif" style="border: 1px solid white !important;" class="badge rounded-pill bg-danger ml-2">@if (auth()->user()->userlevel == "1") {{$dr_count}} @endif</span></a>
				</li>
			</ul>
		@endif
		<ul class="navbar-nav mr-right">
			@role('ADMIN')
				<li class="nav-item mr-1">
					<a class="nav-link {{ Request::is('users') ? 'navactive' : '' }}" href="/users">ACCOUNTS</a>
				</li>
			@endrole
			<li class="nav-item mr-1">
				<a class="nav-link {{ Request::is('logs') ? 'navactive' : '' }}" href="/logs">LOGS</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="/logout" style="font-size: 16px;" onclick="$('#loading').show();">
					LOGOUT<i class="fa fa-sign-out ml-2"></i>
				</a>
			</li>
		</ul>
	</div>
</nav>
<input type="hidden" id="current_email" value="{{auth()->user()->email}}" readonly>
<input type="hidden" id="current_user" value="{{auth()->user()->id}}" readonly>
<input type="hidden" id="current_user_name" value="{{auth()->user()->name}}" readonly>
<input type="hidden" id="current_updated_at" value="{{(new DateTime(auth()->user()->updated_at))->modify('-8 hours')->format('Y-m-d\TH:i:s.u\Z')}}" readonly>
<input type="hidden" id="current_department" value="{{auth()->user()->department}}" readonly>
<input type="hidden" id="current_role" value="{{ App\Models\User::select('roles.name')->where('users.id', auth()->user()->id)->join('roles', 'roles.id', 'users.userlevel')->first()->name }}" readonly>
<input type="hidden" id="current_date" value="{{date('Y-m-d')}}" readonly>
<input type="hidden" id="current_session" value="{{\Session::getId()}}" readonly>
<input type="hidden" id="current_token" value="{{\Illuminate\Support\Str::random(50)}}" readonly>
<input type="hidden" id="current_timeout" value="{{ env('APP_TIMEOUT') }}" readonly>
<input type="hidden" id="current_mailserver" value="{{ env('MAIL_ENABLED') }}" readonly>