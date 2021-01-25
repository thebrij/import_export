<div class="sidebar" data-color="orange">
    <!--
      Tip 1: You can change the color of the sidebar using: data-color="blue | green | orange | red | yellow"
  -->
    <div class="logo">
      <a href="/home" class="simple-text logo-mini">
        {{ __('IE') }}
      </a>
      <a href="/home" class="simple-text logo-normal">
        {{ __('Import Export') }}
      </a>
    </div>
    <div class="sidebar-wrapper" id="sidebar-wrapper">
      <ul class="nav">
        <li class="@if ($activePage == 'home') active @endif">
          <a href="{{ route('home') }}">
            <i class="now-ui-icons design_app"></i>
            <p>{{ __('Dashboard') }}</p>
          </a>
        </li>
        <li>
          <a data-toggle="collapse" href="#usernroles">
            <i class="fas fa-users-cog"></i>
            <p>
              {{ __("Users & Roles") }}
              <b class="caret"></b>
            </p>
          </a>
          <div class="collapse @if ($activePage == 'profile' or $activePage == 'users' or $activePage == 'roles' or $activePage == 'userdata') show @endif" id="usernroles">
            <ul class="nav">
              <li class="@if ($activePage == 'profile') active @endif">
                <a href="{{ route('profile.edit') }}">
                  <i class="now-ui-icons users_single-02"></i>
                  <p> {{ __("User Profile") }} </p>
                </a>
              </li>
              @if(Auth::user()->hasRole('Administrator') or Auth::user()->hasRole('Manager') or Auth::user()->hasRole('Supervisor'))
              <li class="@if ($activePage == 'users') active @endif">
                <a href="{{ route('user.index') }}">
                  <i class="fas fa-users"></i>
                  <p> {{ __("User Management") }} </p>
                </a>
              </li>
  
              <li class="@if ($activePage == 'roles') active @endif">
                <a href="{{ route('role.index') }}">
                  <i class="fas fa-dice-d20"></i>
                  <p> {{ __("Role Management") }} </p>
                </a>
              </li>
              {{--<li class="@if ($activePage == 'userdata') active @endif">
                <a href="{{ route('dataaccess.create') }}">
                    <i class="fas fa-dice-d20"></i>
                    <p> {{ __("Data Access") }} </p>
                </a>
              </li>--}}
              @endif
            </ul>
          </div>
        </li>
        @if(Auth::user()->hasRole('Administrator') or Auth::user()->hasRole('Manager') or Auth::user()->hasRole('Supervisor') or Auth::user()->hasRole('User'))
        <li>
          <a data-toggle="collapse" href="#imports">
            {{--<i class="fab fa-laravel"></i>--}}
            <i class="fas fa-file-import"></i>
            <p>
              {{ __("Imports") }}
              <b class="caret"></b>
            </p>
          </a>
          <div class="collapse @if ($activePage == 'importerbills' or $activePage == 'exporterbills') show @endif" id="imports">
            <ul class="nav">
              <li class="@if ($activePage == 'importerbills') active @endif">
                <a href="{{ route('importerbill.create') }}">
                  <i class="fas fa-file-upload"></i>
                  <p> {{ __("Importer Bills") }} </p>
                </a>
              </li>
              <li class="@if ($activePage == 'exporterbills') active @endif">
                <a href="{{ route('exporterbill.create') }}">
                  <i class="fas fa-file-upload"></i>
                  <p> {{ __("Exporter Bills") }} </p>
                </a>
              </li>
                <li class="@if ($activePage == 'ImportFiles') active @endif">
                    <a href="{{ route('importerbill-files.list') }}">
                        <i class="fas fa-file-upload"></i>
                        <p> {{ __("Import Bill Files") }} </p>
                    </a>
                </li>
            </ul>
          </div>
        </li>
        @endif
  
        @if (@$page == 'home')
            
       
        <li>
          <a data-toggle="collapse" href="#hscodes">
            <i class="fab fa-gg-circle"></i>
            <p>
              
              {{ __("Hs Code") }}
              <b class="caret"></b>
            </p>
          </a>
          <div class="collapse hide" id="hscodes">
            <input type="text" id="txtHsCodeFilter" class="form-control" {{--onkeyup="myFunction('myInput','nav_imp_hscode')"--}} placeholder="Search for names.." style="margin: 10px auto; width: 80%;">
            <ul id="nav_imp_hscode" class="nav">
              @php
                  $da_imp_cals = [];
                  $da_get_imp_cals = \App\Models\DataAccess::where('user_id', Auth::user()->id)->where('right_name', 'selimpcal')->get();
                  
                  $da_imp_hscodes = [];
                  $da_get_imp_hscodes = \App\Models\DataAccess::where('user_id', Auth::user()->id)->where('right_name', 'selimphscode')->get();
                  
                  $da_imp_ports = [];
                  $da_get_imp_ports = \App\Models\DataAccess::where('user_id', Auth::user()->id)->where('right_name', 'selimpport')->get();
  
              @endphp
  
              @if ($da_get_imp_cals->count())
                  @foreach ($da_get_imp_cals as $da_get_imp_cal)
                      @php
                          array_push($da_imp_cals, $da_get_imp_cal->right_option);
                      @endphp
                  @endforeach
              @endif
  
              @if (isset($da_get_imp_hscodes) and !empty($da_get_imp_hscodes))
                  @foreach ($da_get_imp_hscodes as $da_get_imp_hscode)
                      @php
                          array_push($da_imp_hscodes, $da_get_imp_hscode->right_option);
                      @endphp
                  @endforeach
              @endif
              @if ($da_get_imp_ports->count())
                  @foreach ($da_get_imp_ports as $da_get_imp_port)
                      @php
                          array_push($da_imp_ports, $da_get_imp_port->right_option);
                      @endphp
                  @endforeach
              @endif
  
              @if(Auth::user()->hasRole('Administrator') or Auth::user()->hasRole('Manager') or Auth::user()->hasRole('Supervisor'))
                @foreach(\App\Models\ImporterBill::select(DB::raw('count(*) as order_count, hs_code'))->groupBy('hs_code')->orderBy('order_count','DESC')->get() as $hscode)
                  <li class="@if ($activePage == $hscode->hs_code) active @endif">
                    <a href="#" class="mmhscode"  style="margin-top: 0px; margin-bottom: 0px; padding-top: 0px; padding-bottom: 0px; padding-left: 15px;">
                      <i class="fas fa-chevron-circle-right"></i>
                      <p><span>{{ $hscode->hs_code }}</span>  <span>({{ $hscode->order_count }})</span></p>
                    </a>
                  </li>
  
                @endforeach
              @else
  
                
              @endif
            </ul>
            <ul id="nav_exp_hscode" class="nav" style="display: none;">
                @php
                    $da_exp_cals = [];
                    $da_get_exp_cals = \App\Models\DataAccess::where('user_id', Auth::user()->id)->where('right_name', 'selexpcal')->get();
                    $da_exp_hscodes = [];
                    $da_get_exp_hscodes = \App\Models\DataAccess::where('user_id', Auth::user()->id)->where('right_name', 'selexphscode')->get();
                    $da_exp_ports = [];
                    $da_get_exp_ports = \App\Models\DataAccess::where('user_id', Auth::user()->id)->where('right_name', 'selexphscode')->get();
  
                    $da_exp_ports = [];
                    $da_get_exp_ports = \App\Models\DataAccess::where('user_id', Auth::user()->id)->where('right_name', 'selexpport')->get();
  
                @endphp
  
                @if ($da_get_exp_cals->count())
                    @foreach ($da_get_exp_cals as $da_get_exp_cal)
                        @php
                            array_push($da_exp_cals, $da_get_exp_cal->right_option);
                        @endphp
                    @endforeach
                @endif
  
                @if (isset($da_get_exp_hscodes) and !empty($da_get_exp_hscodes))
                    @foreach ($da_get_exp_hscodes as $da_get_exp_hscode)
                        @php
                            array_push($da_exp_hscodes, $da_get_exp_hscode->right_option);
                        @endphp
                    @endforeach
                @endif
                @if ($da_get_exp_ports->count())
                    @foreach ($da_get_exp_ports as $da_get_exp_port)
                        @php
                            array_push($da_exp_ports, $da_get_exp_port->right_option);
                        @endphp
                    @endforeach
                @endif
              @if(Auth::user()->hasRole('Administrator') or Auth::user()->hasRole('Manager') or Auth::user()->hasRole('Supervisor'))
                @foreach(\App\Models\ExporterBill::select(DB::raw('count(*) as order_count, hs_code'))->groupBy('hs_code')->orderBy('order_count','DESC')->get() as $hscode)
                  <li class="@if ($activePage == $hscode->hs_code) active @endif">
                    <a href="#" class="mmhscode"  style="margin-top: 0px; margin-bottom: 0px; padding-top: 0px; padding-bottom: 0px; padding-left: 15px;">
                      <i class="fas fa-chevron-circle-right"></i>
                      <p><span>{{ $hscode->hs_code }}</span>  <span>({{ $hscode->order_count }})</span></p>
                    </a>
                  </li>
                @endforeach
              @else
                
              @endif
            </ul>
          </div>
        </li>
  
  
        <li>
          <a data-toggle="collapse" href="#countries">
            <i class="fab fa-gg-circle"></i>
            <p>
              {{ __("Country") }}
              <b class="caret"></b>
            </p>
          </a>
          <div class="collapse hide" id="countries">
            <input type="text" id="txtCountriesFilter" class="form-control" {{--onkeyup="myFunction('myInput','nav_imp_hscode')"--}} placeholder="Search for names.." style="margin: 10px auto; width: 80%;">
            <ul id="nav_imp_country" class="nav">
              @foreach(\App\Models\ImporterBill::select(DB::raw('count(*) as order_count, origin_country'))
                          ->where(function ($query) use ($da_imp_cals) {
                              for ($i = 0; $i < count($da_imp_cals); $i++) {
                                  $query->orwhere([
                                      [DB::raw('YEAR(bill_of_entry_date)'), '=', date('Y', strtotime($da_imp_cals[$i]))],
                                      [DB::raw('MONTH(bill_of_entry_date)'), '=', (int)date('m', strtotime($da_imp_cals[$i]))],
                                  ]);
                              }
                          })
                          ->where(function ($query) use ($da_imp_hscodes) {
                              for ($i = 0; $i < count($da_imp_hscodes); $i++) {
                                  $query->orwhere('hs_code', 'LIKE', $da_imp_hscodes[$i] . '%');
                              }
                          })
                          ->groupBy('origin_country')
                          ->orderBy('order_count','DESC')
                          ->get() as $row)
                <li class="@if ($activePage == $row->origin_country) active @endif">
                  <a href="#" class="mmcountries" style="margin-top: 0px; margin-bottom: 0px; padding-top: 0px; padding-bottom: 0px; padding-left: 15px;">
                    <i class="fas fa-chevron-circle-right"></i>
                    <p><span>{{ $row->origin_country }}</span>  <span>({{ $row->order_count }})</span></p>
                  </a>
                </li>
              @endforeach
            </ul>
            <ul id="nav_exp_country" class="nav" style="display: none;">
              @foreach(\App\Models\ExporterBill::select(DB::raw('count(*) as order_count, foreign_country'))
                          ->where(function ($query) use ($da_exp_cals) {
                              for ($i = 0; $i < count($da_exp_cals); $i++) {
                                  $query->orwhere([
                                      [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($da_exp_cals[$i]))],
                                      [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($da_exp_cals[$i]))],
                                  ]);
                              }
                          })
                          ->where(function ($query) use ($da_exp_hscodes) {
                              for ($i = 0; $i < count($da_exp_hscodes); $i++) {
                                  $query->orwhere('hs_code', 'LIKE', $da_exp_hscodes[$i] . '%');
                              }
                          })
                          ->groupBy('foreign_country')
                          ->orderBy('order_count','DESC')
                          ->get() as $row)
                <li class="@if ($activePage == $row->foreign_country) active @endif">
                  <a href="#" class="mmcountries" style="margin-top: 0px; margin-bottom: 0px; padding-top: 0px; padding-bottom: 0px; padding-left: 15px;">
                    <i class="fas fa-chevron-circle-right"></i>
                    <p><span>{{ $row->foreign_country }}</span>  <span>({{ $row->order_count }})</span></p>
                  </a>
                </li>
              @endforeach
            </ul>
          </div>
        </li>
        <li>
          <a data-toggle="collapse" href="#ports">
            <i class="fab fa-gg-circle"></i>
            <p>
              {{ __("Port") }}
              <b class="caret"></b>
            </p>
          </a>
          <div class="collapse hide" id="ports">
            <input type="text" id="txtPortsFilter" class="form-control" {{--onkeyup="myFunction('myInput','nav_imp_hscode')"--}} placeholder="Search for names.." style="margin: 10px auto; width: 80%;">
            <ul id="nav_imp_port" class="nav">
              @foreach(\App\Models\ImporterBill::select(DB::raw('count(*) as order_count, indian_port'))
                          ->where(function ($query) use ($da_imp_cals) {
                              for ($i = 0; $i < count($da_imp_cals); $i++) {
                                  $query->orwhere([
                                      [DB::raw('YEAR(bill_of_entry_date)'), '=', date('Y', strtotime($da_imp_cals[$i]))],
                                      [DB::raw('MONTH(bill_of_entry_date)'), '=', (int)date('m', strtotime($da_imp_cals[$i]))],
                                  ]);
                              }
                          })
                          ->where(function ($query) use ($da_imp_hscodes) {
                              for ($i = 0; $i < count($da_imp_hscodes); $i++) {
                                  $query->orwhere('hs_code', 'LIKE', $da_imp_hscodes[$i] . '%');
                              }
                          })
                          ->where(function ($query) use ($da_imp_ports) {
                              for ($i = 0; $i < count($da_imp_ports); $i++) {
                                  $query->orwhere('indian_port', 'LIKE', $da_imp_ports[$i] . '%');
                              }
                          })
                          ->groupBy('indian_port')
                          ->orderBy('order_count','DESC')
                          ->get() as $row)
                <li class="@if ($activePage == $row->indian_port) active @endif">
                  <a href="#" class="mmports" style="margin-top: 0px; margin-bottom: 0px; padding-top: 0px; padding-bottom: 0px; padding-left: 15px;">
                    <i class="fas fa-chevron-circle-right"></i>
                    <p><span>{{ $row->indian_port }}</span>  <span>({{ $row->order_count }})</span></p>
                  </a>
                </li>
              @endforeach
            </ul>
            <ul id="nav_exp_port" class="nav" style="display: none;">
              @foreach(\App\Models\ExporterBill::select(DB::raw('count(*) as order_count, indian_port'))
                          ->where(function ($query) use ($da_exp_cals) {
                              for ($i = 0; $i < count($da_exp_cals); $i++) {
                                  $query->orwhere([
                                      [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($da_exp_cals[$i]))],
                                      [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($da_exp_cals[$i]))],
                                  ]);
                              }
                          })
                          ->where(function ($query) use ($da_exp_hscodes) {
                              for ($i = 0; $i < count($da_exp_hscodes); $i++) {
                                  $query->orwhere('hs_code', 'LIKE', $da_exp_hscodes[$i] . '%');
                              }
                          })
                          ->where(function ($query) use ($da_exp_ports) {
                              for ($i = 0; $i < count($da_exp_ports); $i++) {
                                  $query->orwhere('indian_port', 'LIKE', $da_exp_ports[$i] . '%');
                              }
                          })
                          ->groupBy('indian_port')
                          ->orderBy('order_count','DESC')
                          ->get() as $row)
                <li class="@if ($activePage == $row->indian_port) active @endif">
                  <a href="#" class="mmports" style="margin-top: 0px; margin-bottom: 0px; padding-top: 0px; padding-bottom: 0px; padding-left: 15px;">
                    <i class="fas fa-chevron-circle-right"></i>
                    <p><span>{{ $row->indian_port }}</span>  <span>({{ $row->order_count }})</span></p>
                  </a>
                </li>
              @endforeach
            </ul>
          </div>
        </li>
        <li>
          <a data-toggle="collapse" href="#units">
            <i class="fab fa-gg-circle"></i>
            <p>
              {{ __("Units") }}
              <b class="caret"></b>
            </p>
          </a>
          <div class="collapse hide" id="units">
            <input type="text" id="txtUnitsFilter" class="form-control" {{--onkeyup="myFunction('myInput','nav_imp_hscode')"--}} placeholder="Search for names.." style="margin: 10px auto; width: 80%;">
            <ul id="nav_imp_units" class="nav">
              @foreach(\App\Models\ImporterBill::select(DB::raw('count(*) as order_count, unit_quantity'))
                          ->where(function ($query) use ($da_imp_cals) {
                              for ($i = 0; $i < count($da_imp_cals); $i++) {
                                  $query->orwhere([
                                      [DB::raw('YEAR(bill_of_entry_date)'), '=', date('Y', strtotime($da_imp_cals[$i]))],
                                      [DB::raw('MONTH(bill_of_entry_date)'), '=', (int)date('m', strtotime($da_imp_cals[$i]))],
                                  ]);
                              }
                          })
                          ->where(function ($query) use ($da_imp_hscodes) {
                              for ($i = 0; $i < count($da_imp_hscodes); $i++) {
                                  $query->orwhere('hs_code', 'LIKE', $da_imp_hscodes[$i] . '%');
                              }
                          })
                          ->groupBy('unit_quantity')
                          ->orderBy('order_count','DESC')
                          ->get() as $row)
                <li class="@if ($activePage == $row->unit_quantity) active @endif">
                  <a href="#" class="mmunits" style="margin-top: 0px; margin-bottom: 0px; padding-top: 0px; padding-bottom: 0px; padding-left: 15px;">
                    <i class="fas fa-chevron-circle-right"></i>
                    <p><span>{{ $row->unit_quantity }}</span>  <span>({{ $row->order_count }})</span></p>
                  </a>
                </li>
              @endforeach
            </ul>
            <ul id="nav_exp_units" class="nav" style="display: none;">
              @foreach(\App\Models\ExporterBill::select(DB::raw('count(*) as order_count, unit_quantity'))
                          ->where(function ($query) use ($da_exp_cals) {
                              for ($i = 0; $i < count($da_exp_cals); $i++) {
                                  $query->orwhere([
                                      [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($da_exp_cals[$i]))],
                                      [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($da_exp_cals[$i]))],
                                  ]);
                              }
                          })
                          ->where(function ($query) use ($da_exp_hscodes) {
                              for ($i = 0; $i < count($da_exp_hscodes); $i++) {
                                  $query->orwhere('hs_code', 'LIKE', $da_exp_hscodes[$i] . '%');
                              }
                          })
                          ->groupBy('unit_quantity')
                          ->orderBy('order_count','DESC')
                          ->get() as $row)
                <li class="@if ($activePage == $row->unit_quantity) active @endif">
                  <a href="#" class="mmunits" style="margin-top: 0px; margin-bottom: 0px; padding-top: 0px; padding-bottom: 0px; padding-left: 15px;">
                    <i class="fas fa-chevron-circle-right"></i>
                    <p><span>{{ $row->unit_quantity }}</span>  <span>({{ $row->order_count }})</span></p>
                  </a>
                </li>
              @endforeach
            </ul>
          </div>
        </li>
   @endif
        
        {{--<li class="@if ($activePage == 'icons') active @endif">
          <a href="{{ route('page.index','icons') }}">
            <i class="now-ui-icons education_atom"></i>
            <p>{{ __('Icons') }}</p>
          </a>
        </li>
        <li class = "@if ($activePage == 'maps') active @endif">
          <a href="{{ route('page.index','maps') }}">
            <i class="now-ui-icons location_map-big"></i>
            <p>{{ __('Maps') }}</p>
          </a>
        </li>
        <li class = " @if ($activePage == 'notifications') active @endif">
          <a href="{{ route('page.index','notifications') }}">
            <i class="now-ui-icons ui-1_bell-53"></i>
            <p>{{ __('Notifications') }}</p>
          </a>
        </li>
        <li class = " @if ($activePage == 'table') active @endif">
          <a href="{{ route('page.index','table') }}">
            <i class="now-ui-icons design_bullet-list-67"></i>
            <p>{{ __('Table List') }}</p>
          </a>
        </li>
        <li class = "@if ($activePage == 'typography') active @endif">
          <a href="{{ route('page.index','typography') }}">
            <i class="now-ui-icons text_caps-small"></i>
            <p>{{ __('Typography') }}</p>
          </a>
        </li>--}}
      </ul>
  
    </div>
  </div>
  @push('js')
    <script>
        function myFunction(inputId, ulId) {
            // Declare variables
            var input, filter, ul, li, a, i, txtValue;
  
            input = document.getElementById(inputId);
            filter = input.value.toUpperCase();
            //ul = document.getElementById(ulId);
            ul = $('#'+ulId)
            li = ul.getElementsByTagName('li');
  
            // Loop through all list items, and hide those who don't match the search query
            for (i = 0; i < li.length; i++) {
                a = li[i].getElementsByTagName("a")[0];
                txtValue = a.textContent || a.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    li[i].style.display = "";
                } else {
                    li[i].style.display = "none";
                }
            }
        }
        $(document).ready(function() {
  
            $('#txtHsCodeFilter').keyup(function(){
                var filter_value = $(this).val();
                $('#hscodes ul > li').each(function() {
                    if ($(this).text().search(filter_value) == 61 || $(this).text().search(filter_value) == 0) {
                        $(this).show();
                    }
                    else {
                        $(this).hide();
                    }
                });
            });
            $('#txtCountriesFilter').keyup(function(){
                var f_value = $(this).val();
                //alert(f_value.toUpperCase());
                $('#countries ul > li').each(function() {
                    //alert($(this).text().search(f_value.toUpperCase()));
                    if ($(this).text().search(f_value.toUpperCase()) == 55 || $(this).text().search(f_value) == 61 || $(this).text().search(f_value.toUpperCase()) == 0) {
                        $(this).show();
                    }
                    else {
                        $(this).hide();
                    }
                });
            });
            $('#txtPortsFilter').keyup(function(){
                var f_value = $(this).val();
  
                $('#ports ul > li').each(function() {
  
                    if ($(this).text().search(f_value.toUpperCase()) == 55 || $(this).text().search(f_value) == 61 || $(this).text().search(f_value.toUpperCase()) == 0) {
                        $(this).show();
                    }
                    else {
                        $(this).hide();
                    }
                });
            });
            $('#txtUnitsFilter').keyup(function(){
                var f_value = $(this).val();
                $('#units ul > li').each(function() {
                    if ($(this).text().search(f_value.toUpperCase()) == 55 || $(this).text().search(f_value) == 61 || $(this).text().search(f_value.toUpperCase()) == 0) {
                        $(this).show();
                    }
                    else {
                        $(this).hide();
                    }
                });
            });
            $(document).on('click', '.mmhscode', function(event){
                event.preventDefault();
                //alert($(event.target).text());
                //alert($.trim($(event.target).text()).substring(0,1));
                if($(this).data('value') != '') {
  
                    $('#input-fhscode').val($.trim($(this).data('value')));
                    $('#input-fcountry').val('');
                    $('#input-fport').val('');
                    $('#input-funit').val('');
                    $("#frm_filter").submit();
                }
            });
            $(document).on('click', '.mmcountries', function(event){
                event.preventDefault();
                if($(this).data('value') != '') {
                    $('#input-fhscode').val('');
                    $('#input-fcountry').val($.trim($(this).data('value')));
                    $('#input-fport').val('');
                    $('#input-funit').val('');
                    $("#frm_filter").submit();
                }
            });
            $(document).on('click', '.mmports', function(event){
                event.preventDefault();
                //alert($(this).data('value'));
                if($(this).data('value') != '') {
                    $('#input-fhscode').val('');
                    $('#input-fcountry').val('');
                    $('#input-fport').val($.trim($(this).data('value')));
                    $('#input-funit').val('');
                    $("#frm_filter").submit();
                }
            });
            $(document).on('click', '.mmunits', function(event){
                event.preventDefault();
                if($(this).data('value') != '') {
                    $('#input-fhscode').val('');
                    $('#input-fcountry').val('');
                    $('#input-fport').val('');
                    $('#input-funit').val($.trim($(this).data('value')));
                    $("#frm_filter").submit();
                }
            });
        });
    </script>
  @endpush
  