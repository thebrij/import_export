@extends('layouts.app', [
    'namePage' => 'Dashboard',
    'class' => 'login-page sidebar-mini ',
    'activePage' => 'home',
    'backgroundImage' => asset('now') . "/img/bg15.jpg",
])

@section('content')
    <div class="ajax-loader">
        <img src="{{ asset('assets/img/ajax-loader.gif') }}" class="img-responsive" />
    </div>
  <div class="panel-header panel-header-md">

   


  <div class=""  style="margin: -20px 20px 10px 20px;">
    <div class="row">
      <div class="col-xl-12 order-xl-1">
        <div class="card">
          <div class="card-header">
            <h3 class="mb-0">{{ __('Search') }}</h3>
          </div>
          <div class="card-body">
            <form id="frm_filter" method="post" action="{{ route('importerbill.get_ajax') }}" autocomplete="off"
                  enctype="multipart/form-data">
              @csrf
              <div class="pl-lg-4">
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-2">
                    
                      <select id="input-rtype" name="rtype" class="form-control" >
                        <option value="Importer" {{  ("Importer" == old('rtype'))?'selected="selected"':'' }}>Importer</option>
                        <option value="Exporter" {{  ("Exporter" == old('rtype'))?'selected="selected"':'' }}>Exporter</option>
                      </select>
                    </div>
                    <div class="col-md-2">
                      <select id="input-fyear" name="fyear" class="form-control" required>
                        <option value="">Year</option>
                        {{--@if (Auth::user()->role_id >= 11 and Auth::user()->role_id <= 13)

                        <option value="2020" {{  ("2020" == old('fyear'))?'selected="selected"':'' }}>2020</option>
                        <option value="2019" {{  ("2019" == old('fyear'))?'selected="selected"':'' }}>2019</option>
                        <option value="2018" {{  ("2018" == old('fyear'))?'selected="selected"':'' }}>2018</option>
                        <option value="2017" {{  ("2017" == old('fyear'))?'selected="selected"':'' }}>2017</option>
                        <option value="2016" {{  ("2016" == old('fyear'))?'selected="selected"':'' }}>2016</option>
                        <option value="2015" {{  ("2015" == old('fyear'))?'selected="selected"':'' }}>2015</option>
                        <option value="2014" {{  ("2014" == old('fyear'))?'selected="selected"':'' }}>2014</option>
                        <option value="2013" {{  ("2013" == old('fyear'))?'selected="selected"':'' }}>2013</option>
                        <option value="2012" {{  ("2012" == old('fyear'))?'selected="selected"':'' }}>2012</option>
                        <option value="2011" {{  ("2011" == old('fyear'))?'selected="selected"':'' }}>2011</option>
                        <option value="2010" {{  ("2010" == old('fyear'))?'selected="selected"':'' }}>2010</option>
                        @else--}}
                          @if(isset($da_years) and !empty($da_years))
                            @foreach($da_years as $da_year)
                              <option value="{{ $da_year }}" {{($da_year == $last_year)?'selected="selected"':'' }} {{($da_year == old('fyear'))?'selected="selected"':'' }}>{{ $da_year }} </option>
                            @endforeach
                          @endif
                        {{--@endif--}}
                      </select>
                    </div>

                    

                    <div class="col-md-2">
                      <select id="input-fmonth" name="fmonth" class="form-control" >
                        <option value="" {{  ("" == old('fmonth'))?'selected="selected"':'' }}>Month</option>
                        @if (Auth::user()->role_id >= 11 and Auth::user()->role_id <= 13)
                        <option value="Jan" {{  ("Jan" == old('fmonth'))?'selected="selected"':'' }}>Jan</option>
                        <option value="Feb" {{  ("Feb" == old('fmonth'))?'selected="selected"':'' }}>Feb</option>
                        <option value="Mar" {{  ("Mar" == old('fmonth'))?'selected="selected"':'' }}>Mar</option>
                        <option value="Apr" {{  ("Apr" == old('fmonth'))?'selected="selected"':'' }}>Apr</option>
                        <option value="May" {{  ("May" == old('fmonth'))?'selected="selected"':'' }}>May</option>
                        <option value="Jun" {{  ("Jun" == old('fmonth'))?'selected="selected"':'' }}>Jun</option>
                        <option value="Jul" {{  ("Jul" == old('fmonth'))?'selected="selected"':'' }}>Jul</option>
                        <option value="Aug" {{  ("Aug" == old('fmonth'))?'selected="selected"':'' }}>Aug</option>
                        <option value="Sep" {{  ("Sep" == old('fmonth'))?'selected="selected"':'' }}>Sep</option>
                        <option value="Oct" {{  ("Oct" == old('fmonth'))?'selected="selected"':'' }}>Oct</option>
                        <option value="Nov" {{  ("Nov" == old('fmonth'))?'selected="selected"':'' }}>Nov</option>
                        <option value="Dec" {{  ("Dec" == old('fmonth'))?'selected="selected"':'' }}>Dec</option>
                        @else
                          @if(isset($da_months) and !empty($da_months))
                            @foreach($da_months as $da_month)
                              <option value="{{ $da_month->right_option }}" {{  ($da_month->right_option == old('fmonth'))?'selected="selected"':'' }}>{{ $da_month->description }}</option>
                            @endforeach
                          @endif
                        @endif
                      </select>
                    </div>
                    <div class="col-md-2 mt-1 mb-1">
                      <input type="text" name="fproduct" id="input-fproduct"  class="form-control" placeholder="{{ __('Product') }}" value="{{ old('fproduct') }}">
                    </div>
                    <div class="col-md-2 mt-1 mb-1">
                      <input type="text" name="fhscode" id="input-fhscode"  class="form-control" placeholder="{{ __('Hs Code') }}" value="{{ old('fhscode') }}">
                    </div>
                    <div class="col-md-2 mt-1 mb-1">
                      <input type="text" name="fchapter" id="input-fchapter"  class="form-control" placeholder="{{ __('Chapter') }}" value="{{ old('fchapter') }}">
                    </div>
                    <div class="col-md-2 mt-1 mb-1">
                      <input type="text" name="fcountry" id="input-fcountry"  class="form-control" placeholder="{{ __('Country') }}" value="{{ old('fcountry') }}">
                    </div>
                    <div class="col-md-2 mt-1 mb-1">
                      <input type="text" name="fport" id="input-fport"  class="form-control" placeholder="{{ __('Port') }}" value="{{ old('fport') }}">
                    </div>
                    <div class="col-md-2 mt-1 mb-1">
                      <input type="text" name="funit" id="input-funit"  class="form-control" placeholder="{{ __('Unit') }}" value="{{ old('funit') }}">
                    </div>
                    <div class="col-md-2 mt-1 mb-1">
                      <input type="text" name="fimpexpname" id="input-fimpexpname"  class="form-control" placeholder="{{ __('Imp/Exporter Name') }}" value="{{ old('fimpexpname') }}">
                    </div>
                    <div class="col-md-2 text-right">
                      <button type="submit" id="searchButton" class="btn btn-success" style="margin: 3px 1px; padding: 10px 10px;">{{ __('Search') }}</button>
                      <button type="reset" id="reset" class="btn btn-danger " style="margin: 3px 1px; padding: 10px 10px;">{{ __('Reset') }}</button>

                      

                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>



  <ul class="nav nav-tabs" id="myTab" role="tablist"  style="margin: -20px 20px 10px 20px;">
    <li class="nav-item">
      <a class="nav-link active" id="shipments-tab" data-toggle="tab" href="#shipments" role="tab" aria-controls="shipments" aria-selected="true">Shipments</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="dashboard-tab" data-toggle="tab" href="#dashboard" role="tab" aria-controls="dashboard" aria-selected="false">Dashboard</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="impanalysis-tab" data-toggle="tab" href="#impanalysis" role="tab" aria-controls="impanalysis" aria-selected="false">Importer Analysis</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="expanalysis-tab" data-toggle="tab" href="#expanalysis" role="tab" aria-controls="expanalysis" aria-selected="false">Exporter Analysis</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="marketshare-tab" data-toggle="tab" href="#marketshare" role="tab" aria-controls="marketshare" aria-selected="false">Market Share</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="priceana-tab" data-toggle="tab" href="#priceana" role="tab" aria-controls="priceana" aria-selected="false">Price Analysis</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="comparisontt-tab" data-toggle="tab" href="#comparisontt" role="tab" aria-controls="comparisontt" aria-selected="false">Comparison</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="gridsummaries-tab" data-toggle="tab" href="#gridsummaries" role="tab" aria-controls="gridsummaries" aria-selected="false">Grid Summaries</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="pricecompare-tab" data-toggle="tab" href="#pricecompare" role="tab" aria-controls="pricecompare" aria-selected="false">Price compare</a>
    </li>

  </ul>
  </div>
  <div class="tab-content" id="myTabContent">
    {{--Tab 1--}}
    <div class="tab-pane fade show active" id="shipments" role="tabpanel" aria-labelledby="shipments-tab">
      <div class="panel-header panel-header-xs">
      </div>
      <div class="content" style="margin: -20px 20px 10px 20px;">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title" style="margin-top:0px !important; margin-bottom:0px !important;"> Shipments</h4>
                  <div class="col-12 mt-2">
                      @include('alerts.success')
                      @include('alerts.errors')
                  </div>
              </div>
              <div class="card-body">
                <div id="dvDataTable" class="table-responsive">
                  <table class="table" id="importer-table">
                    <thead class=" text-primary"></thead>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
    {{--Tab 2 --}}
    <div class="tab-pane fade" id="dashboard"    role="tabpanel" aria-labelledby="dashboard-tab">
      <div class="panel-header panel-header-lg" id="bigDashboardChart_chart">
          <h2 id="dashboard_bigchart_h2" class="text-center text-white-50 mt-n5">Total Value of Top 15 <span id="dashboard_bigchart_h2_span">Importer</span> in USD</h2>
          <canvas id="bigDashboardChart" ></canvas>
      </div>

 
      <div class="text-center mb-2" id="loading-bigDashboardChart">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-chart">
                    <div class="card-body">
                        <img  src="{{asset('files/loader.gif')}}"/>
                    </div>
                </div>
            </div>
        </div>
    </div>



      <div class="content" style="margin: -20px 20px 10px 20px;">
        <div class="row">
          <div class="col-lg-6 col-md-6">
            <div class="card card-chart">
              <div class="card-body">
                <div class="chart-area">

                    <div id="myDiv" class="text-center mt-5" >
                        <img id="loading-container_top15_usd_port" src="{{asset('files/loader.gif')}}" style="display:none;"/>
                    </div>

                  <figure class="highcharts-figure" id="container_top15_usd_port_chart">
                    <div id="container_top15_usd_port"></div>
                  </figure>

                </div>
              </div>
              <div class="card-footer">
                <div class="stats">
                  <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6 col-md-6">
            <div class="card card-chart">
              <div class="card-body">
                <div class="chart-area">
                    <div id="myDiv" class="text-center mt-5" >
                        <img id="loading-chart_top15_usd_country" src="{{asset('files/loader.gif')}}" style="display:none;"/>
                    </div>

                  <figure class="highcharts-figure" id="chart_top15_usd_country_chart">
                    <div id="chart_top15_usd_country"></div>
                  </figure>
                </div>
              </div>
              <div class="card-footer">
                <div class="stats">
                  <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
                </div>
              </div>
            </div>
          </div>
        </div>
        {{--<div class="row">
          <div class="col-md-6">
            <div class="card  card-tasks">
              <div class="card-header ">
                <h5 class="card-category">Backend development</h5>
                <h4 class="card-title">Tasks</h4>
              </div>
              <div class="card-body ">
                <div class="table-full-width table-responsive">
                  <table class="table">
                    <tbody>
                    <tr>
                      <td>
                        <div class="form-check">
                          <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" checked>
                            <span class="form-check-sign"></span>
                          </label>
                        </div>
                      </td>
                      <td class="text-left">Sign contract for "What are conference organizers afraid of?"</td>
                      <td class="td-actions text-right">
                        <button type="button" rel="tooltip" title="" class="btn btn-info btn-round btn-icon btn-icon-mini btn-neutral" data-original-title="Edit Task">
                          <i class="now-ui-icons ui-2_settings-90"></i>
                        </button>
                        <button type="button" rel="tooltip" title="" class="btn btn-danger btn-round btn-icon btn-icon-mini btn-neutral" data-original-title="Remove">
                          <i class="now-ui-icons ui-1_simple-remove"></i>
                        </button>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="form-check">
                          <label class="form-check-label">
                            <input class="form-check-input" type="checkbox">
                            <span class="form-check-sign"></span>
                          </label>
                        </div>
                      </td>
                      <td class="text-left">Lines From Great Russian Literature? Or E-mails From My Boss?</td>
                      <td class="td-actions text-right">
                        <button type="button" rel="tooltip" title="" class="btn btn-info btn-round btn-icon btn-icon-mini btn-neutral" data-original-title="Edit Task">
                          <i class="now-ui-icons ui-2_settings-90"></i>
                        </button>
                        <button type="button" rel="tooltip" title="" class="btn btn-danger btn-round btn-icon btn-icon-mini btn-neutral" data-original-title="Remove">
                          <i class="now-ui-icons ui-1_simple-remove"></i>
                        </button>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="form-check">
                          <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" checked>
                            <span class="form-check-sign"></span>
                          </label>
                        </div>
                      </td>
                      <td class="text-left">Flooded: One year later, assessing what was lost and what was found when a ravaging rain swept through metro Detroit
                      </td>
                      <td class="td-actions text-right">
                        <button type="button" rel="tooltip" title="" class="btn btn-info btn-round btn-icon btn-icon-mini btn-neutral" data-original-title="Edit Task">
                          <i class="now-ui-icons ui-2_settings-90"></i>
                        </button>
                        <button type="button" rel="tooltip" title="" class="btn btn-danger btn-round btn-icon btn-icon-mini btn-neutral" data-original-title="Remove">
                          <i class="now-ui-icons ui-1_simple-remove"></i>
                        </button>
                      </td>
                    </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="card-footer ">
                <hr>
                <div class="stats">
                  <i class="now-ui-icons loader_refresh spin"></i> Updated 3 minutes ago
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5 class="card-category">All Persons List</h5>
                <h4 class="card-title"> Exporter Stats</h4>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table">
                    <thead class=" text-primary">
                    <th>
                      Name
                    </th>
                    <th>
                      Country
                    </th>
                    <th>
                      City
                    </th>
                    <th class="text-right">
                      Salary
                    </th>
                    </thead>
                    <tbody>
                    <tr>
                      <td>
                        Dakota Rice
                      </td>
                      <td>
                        Niger
                      </td>
                      <td>
                        Oud-Turnhout
                      </td>
                      <td class="text-right">
                        $36,738
                      </td>
                    </tr>
                    <tr>
                      <td>
                        Minerva Hooper
                      </td>
                      <td>
                        Curaçao
                      </td>
                      <td>
                        Sinaai-Waas
                      </td>
                      <td class="text-right">
                        $23,789
                      </td>
                    </tr>
                    <tr>
                      <td>
                        Sage Rodriguez
                      </td>
                      <td>
                        Netherlands
                      </td>
                      <td>
                        Baileux
                      </td>
                      <td class="text-right">
                        $56,142
                      </td>
                    </tr>
                    <tr>
                      <td>
                        Doris Greene
                      </td>
                      <td>
                        Malawi
                      </td>
                      <td>
                        Feldkirchen in Kärnten
                      </td>
                      <td class="text-right">
                        $63,542
                      </td>
                    </tr>
                    <tr>
                      <td>
                        Mason Porter
                      </td>
                      <td>
                        Chile
                      </td>
                      <td>
                        Gloucester
                      </td>
                      <td class="text-right">
                        $78,615
                      </td>
                    </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>--}}
      </div>
    </div>
    <div class="tab-pane fade" id="impanalysis"  role="tabpanel" aria-labelledby="impanalysis-tab">
      <div class="panel-header panel-header-lg">
        <h2 class="text-center text-white-50 mt-n5">Top 15 <span id="importerana_bigchart_h2_span">Importer</span> Companies Total Cost in USD</h2>
        <canvas id="bigImporterAnaChart"></canvas>
      </div>

      <div class="content" style="margin: -20px 20px 10px 20px;">
        <div class="row">
          <div class="col-lg-6 col-md-6">
        <div class="card card-chart">
            <div class="card-body">
                <div class="chart-area">
                    <div id="myDiv" class="text-center mt-5" >
                        <img id="loading-container_importerana_port" src="{{asset('files/loader.gif')}}" style="display:none;"/>
                    </div>

                    <figure class="highcharts-figure" id="container_importerana_port_chart">
                        <div id="container_importerana_port"></div>
                    </figure>
                </div>
            </div>
            <div class="card-footer">
                <div class="stats">
                    <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
                </div>
            </div>
        </div>
        </div>
          <div class="col-lg-6 col-md-6">
            <div class="card card-chart">
                <div class="card-body">
                    <div class="chart-area">
                        <div id="myDiv" class="text-center mt-5" >
                            <img id="loading-chart_importerana_country" src="{{asset('files/loader.gif')}}" style="display:none;"/>
                        </div>
                        <figure class="highcharts-figure" id="chart_importerana_country_chart">
                            
                            <div id="chart_importerana_country"></div>
                        </figure>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="stats">
                        <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
                    </div>
                </div>
            </div>
        </div>
        </div>
      </div>
    </div>
    <div class="tab-pane fade" id="expanalysis"  role="tabpanel" aria-labelledby="expanalysis-tab">
      <div class="panel-header panel-header-lg">
          <h2 class="text-center text-white-50 mt-n5">Top 15 <span id="exporterana_bigchart_h2_span">Suppliers</span> Total Cost in USD</h2>
          <canvas id="bigExporterAnaChart"></canvas>
      </div>

      <div class="content" style="margin: -20px 20px 10px 20px;">
          <div class="row">
              <div class="col-lg-6 col-md-6">
                  <div class="card card-chart">
                      <div class="card-body">
                          <div class="chart-area">
                            <div class="text-center mt-5" >
                                <img id="loading-container_expana_cost" src="{{asset('files/loader.gif')}}" style="display:none;"/>
                            </div>

                              <figure class="highcharts-figure" id="container_expana_cost_chart">
                                  <div id="container_expana_cost"></div>
                              </figure>
                          </div>
                      </div>
                      <div class="card-footer">
                          <div class="stats">
                              <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-lg-6 col-md-6">
                  <div class="card card-chart">
                      <div class="card-body">
                          <div class="chart-area">
                            <div class="text-center mt-5" >
                                <img id="loading-container_expana_quantity" src="{{asset('files/loader.gif')}}" style="display:none;"/>
                            </div>


                              <figure class="highcharts-figure" id="container_expana_quantity_chart">
                                  <div id="container_expana_quantity"></div>
                              </figure>
                          </div>
                      </div>
                      <div class="card-footer">
                          <div class="stats">
                              <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
    </div>
    <div class="tab-pane fade" id="marketshare"  role="tabpanel" aria-labelledby="marketshare-tab">
      <div class="panel-header panel-header-xs">
      </div>

      <div class="content" style="margin: -20px 20px 10px 20px;">
          <div class="row">
              <div class="col-lg-6 col-md-6">
                  <div class="card card-chart">
                      <div class="card-body">
                          <div class="chart-area">
                            <div class="text-center mt-5" >
                                <img id="loading-container_ms_cost_usd_port" src="{{asset('files/loader.gif')}}" style="display:none;"/>
                            </div>


                            <figure class="highcharts-figure" id="container_ms_cost_usd_port_chart">
                                <div id="container_ms_cost_usd_port"></div>
                            </figure>
                          </div>
                      </div>
                      <div class="card-footer">
                          <div class="stats">
                              <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-lg-6 col-md-6">
                  <div class="card card-chart">
                      <div class="card-body">
                          <div class="chart-area">
                            <div class="text-center mt-5" >
                                <img id="loading-container_ms_cost_qua_port" src="{{asset('files/loader.gif')}}" style="display:none;"/>
                                </div>

                              <figure class="highcharts-figure" id="container_ms_cost_qua_port_chart">
                               
                                  <div id="container_ms_cost_qua_port"></div>
                              </figure>
                          </div>
                      </div>
                      <div class="card-footer">
                          <div class="stats">
                              <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
        <div class="content" style="margin: -20px 20px 10px 20px;">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="card card-chart">
                        <div class="card-body">
                            <div class="chart-area">
                                <div class="text-center mt-5" >
                                    <img id="loading-container_ms_cost_qua_country" src="{{asset('files/loader.gif')}}" style="display:none;"/>
                                    </div>

                                <figure class="highcharts-figure" id="container_ms_cost_qua_country_chart">
                                    
                                    <div id="container_ms_cost_qua_country"></div>
                                </figure>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="card card-chart">
                        <div class="card-body">
                            <div class="chart-area">
                                <div class="text-center mt-5" >
                                    <img id="loading-container_ms_cost_usd_country" src="{{asset('files/loader.gif')}}" style="display:none;"/>
                                    </div>

                                <figure class="highcharts-figure" id="container_ms_cost_usd_country_chart">
                                   
                                    <div id="container_ms_cost_usd_country"></div>
                                </figure>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="tab-pane fade" id="priceana"     role="tabpanel" aria-labelledby="priceana-tab">
      <div class="panel-header panel-header-lg" style="padding:0 20px; height:450px;">
          {{--<h2 class="text-center text-white-50 mt-n5">Top 15 <span id="importerana_bigchart_h2_span">Importer</span> Companies Total Cost in USD</h2>--}}
          <figure class="highcharts-figure">
              <div id="container_priceana_usd_country"></div>
          </figure>
          
      </div>

      <div class="content" style="margin: -20px 20px 10px 20px;">
          <div class="row">
              <div class="col-lg-12 col-md-12">
                  <div class="card card-chart">
                      <div class="card-body">
                          <div class="chart-area">
                              <figure class="highcharts-figure" id="container_priceana_usd_port_chart">
                                  <div id="container_priceana_usd_port"></div>
                              </figure>

                              <div class="text-center mt-5" >
                                <img id="loading-container_priceana_usd_port" src="{{asset('files/loader.gif')}}" style="display:none;"/>
                            </div> 

                          </div>
                      </div>
                      <div class="card-footer">
                          <div class="stats">
                              <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <div class="row">
              <div class="col-lg-12 col-md-12">
                  <div class="card card-chart">
                      <div class="card-body">
                          <div class="chart-area">
                            <div class="text-center mt-5" >
                                <img id="loading-chart_priceana_usd_impexp" src="{{asset('files/loader.gif')}}" style="display:none;"/>
                            </div> 

                              <figure class="highcharts-figure" id="chart_priceana_usd_impexp_chart">
                                  <div id="chart_priceana_usd_impexp"></div>
                              </figure>
                          </div>
                      </div>
                      <div class="card-footer">
                          <div class="stats">
                              <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
    </div>
    <div class="tab-pane fade" id="comparisontt" role="tabpanel" aria-labelledby="comparisontt-tab">
      <div class="panel-header panel-header-lg" style="padding:0 20px; height:450px;">
          {{--<h2 class="text-center text-white-50 mt-n5">Top 15 <span id="importerana_bigchart_h2_span">Importer</span> Companies Total Cost in USD</h2>--}}
          <figure class="highcharts-figure" id="chart_comparisontt_usd_impexp_chart">
              <div id="chart_comparisontt_usd_impexp"></div>
          </figure>
          <div class="text-center mt-5" >
            <img id="loading-chart_comparisontt_usd_impexp" src="{{asset('files/loader.gif')}}" style="display:none;"/>
         </div>


      </div>

      <div class="content" style="margin: -20px 20px 10px 20px;">
          <div class="row">
              <div class="col-lg-12 col-md-12">
                  <div class="card card-chart">
                      <div class="card-body">
                          <div class="chart-area">
                              <figure class="highcharts-figure" id="chart_comparisontt_usd_country_chart">
                                  <div id="chart_comparisontt_usd_country"></div>
                              </figure>
                              <div class="text-center mt-5" >
                                <img id="loading-chart_comparisontt_usd_country" src="{{asset('files/loader.gif')}}" style="display:none;"/>
                             </div>

                          </div>
                      </div>
                      <div class="card-footer">
                          <div class="stats">
                              <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <div class="row">
              <div class="col-lg-12 col-md-12">
                  <div class="card card-chart">
                      <div class="card-body">
                          <div class="chart-area">
                              <figure class="highcharts-figure" id="chart_comparisontt_usd_ports_chart">
                                  <div id="chart_comparisontt_usd_ports"></div>
                              </figure>

                              <div class="text-center mt-5" >
                                <img id="loading-chart_comparisontt_usd_ports" src="{{asset('files/loader.gif')}}" style="display:none;"/>
                             </div>
                    
                          </div>
                      </div>
                      <div class="card-footer">
                          <div class="stats">
                              <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
    </div>
    <div class="tab-pane fade" id="gridsummaries" role="tabpanel" aria-labelledby="gridsummaries-tab">
      <div class="panel-header panel-header-xs">
          {{--<h2 class="text-center text-white-50 mt-n5">Top 15 <span id="importerana_bigchart_h2_span">Importer</span> Companies Total Cost in USD</h2>--}}
      </div>

      <div class="content" style="margin: -20px 20px 10px 20px;">
          <div class="row">
              <div class="col-md-12">
                  <div class="card">
                      <div class="card-header">
                          <h4 class="card-title">  HsCode 8 digit Summary</h4>
                      </div>
                      <div class="card-body" >
                          <div id="dvDataTableGSum8Digit" class="table-responsive">
                              <table id="gsum-8digit-table" class="table"  width="100%">
                                  <thead class=" text-primary"></thead>
                              </table>
                          </div>
                      </div>
                  </div>
              </div>

          </div>
          <div class="row">
              <div class="col-md-12">
                  <div class="card">
                      <div class="card-header">
                          <h4 class="card-title">  HsCode 2 digit Summary</h4>
                      </div>
                      <div class="card-body">
                          <div id="dvDataTableGSum2Digit" class="table-responsive">
                              <table id="gsum-2digit-table" class="table" width="100%" >
                                  <thead></thead>
                              </table>
                          </div>
                      </div>
                  </div>
              </div>

          </div>
          <div class="row">
              <div class="col-md-12">
                  <div class="card">
                      <div class="card-header">
                          <h4 class="card-title">  HsCode 4 digit Summary</h4>
                      </div>
                      <div class="card-body" >
                          <div id="dvDataTableGSum4Digit" class="table-responsive" style=" margin-right: 0px;">
                              <table id="gsum-4digit-table" class="table table-striped table-bordered" cellspacing="0" >
                                  <thead class=" text-primary"></thead>
                              </table>
                          </div>
                      </div>
                  </div>
              </div>

          </div>
          <div class="row">
              <div class="col-md-12">
                  <div class="card">
                      <div class="card-header">
                          <h4 class="card-title">  Port Summary</h4>
                      </div>
                      <div class="card-body" >
                          <div id="dvDataTableGSumPort" class="table-responsive" style=" margin-right: 0px;">
                              <table id="gsum-port-table" class="table table-striped table-bordered" cellspacing="0" >
                                  <thead class=" text-primary"></thead>
                              </table>
                          </div>
                      </div>
                  </div>
              </div>

          </div>
          <div class="row">
              <div class="col-md-12">
                  <div class="card">
                      <div class="card-header">
                          <h4 class="card-title">  Country Summary</h4>
                      </div>
                      <div class="card-body" >
                          <div id="dvDataTableGSumCountry" class="table-responsive" style=" margin-right: 0px;">
                              <table id="gsum-country-table" class="table table-striped table-bordered" cellspacing="0" >
                                  <thead class=" text-primary"></thead>
                              </table>
                          </div>
                      </div>
                  </div>
              </div>

          </div>
          <div class="row">
              <div class="col-md-12">
                  <div class="card">
                      <div class="card-header">
                          <h4 class="card-title">  Unit Summary</h4>
                      </div>
                      <div class="card-body" >
                          <div id="dvDataTableGSumUnit" class="table-responsive" style=" margin-right: 0px;">
                              <table id="gsum-unit-table" class="table table-striped table-bordered" cellspacing="0" >
                                  <thead class=" text-primary"></thead>
                              </table>
                          </div>
                      </div>
                  </div>
              </div>

          </div>
      </div>
    </div>


    <div class="tab-pane fade" id="pricecompare"  role="tabpanel" aria-labelledby="pricecompare-tab">
      <div class="panel-header panel-header-xs">
      </div>

      <div class="content" style="margin: -20px 20px 10px 20px;">
          <div class="row">
              <div class="col-lg-6 col-md-6">
                  <div class="card card-chart">
                      <div class="card-body">
                          <div class="chart-area">
                              <figure class="highcharts-figure" id="chart_pc_unit_usd_country_max_chart">
                                  <div id="chart_pc_unit_usd_country_max"></div>
                              </figure>
                            <div class="text-center mt-5" >
                                <img id="loading-chart_pc_unit_usd_country_max" src="{{asset('files/loader.gif')}}" style="display:none;"/>
                            </div>
                    
                          </div>
                      </div>
                      <div class="card-footer">
                          <div class="stats">
                              <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-lg-6 col-md-6">
                  <div class="card card-chart">
                      <div class="card-body">
                          <div class="chart-area">
                              <figure class="highcharts-figure" id="chart_pc_quantity_country_max_chart">
                                  <div id="chart_pc_quantity_country_max"></div>
                              </figure>
                              <div class="text-center mt-5" >
                                <img id="loading-chart_pc_quantity_country_max" src="{{asset('files/loader.gif')}}" style="display:none;"/>
                                </div>

                          </div>
                      </div>
                      <div class="card-footer">
                          <div class="stats">
                              <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
      <div class="content" style="margin: -20px 20px 10px 20px;">
          <div class="row">
              <div class="col-lg-6 col-md-6">
                  <div class="card card-chart">
                      <div class="card-body">
                          <div class="chart-area">
                              <figure class="highcharts-figure" id="chart_pc_unit_usd_country_min_chart" >
                                  <div id="chart_pc_unit_usd_country_min"></div>
                              </figure>
                              <div class="text-center mt-5" >
                                <img id="loading-chart_pc_unit_usd_country_min" src="{{asset('files/loader.gif')}}" style="display:none;"/>
                                </div>
                          </div>
                      </div>
                      <div class="card-footer">
                          <div class="stats">
                              <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-lg-6 col-md-6">
                  <div class="card card-chart">
                      <div class="card-body">
                          <div class="chart-area">
                              <figure class="highcharts-figure" id="chart_pc_quantity_country_min_chart" >
                                  <div id="chart_pc_quantity_country_min"></div>
                              </figure>
                              <div class="text-center mt-5" >
                                <img id="loading-chart_pc_quantity_country_min" src="{{asset('files/loader.gif')}}" style="display:none;"/>
                                </div>
                          </div>
                      </div>
                      <div class="card-footer">
                          <div class="stats">
                              <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <div class="row">
              <div class="col-lg-6 col-md-6">
                  <div class="card card-chart">
                      <div class="card-body">
                          <div class="chart-area">
                              <figure class="highcharts-figure" id="chart_pc_unit_usd_port_max_chart" >
                                  <div id="chart_pc_unit_usd_port_max"></div>
                              </figure>
                              <div class="text-center mt-5" >
                                <img id="loading-chart_pc_unit_usd_port_max" src="{{asset('files/loader.gif')}}" style="display:none;"/>
                                </div>
                          </div>
                      </div>
                      <div class="card-footer">
                          <div class="stats">
                              <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-lg-6 col-md-6">
                  <div class="card card-chart">
                      <div class="card-body">
                          <div class="chart-area">
                              <figure class="highcharts-figure" id="chart_pc_quantity_port_max_chart">
                                  <div id="chart_pc_quantity_port_max"></div>
                              </figure>
                              <div class="text-center mt-5" >
                                <img id="loading-chart_pc_quantity_port_max" src="{{asset('files/loader.gif')}}" style="display:none;"/>
                                </div>
                          </div>
                      </div>
                      <div class="card-footer">
                          <div class="stats">
                              <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <div class="row">
              <div class="col-lg-6 col-md-6">
                  <div class="card card-chart">
                      <div class="card-body">
                          <div class="chart-area">
                              <figure class="highcharts-figure" id="chart_pc_unit_usd_port_min_chart">
                                  <div id="chart_pc_unit_usd_port_min"></div>
                              </figure>
                              <div class="text-center mt-5" >
                                <img id="loading-chart_pc_unit_usd_port_min" src="{{asset('files/loader.gif')}}" style="display:none;"/>
                                </div>
                          </div>
                      </div>
                      <div class="card-footer">
                          <div class="stats">
                              <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-lg-6 col-md-6">
                  <div class="card card-chart">
                      <div class="card-body">
                          <div class="chart-area">
                              <figure class="highcharts-figure" id="chart_pc_quantity_port_min_chart">
                                  <div id="chart_pc_quantity_port_min"></div>
                              </figure>
                              <div class="text-center mt-5" >
                                <img id="loading-chart_pc_quantity_port_min" src="{{asset('files/loader.gif')}}" style="display:none;"/>
                                </div>
                          </div>
                      </div>
                      <div class="card-footer">
                          <div class="stats">
                              <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
    </div>

  </div>

@endsection

@push('js')

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script>
       
       

    Date.prototype.lastweek= function(wd, n){
        n= n || 1;
        return this.nextweek(wd, -n);
    }
    Date.prototype.nextweek= function(wd, n){
        if(n== undefined) n= 1;
        var incr= (n<0)? 1: -1,
        D= new Date(this),
        dd= D.getDay();
        if(wd=== undefined) wd= dd;
        if(dd!= wd) while(D.getDay()!= wd) D.setDate(D.getDate()+incr);
        D.setDate(D.getDate()+7*n);
        D.setHours(05, 0, 0, 0);
        return D;
    }


    function lastMondayinmonth(month, year){
        var day= new Date();
        if(!month) month= day.getMonth()+1;
        if(!year) year= day.getFullYear();
        day.setFullYear(year, month, 0);
        return new Date(day.lastweek(0));
    }
    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

    var myChartDashboard;
    var myChartImpAna, myChartExpAna;
    /* For Export Buttons available inside jquery-datatable "server side processing" - Start
    - due to "server side processing" jquery datatble doesn't support all data to be exported
    - below function makes the datatable to export all records when "server side processing" is on */

    function falseform(){
        $("#input-fmonth").attr("disabled", false);
        $("#input-fproduct").attr("disabled", false);
        $("#input-fhscode").attr("disabled", false);
        $("#input-fchapter").attr("disabled", false);
        $("#input-fcountry").attr("disabled", false);
        $("#input-fport").attr("disabled", false);
        $("#input-funit").attr("disabled", false);
        $("#input-fimpexpname").attr("disabled", false);
    }

    function trueform(){
        $("#input-fmonth").attr("disabled", true);
        $("#input-fproduct").attr("disabled", true);
        $("#input-fhscode").attr("disabled", true);
        $("#input-fchapter").attr("disabled", true);
        $("#input-fcountry").attr("disabled", true);
        $("#input-fport").attr("disabled", true);
        $("#input-funit").attr("disabled", true);
        $("#input-fimpexpname").attr("disabled", true);
    }
    function newexportaction(e, dt, button, config) {
        //   $('.ajax-loader').css("visibility", "visible");
          var self = this;
          var oldStart = dt.settings()[0]._iDisplayStart;
          dt.one('preXhr', function (e, s, data) {
              // Just this once, load all data from the server...
              data.start = 0;
              data.length = 2147483647;
              dt.one('preDraw', function (e, settings) {
                  // Call the original action function
                  if (button[0].className.indexOf('buttons-copy') >= 0) {
                      $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
                  } else if (button[0].className.indexOf('buttons-excel') >= 0) {
                      $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
                          $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
                          $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
                  } else if (button[0].className.indexOf('buttons-csv') >= 0) {
                      $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
                          $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config) :
                          $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
                  } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
                      $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
                          $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config) :
                          $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
                  } else if (button[0].className.indexOf('buttons-print') >= 0) {
                      $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
                  }
                  dt.one('preXhr', function (e, s, data) {
                      // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                      // Set the property to what it was before exporting.
                      settings._iDisplayStart = oldStart;
                      data.start = oldStart;
                  });
                  // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                  setTimeout(dt.ajax.reload, 0);
                  // Prevent rendering of the full data to the DOM
                //   $('.ajax-loader').css("visibility", "hidden");
                  return false;
              });
          });
          // Requery the server with the new one-time export settings
          dt.ajax.reload();
      };
    //For Export Buttons available inside jquery-datatable "server side processing" - End
    function newexportaction1 () {
        if ('Importer' == $('#input-rtype').val()) {
            if('{{ count($da_years) }}'>0) {
                var ajax_form_data = $('#frm_filter').serializeArray(); //Encode form elements for submission
                var ajax_url = '{!! route('importerbill.ajax_importer_export') !!}?' + $.param(ajax_form_data); //set form action url
                window.location = ajax_url;
            }
            else {
                alert('No Import data Year Found in rights');
            }
        }
        else if ('Exporter' == $('#input-rtype').val()) {
            if('{{ count($da_exp_years) }}'>0) {
                var ajax_form_data = $('#frm_filter').serializeArray(); //Encode form elements for submission
                var ajax_url = '{!! route('exporterbill.ajax_exporter_export') !!}?' + $.param(ajax_form_data); //set form action url
                window.location = ajax_url;
            }
            else {
                alert('No Export data Year Found in rights');
            }
        }
    }
    function sidebarupdate (form_data){
        var postData = {}; var data={};
        if(typeof form_data !== 'undefined') {
            postData = form_data.reduce(function(a, x) { a[x.name] = x.value; return a; }, {});
        } else {
            postData['_token'] = "{{ csrf_token() }}";
        }
        if($('#input-rtype').val() == 'Importer') {
            $.ajax({
                url: '{!! route('importerbill.get_ajax_side_bar') !!}',
                method: "POST",
                data: postData,
                success: function (data) {
                    if (typeof data['sb_hs_codes'] != "undefined" && data['sb_hs_codes'] != null && data['sb_hs_codes'].length != null && data['sb_hs_codes'].length > 0) {
                        $('#nav_imp_hscode li').remove();
                        $.each(data['sb_hs_codes'], function (index, value) {
                            $('#nav_imp_hscode').append($('<li class=""><a href="#" class="mmhscode" data-value="' + value.d_hs_code + '" style="margin-top: 0px; margin-bottom: 0px; padding-top: 0px; padding-bottom: 0px; padding-left: 15px;"><i class="fas fa-chevron-circle-right"></i><p><span>' + value.d_hs_code + '</span>  <span>(' + value.order_count + ')</span></p></a></li>'));
                        });
                    }
                    if (typeof data['sb_countries'] != "undefined" && data['sb_countries'] != null && data['sb_countries'].length != null && data['sb_countries'].length > 0) {
                        $('#nav_imp_country li').remove();
                        $.each(data['sb_countries'], function (index, value) {
                            $('#nav_imp_country').append($('<li class=""><a href="#" class="mmcountries" data-value="' + value.d_country + '" style="margin-top: 0px; margin-bottom: 0px; padding-top: 0px; padding-bottom: 0px; padding-left: 15px;"><i class="fas fa-chevron-circle-right"></i><p><span>' + value.d_country + '</span>  <span>(' + value.order_count + ')</span></p></a></li>'));
                        });
                    }
                    if (typeof data['sb_ports'] != "undefined" && data['sb_ports'] != null && data['sb_ports'].length != null && data['sb_ports'].length > 0) {
                        $('#nav_imp_port li').remove();
                        $.each(data['sb_ports'], function (index, value) {
                            $('#nav_imp_port').append($('<li class=""><a href="#" class="mmports" data-value="' + value.d_port + '" style="margin-top: 0px; margin-bottom: 0px; padding-top: 0px; padding-bottom: 0px; padding-left: 15px;"><i class="fas fa-chevron-circle-right" ></i><p><span>' + value.d_port + '</span>  <span>(' + value.order_count + ')</span></p></a></li>'));
                        });
                    }
                    if (typeof data['sb_units'] != "undefined" && data['sb_units'] != null && data['sb_units'].length != null && data['sb_units'].length > 0) {
                        $('#nav_imp_units li').remove();
                        $.each(data['sb_units'], function (index, value) {
                            $('#nav_imp_units').append($('<li class=""><a href="#" class="mmunits" data-value="' + value.d_unit + '" style="margin-top: 0px; margin-bottom: 0px; padding-top: 0px; padding-bottom: 0px; padding-left: 15px;"><i class="fas fa-chevron-circle-right"></i><p><span>' + value.d_unit + '</span>  <span>(' + value.order_count + ')</span></p></a></li>'));
                        });
                    }
                },
                error: function (data) {
                    console.log(data);
                }
            });
        }
        if($('#input-rtype').val() == 'Exporter') {
            $.ajax({
                url: '{!! route('exporterbill.get_ajax_side_bar') !!}',
                method: "POST",
                data: postData,
                success: function (data) {
                    console.dir(data);
                    if (typeof data['sb_hs_codes'] != "undefined" && data['sb_hs_codes'] != null && data['sb_hs_codes'].length != null && data['sb_hs_codes'].length > 0) {
                        //alert('in Hs Code');
                        $('#nav_exp_hscode li').remove();
                        $.each(data['sb_hs_codes'], function (index, value) {
                            $('#nav_exp_hscode').append($('<li class=""><a href="#" class="mmhscode" data-value="' + value.d_hs_code + '" style="margin-top: 0px; margin-bottom: 0px; padding-top: 0px; padding-bottom: 0px; padding-left: 15px;"><i class="fas fa-chevron-circle-right"></i><p><span>' + value.d_hs_code + '</span>  <span>(' + value.order_count + ')</span></p></a></li>'));
                        });
                    }
                    if (typeof data['sb_countries'] != "undefined" && data['sb_countries'] != null && data['sb_countries'].length != null && data['sb_countries'].length > 0) {
                        $('#nav_exp_country li').remove();
                        $.each(data['sb_countries'], function (index, value) {
                            $('#nav_exp_country').append($('<li class=""><a href="#" class="mmcountries" data-value="' + value.d_country + '" style="margin-top: 0px; margin-bottom: 0px; padding-top: 0px; padding-bottom: 0px; padding-left: 15px;"><i class="fas fa-chevron-circle-right"></i><p><span>' + value.d_country + '</span>  <span>(' + value.order_count + ')</span></p></a></li>'));
                        });
                    }
                    if (typeof data['sb_ports'] != "undefined" && data['sb_ports'] != null && data['sb_ports'].length != null && data['sb_ports'].length > 0) {
                        $('#nav_exp_port li').remove();
                        $.each(data['sb_ports'], function (index, value) {
                            $('#nav_exp_port').append($('<li class=""><a href="#" class="mmports" data-value="' + value.d_port + '" style="margin-top: 0px; margin-bottom: 0px; padding-top: 0px; padding-bottom: 0px; padding-left: 15px;"><i class="fas fa-chevron-circle-right"></i><p><span>' + value.d_port + '</span>  <span>(' + value.order_count + ')</span></p></a></li>'));
                        });
                    }
                    if (typeof data['sb_units'] != "undefined" && data['sb_units'] != null && data['sb_units'].length != null && data['sb_units'].length > 0) {
                        $('#nav_exp_units li').remove();
                        $.each(data['sb_units'], function (index, value) {
                            $('#nav_exp_units').append($('<li class=""><a href="#" class="mmunits" data-value="' + value.d_unit + '" style="margin-top: 0px; margin-bottom: 0px; padding-top: 0px; padding-bottom: 0px; padding-left: 15px;"><i class="fas fa-chevron-circle-right"></i><p><span>' + value.d_unit + '</span>  <span>(' + value.order_count + ')</span></p></a></li>'));
                        });
                    }
                },
                error: function (data) {
                    console.log(data);
                }
            });
        }
    }
    function importer_datatable(form_data) {
       
        
        form_data = JSON.parse(localStorage.getItem("search") || "[]");
        console.log(form_data);

        var postData = {}; var data={};
        if(localStorage.getItem("search") != null) {
            postData = form_data.reduce(function(a, x) { a[x.name] = x.value; return a; }, {});
        } else {
            postData['_token'] = "{{ csrf_token() }}";
        }
        
        var importer_table = $('#importer-table').DataTable({
            processing: true,
            serverSide: true,
            processData: false,
            contentType: false,
            enctype: 'multipart/form-data',
            ajax: {
                url:'{!! route('importerbill.get_ajax') !!}',
                type:'POST',
                data: postData,
                error: function (xhr, error, code)
                {
                    console.log(xhr);
                    console.log(code);
                },
                statusCode: {
                    401: function() {
                        window.location.href = 'login'; //or what ever is your login URI
                    },
                    419: function(){
                        window.location.href = 'login'; //or what ever is your login URI
                    }
                },
            },

            columns: [
                /*{ title: 'Id'                       , data: 'id'                       , name: 'id'},*/
                { title: ''                         , data: null, 'defaultContent': ''},
                { title: 'Bill of Entry No'         , data: 'bill_of_entry_no'         , name: 'bill_of_entry_no'     , "visible": false},
                { title: 'Reg Date'                 , data: 'bill_of_entry_date'       , name: 'bill_of_entry_date'   },
                { title: 'Importer ID'              , data: 'importer_id'              , name: 'importer_id'          , "visible": false},
                { title: 'Importer'                 , data: 'importer_name'            , name: 'importer_name'        },
                { title: 'Importer Address'         , data: 'importer_address'         , name: 'importer_address'     , "visible": false },
                { title: 'Importer City State'      , data: 'importer_city_state'      , name: 'importer_city_state'  , "visible": false },
                { title: 'PIN Code'                 , data: 'pin_code'                 , name: 'pin_code'             , "visible": false },
                { title: 'City'                     , data: 'city'                     , name: 'city'                 , "visible": false },
                { title: 'State'                    , data: 'state'                    , name: 'state'                , "visible": false },
                { title: 'Contact No'               , data: 'contact_no'               , name: 'contact_no'           , "visible": false },
                { title: 'Email'                    , data: 'email'                    , name: 'email'                , "visible": false },
                { title: 'Supplier'                 , data: 'supplier'                 , name: 'supplier'             },
                { title: 'Supplier Address'         , data: 'supplier_address'         , name: 'supplier_address'     , "visible": false },
                { title: 'Foreign Port'             , data: 'foreign_port'             , name: 'foreign_port'         },
                { title: 'Origin Country'           , data: 'origin_country'           , name: 'origin_country'       },
                { title: 'HS Code'                  , data: 'hs_code'                  , name: 'hs_code'              },
                { title: 'Chapter'                  , data: 'chapter'                  , name: 'chapter'              },
                { title: 'Product Discription'      , data: 'product_discription'      , name: 'product_discription'  },
                { title: 'Quantity'                 , data: 'quantity'                 , name: 'quantity'             },
                { title: 'Unit Quantity'            , data: 'unit_quantity'            , name: 'unit_quantity'        },
                { title: 'Unit value as per Invoice', data: 'unit_value_as_per_invoice', name: 'unit_value_as_per_invoice' },
                { title: 'Invoice Currency'         , data: 'invoice_currency'         , name: 'invoice_currency'     },
                { title: 'Total Value In FC'        , data: 'total_value_in_fc'        , name: 'total_value_in_fc'    },
                { title: 'Unit Rate in USD'         , data: 'unit_rate_in_usd'         , name: 'unit_rate_in_usd'     , "visible": false },
                { title: 'Exchange Rate'            , data: 'exchange_rate'            , name: 'exchange_rate'        , "visible": false },
                { title: 'Total Value USD (Exchange', data: 'total_value_usd_exchange' , name: 'total_value_usd_exchange' },
                { title: 'Unit_Price in INR'        , data: 'unit_price_in_inr'        , name: 'unit_price_in_inr'    },
                { title: 'Assess Value In INR'      , data: 'assess_value_in_inr'      , name: 'assess_value_in_inr'  },
                { title: 'Duty'                     , data: 'duty'                     , name: 'duty'                 , "visible": false },
                { title: 'CHA Number'               , data: 'cha_number'               , name: 'cha_number'           , "visible": false },
                { title: 'CHA Name'                 , data: 'cha_name'                 , name: 'cha_name'             , "visible": false },
                { title: 'Invoice No'               , data: 'invoice_no'               , name: 'invoice_no'           , "visible": false },
                { title: 'Item Number'              , data: 'item_number'              , name: 'item_number'          , "visible": false },
                { title: 'Be Type'                  , data: 'be_type'                  , name: 'be_type'              , "visible": false },
                { title: 'A Group'                  , data: 'a_group'                  , name: 'a_group'              , "visible": false },
                { title: 'Indian Port'              , data: 'indian_port'              , name: 'indian_port'          },
                { title: 'CUSH'                     , data: 'cush'                     , name: 'cush'                 , "visible": false },
                /*{ title: 'Created by', data: 'created_by'         , name: 'created_by', "visible": false },
                { title: 'Updated by', data: 'updated_by'         , name: 'updated_by', "visible": false },
                { title: 'Created at', data: 'created_at'         , name: 'created_at', "visible": false },
                { title: 'Updated at', data: 'updated_at'         , name: 'updated_at', "visible": false },*/
                /*{ title: 'Month'     , data: 'bill_of_entry_date'             , name: 'bill_of_entry_date', render: function ( data, type, row ) { var d = new Date(data); return monthNames[d.getMonth()]; }},
                { title: 'Year'      , data: 'bill_of_entry_date'             , name: 'bill_of_entry_date' , render: function ( data, type, row ) { var d = new Date(data); return d.getFullYear(); }}
*/
            ],
            columnDefs: [
                { width: 30, searchable: false, orderable: false, className: 'select-checkbox', targets: 0 },
                { width: 110, targets: 2 },
                { width: 200, targets: 4 },
                { width: 250, targets: 11 },
                { width: 200, targets: 12 },
                { width: 110, targets: 14 },
                { width: 130, targets: 15 },
                { width: 80, targets: 16 },
                { width: 80, targets: 17 },
                { width: 780, targets: 18 },
                { width: 100, targets: 19 },
                { width: 110, targets: 20 },
                { width: 200, targets: 21 },
                { width: 170, targets: 22 },
                { width: 120, targets: 23 },
                { width: 210, targets: 26 },
                { width: 140, targets: 27 },
                { width: 160, targets: 28 },
                { width: 250, targets: 36 },


            ],
            retrieve : true,
            select: {
                style:    'multi',
                selector: 'td:first-child'
            },
            scrollX:        true,
            scrollCollapse: true,
            fixedColumns: true,
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            dom: '<T<"clear">i<"clear">B<"clear">lf<rt>p>',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel-o" style="color: green;"></i> Download All',
                    titleAttr: 'Excel',
                    exportOptions: {
                        columns: ':not(:first-child)'
                    },
                    action: newexportaction1
                },
                /*{
                    extend: 'excel',
                    text: 'Download all',
                    exportOptions: {
                        modifier: {
                            // DataTables core
                            order : 'index',  // 'current', 'applied', 'index',  'original'
                            page : 'all',      // 'all',     'current'
                            search : 'none',     // 'none',    'applied', 'removed'
                            selected: null,
                        },
                        columns: ':not(:first-child)'
                    },
                    action: function ( e, dt, node, config ) {
                        var that = this, txt='';
                        // show sweetalert ...
                        importer_table.rows().select();

                        if(!$('.dataTables_scrollHeadInner table thead th.select-checkbox').parent().hasClass('selected')){
                            $('.dataTables_scrollHeadInner table thead th.select-checkbox').parent().addClass('selected')
                        }
                        if(dt.rows( '.selected' ).count()) {
                            if ('{{ Auth::user()->hasRole('Administrator') }}' == '1' || '{{ Auth::user()->hasRole('Manager') }}' == '1'
                                || '{{ Auth::user()->hasRole('Supervisor') }}' == '1') {
                                console.log('{{ Auth::user()->user_role['name'] }}');
                                console.log('is Administrator = {{ Auth::user()->hasRole('Administrator') }}');
                                console.log('is Manager = {{ Auth::user()->hasRole('Manager') }}');
                                console.log('is Supervisor = {{ Auth::user()->hasRole('Supervisor') }}');
                                setTimeout(function() {
                                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(that, e, dt, node, config);
                                    //hide sweetalert
                                }, 50);
                            }
                            else if ('{{ Auth::user()->hasRole('User') }}' == '1') {
                                console.log('is User = {{ Auth::user()->hasRole('User') }}');
                                var postData = {}; var data={};
                                postData['_token'] = "{{ csrf_token() }}";

                                $.ajax({
                                    url: '{!! route('importerbill.get_ajax_points_bal') !!}',
                                    method: "POST",
                                    data: postData,
                                    success: function (user_points) {
                                        console.log(user_points);
                                        if (user_points >= dt.rows('.selected').count()) {
                                            postData['drows'] = dt.rows('.selected').count();
                                            $('#nav_user_points').text($('#nav_user_points').text() - dt.rows('.selected').count() );
                                            $.ajax({
                                                url: '{!! route('importerbill.put_ajax_points') !!}',
                                                method: "POST",
                                                data: postData,
                                                success: function (data) {
                                                    console.log(data);
                                                },
                                                error: function (data) {
                                                    console.log(data);
                                                    alert('Error Updating Points.');
                                                }
                                            });
                                            setTimeout(function() {
                                                $.fn.dataTable.ext.buttons.excelHtml5.action.call(that, e, dt, node, config);
                                                //hide sweetalert
                                            }, 50);
                                        }
                                        else {
                                            alert('Insuficient Points');
                                        }
                                    },
                                    error: function (data) {
                                        console.log(data);
                                        alert('Error Getting Points.');
                                    }
                                });
                            }
                        }
                        else {
                            alert('No row selected');
                        }
                    }
                },*/
                {
                    extend: 'excel',
                    text: 'Download selected',
                    exportOptions: {
                        columns: ':not(:first-child)'
                    },
                    action: function ( e, dt, node, config ) {
                        var that = this, txt='';
                        // show sweetalert ...
                        if(dt.rows( '.selected' ).count()) {
                            if ('{{ Auth::user()->hasRole('Administrator') }}' == '1' || '{{ Auth::user()->hasRole('Manager') }}' == '1'
                                || '{{ Auth::user()->hasRole('Supervisor') }}' == '1') {
                                /*console.log('{{ Auth::user()->user_role['name'] }}');
                                console.log('is Administrator = {{ Auth::user()->hasRole('Administrator') }}');
                                console.log('is Manager = {{ Auth::user()->hasRole('Manager') }}');
                                console.log('is Supervisor = {{ Auth::user()->hasRole('Supervisor') }}');*/
                                setTimeout(function() {
                                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(that, e, dt, node, config);
                                    //hide sweetalert
                                }, 50);
                            }
                            else if ('{{ Auth::user()->hasRole('User') }}' == '1') {
                                //console.log('is User = {{ Auth::user()->hasRole('User') }}');
                                var postData = {}; var data={};
                                postData['_token'] = "{{ csrf_token() }}";

                                $.ajax({
                                    url: '{!! route('importerbill.get_ajax_points_bal') !!}',
                                    method: "POST",
                                    data: postData,
                                    success: function (user_points) {
                                        //console.log(user_points);
                                        if (user_points >= dt.rows('.selected').count()) {
                                            postData['drows'] = dt.rows('.selected').count();
                                            $('#nav_user_points').text($('#nav_user_points').text() - dt.rows('.selected').count() );
                                            $.ajax({
                                                url: '{!! route('importerbill.put_ajax_points') !!}',
                                                method: "POST",
                                                data: postData,
                                                success: function (data) {
                                                    console.log(data);
                                                },
                                                error: function (data) {
                                                    console.log(data);
                                                    alert('Error Updating Points.');
                                                }
                                            });
                                            setTimeout(function() {
                                                $.fn.dataTable.ext.buttons.excelHtml5.action.call(that, e, dt, node, config);
                                                //hide sweetalert
                                            }, 50);
                                        }
                                        else {
                                            alert('Insuficient Points');
                                        }
                                    },
                                    error: function (data) {
                                        console.log(data);
                                        alert('Error Getting Points.');
                                    }
                                });
                            }
                        }
                        else {
                            alert('No row selected');
                        }
                    }
                },

            ],
        });

        //importer_table
        $('.dataTables_scrollHeadInner table').on( 'click', 'thead th.select-checkbox', function () {
            if($(this).parent().hasClass('selected')){
                $(this).parent().removeClass('selected');
                importer_table.rows().deselect();
            }
            else {
                $(this).parent().addClass('selected');
                importer_table.rows().select();
            }
            console.log('Datatable Select All/None hit');
        } );
    }

    function importer_dashboard(form_data){

        // alert('importer_dashboard');
        form_data = JSON.parse(localStorage.getItem("search") || "[]");
        console.log(form_data);
       

        var postData = {}; var data={};
        if(localStorage.getItem("search") != null) {
            postData = form_data.reduce(function(a, x) { a[x.name] = x.value; return a; }, {});
        } else {
            postData['_token'] = "{{ csrf_token() }}";
        }
       
        $.ajax({
            url: '{!! route('importerbill.get_ajax_top_usd') !!}',
            method: "POST",
            data:postData,
            beforeSend: function() {
                $("#loading-bigDashboardChart").show();
                $("#bigDashboardChart_chart").hide();
            },
            success: function(data) {
                $("#loading-bigDashboardChart").hide();
                $("#bigDashboardChart_chart").show();
                localStorage.setItem("importer_dashboard_data",(localStorage.getItem("importer_dashboard_data")-1));

                console.log(data);
                var top15_usd = [];
                var top15_importers = [];

                for(var i in data.data) {
                    top15_importers.push(data.data[i].importer_name.substring(0, 8));
                    top15_usd.push(parseFloat(data.data[i].top15_usd));
                }
                /*console.log(top15_importers);
                console.log(top15_usd);*/
                $('#dashboard_bigchart_h2_span').html('Importers');

                var ctx = document.getElementById('bigDashboardChart').getContext("2d");

                var gradientStroke = ctx.createLinearGradient(500, 0, 100, 0);
                gradientStroke.addColorStop(0, '#80b6f4');
                gradientStroke.addColorStop(1, chartColor);

                var gradientFill = ctx.createLinearGradient(0, 200, 0, 50);
                gradientFill.addColorStop(0, "rgba(128, 182, 244, 0)");
                gradientFill.addColorStop(1, "rgba(255, 255, 255, 0.24)");

                if(myChartDashboard != null) // i initialize myBarChart var with null
                    myChartDashboard.destroy(); // if not null call destroy

                myChartDashboard = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        //labels: ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"],
                        labels: top15_importers,
                        datasets: [{
                            label: "Top 15 Importers by Value in USD",
                            borderColor: chartColor,
                            pointBorderColor: chartColor,
                            pointBackgroundColor: "#1e3d60",
                            pointHoverBackgroundColor: "#1e3d60",
                            pointHoverBorderColor: chartColor,
                            pointBorderWidth: 1,
                            pointHoverRadius: 7,
                            pointHoverBorderWidth: 2,
                            pointRadius: 5,
                            fill: true,
                            backgroundColor: gradientFill,
                            borderWidth: 2,
                            //data: [50, 150, 100, 190, 130, 90, 150, 160, 120, 140, 190, 95]
                            data: top15_usd
                        }]
                    },
                    options: {
                        layout: {
                            padding: {
                                left: 20,
                                right: 20,
                                top: 0,
                                bottom: 0
                            }
                        },
                        maintainAspectRatio: false,
                        tooltips: {
                            backgroundColor: '#fff',
                            titleFontColor: '#333',
                            bodyFontColor: '#666',
                            bodySpacing: 4,
                            xPadding: 12,
                            mode: "nearest",
                            intersect: 0,
                            position: "nearest"
                        },
                        legend: {
                            position: "bottom",
                            fillStyle: "#FFF",
                            display: false
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    fontColor: "rgba(255,255,255,0.4)",
                                    fontStyle: "bold",
                                    beginAtZero: true,
                                    maxTicksLimit: 5,
                                    padding: 10
                                },
                                gridLines: {
                                    drawTicks: true,
                                    drawBorder: false,
                                    display: true,
                                    color: "rgba(255,255,255,0.1)",
                                    zeroLineColor: "transparent"
                                }

                            }],
                            xAxes: [{
                                gridLines: {
                                    zeroLineColor: "transparent",
                                    display: false,

                                },
                                ticks: {
                                    padding: 10,
                                    fontColor: "rgba(255,255,255,0.4)",
                                    fontStyle: "bold"
                                }
                            }]
                        }
                    }
                });

                var cardStatsMiniLineColor = "#fff",
                    cardStatsMiniDotColor = "#fff";

            },
            error: function(data) {
                console.log(data);
            }
        });
        $.ajax({
            url: '{!! route('importerbill.get_ajax_top_usd_port') !!}',
            method: "POST",
            data:postData,
            beforeSend: function() {
                    $("#loading-container_top15_usd_port").show();
                    $("#container_top15_usd_port_chart").hide();
                },
            success: function(data) {
                $("#loading-container_top15_usd_port").hide();
                $("#container_top15_usd_port_chart").show();
                localStorage.setItem("importer_dashboard_data",(localStorage.getItem("importer_dashboard_data")-1));
                var chdata31 = []
                for(var i in data.data) {
                    chdata31.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
                }
                //console.log(chdata31);
               
                Highcharts.chart('container_top15_usd_port', {
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 45
                        }
                    },
                    title: {
                        text: 'Market Share by Cost Value in USD'
                    },
                    subtitle: {
                        text: 'Port Wise'
                    },
                    plotOptions: {
                        pie: {
                            innerSize: 100,
                            depth: 45
                        }
                    },
                    series: [{
                        name: 'Total Value USD',
                        data: chdata31
                    }]
                });
            },
            error: function(data) {
                console.log(data);
            }
        });
        $.ajax({
            url: '{!! route('importerbill.get_ajax_top_usd_country') !!}',
            method: "POST",
            data:postData,
            beforeSend: function() {
                    $("#loading-chart_top15_usd_country").show();
                    $("#chart_top15_usd_country_chart").hide();
                },
            success: function(data) {
                $("#loading-chart_top15_usd_country").hide();
                $("#chart_top15_usd_country_chart").show();
                localStorage.setItem("importer_dashboard_data",(localStorage.getItem("importer_dashboard_data")-1));
                var chdata= [];
                for(var i in data.data) {
                    chdata.push([data.data[i].origin_country, parseFloat(data.data[i].top15_usd)]);
                }
                //console.log(chdata);

                Highcharts.chart('chart_top15_usd_country', {
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 45
                        }
                    },
                    title: {
                        text: 'Market Share by Cost Value in USD'
                    },
                    subtitle: {
                        text: 'Country Wise'
                    },
                    plotOptions: {
                        pie: {
                            innerSize: 100,
                            depth: 45
                        }
                    },
                    series: [{
                        name: 'Total Value USD',
                        data: chdata
                    }]
                });
            },
            error: function(data) {
                console.log(data);
            }
        });


    }
    function importer_analysis(form_data){
        form_data = JSON.parse(localStorage.getItem("search") || "[]");
        console.log(form_data);

        var postData = {}; var data={};
        if(localStorage.getItem("search") != null) {
            postData = form_data.reduce(function(a, x) { a[x.name] = x.value; return a; }, {});
        } else {
            postData['_token'] = "{{ csrf_token() }}";
        }
        $.ajax({
            url: '{!! route('importerbill.get_ajax_impana_usd_comp') !!}',
            method: "POST",
            data:postData,
            success: function(data) {
                localStorage.setItem("importer_ImAna_data",(localStorage.getItem("importer_ImAna_data")-1));

                var impana_usd = [];
                var impana_importers = [];

                for(var i in data.data) {
                    impana_importers.push(data.data[i].importer_name.substring(0, 8));
                    impana_usd.push(parseFloat(data.data[i].top15_usd));
                }
                /*console.log(impana_importers);
                 console.log(impana_usd);*/
                $('#importerana_bigchart_h2_span').html('Importers');
                var ctx = document.getElementById('bigImporterAnaChart').getContext("2d");

                var gradientStroke = ctx.createLinearGradient(500, 0, 100, 0);
                gradientStroke.addColorStop(0, '#80b6f4');
                gradientStroke.addColorStop(1, chartColor);

                var gradientFill = ctx.createLinearGradient(0, 200, 0, 50);
                gradientFill.addColorStop(0, "rgba(128, 182, 244, 0)");
                gradientFill.addColorStop(1, "rgba(255, 255, 255, 0.24)");

                if(myChartImpAna != null) // i initialize myBarChart var with null
                    myChartImpAna.destroy(); // if not null call destroy

                myChartImpAna = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        //labels: ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"],
                        labels: impana_importers,
                        datasets: [{
                            label: "Top 15 Importer Companies Total Cost in USD",
                            borderColor: chartColor,
                            pointBorderColor: chartColor,
                            pointBackgroundColor: "#1e3d60",
                            pointHoverBackgroundColor: "#1e3d60",
                            pointHoverBorderColor: chartColor,
                            pointBorderWidth: 1,
                            pointHoverRadius: 7,
                            pointHoverBorderWidth: 2,
                            pointRadius: 5,
                            fill: true,
                            backgroundColor: gradientFill,
                            borderWidth: 2,
                            //data: [50, 150, 100, 190, 130, 90, 150, 160, 120, 140, 190, 95]
                            data: impana_usd
                        }]
                    },
                    options: {
                        layout: {
                            padding: {
                                left: 20,
                                right: 20,
                                top: 0,
                                bottom: 0
                            }
                        },
                        maintainAspectRatio: false,
                        tooltips: {
                            backgroundColor: '#fff',
                            titleFontColor: '#333',
                            bodyFontColor: '#666',
                            bodySpacing: 4,
                            xPadding: 12,
                            mode: "nearest",
                            intersect: 0,
                            position: "nearest"
                        },
                        legend: {
                            position: "bottom",
                            fillStyle: "#FFF",
                            display: false
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    fontColor: "rgba(255,255,255,0.4)",
                                    fontStyle: "bold",
                                    beginAtZero: true,
                                    maxTicksLimit: 5,
                                    padding: 10
                                },
                                gridLines: {
                                    drawTicks: true,
                                    drawBorder: false,
                                    display: true,
                                    color: "rgba(255,255,255,0.1)",
                                    zeroLineColor: "transparent"
                                }

                            }],
                            xAxes: [{
                                gridLines: {
                                    zeroLineColor: "transparent",
                                    display: false,

                                },
                                ticks: {
                                    padding: 10,
                                    fontColor: "rgba(255,255,255,0.4)",
                                    fontStyle: "bold"
                                }
                            }]
                        }
                    }
                });

                var cardStatsMiniLineColor = "#fff",
                    cardStatsMiniDotColor = "#fff";

            },
            error: function(data) {
                console.log(data);
            }
        });
        $.ajax({
            url: '{!! route('importerbill.get_ajax_impana_usd_cost') !!}',
            method: "POST",
            data:postData,
            beforeSend: function() {
                    $("#loading-container_importerana_port").show();
                    $("#container_importerana_port_chart").hide();
                },
            success: function(data) {
                $("#loading-container_importerana_port").hide();
                $("#container_importerana_port_chart").show();
                localStorage.setItem("importer_ImAna_data",(localStorage.getItem("importer_ImAna_data")-1));
                var chdata21 = [];
                for(var i in data.data) {
                    chdata21.push([data.data[i].importer_name, parseFloat(data.data[i].top15_usd)]);
                }
                //console.log(chdata21);

                Highcharts.chart('container_importerana_port', {
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 45
                        }
                    },
                    title: {
                        text: 'Importer Market Share total Cost'
                    },
                    subtitle: {
                        text: 'In USD'
                    },
                    plotOptions: {
                        pie: {
                            innerSize: 100,
                            depth: 45
                        }
                    },
                    series: [{
                        name: 'Total Value USD',
                        data: chdata21
                    }]
                });
            },
            error: function(data) {
                console.log(data);
            }
        });
        $.ajax({
            url: '{!! route('importerbill.get_ajax_impana_usd_quantity') !!}',
            method: "POST",
            data:postData,
            beforeSend: function() {
                    $("#loading-chart_importerana_country").show();
                    $("#chart_importerana_country_chart").hide();
                },

            success: function(data) {
                $("#loading-chart_importerana_country").hide();
                $("#chart_importerana_country_chart").show();
                localStorage.setItem("importer_ImAna_data",(localStorage.getItem("importer_ImAna_data")-1));
                var chdata22= [];
                for(var i in data.data) {
                    chdata22.push([data.data[i].importer_name, parseFloat(data.data[i].top_quantity)]);
                }
                //console.log(chdata22);

                Highcharts.chart('chart_importerana_country', {
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 45
                        }
                    },
                    title: {
                        text: 'Importer Market Share By Quantity'
                    },
                    subtitle: {
                        text: ''
                    },
                    plotOptions: {
                        pie: {
                            innerSize: 100,
                            depth: 45
                        }
                    },
                    series: [{
                        name: 'Total Quantity',
                        data: chdata22
                    }]
                });
            },
            error: function(data) {
                console.log(data);
            }
        });


    }
    function importer_sup_analysis(form_data){
        // alert('importer_sup_analysis');
        form_data = JSON.parse(localStorage.getItem("search") || "[]");
        console.log(form_data);

        var postData = {}; var data={};
        if(localStorage.getItem("search") != null) {
            postData = form_data.reduce(function(a, x) { a[x.name] = x.value; return a; }, {});
        } else {
            postData['_token'] = "{{ csrf_token() }}";
        }
        $.ajax({
            url: '{!! route('importerbill.ga_imp_supana_usd_comp') !!}',
            method: "POST",
            data:postData,
            success: function(data) {
                localStorage.setItem("importer_ExpAna_data",(localStorage.getItem("importer_ExpAna_data")-1));

                var imp_supana_usd = [];
                var imp_supana_importers = [];

                for(var i in data.data) {
                    imp_supana_importers.push(data.data[i].supplier.substring(0, 8));
                    imp_supana_usd.push(parseFloat(data.data[i].top15_usd));
                }
                /*console.dir(imp_supana_importers);
                console.dir(imp_supana_usd);*/
                $('#exporterana_bigchart_h2_span').html('Suppliers');
                var ctx = document.getElementById('bigExporterAnaChart').getContext("2d");

                var gradientStroke = ctx.createLinearGradient(500, 0, 100, 0);
                gradientStroke.addColorStop(0, '#80b6f4');
                gradientStroke.addColorStop(1, chartColor);

                var gradientFill = ctx.createLinearGradient(0, 200, 0, 50);
                gradientFill.addColorStop(0, "rgba(128, 182, 244, 0)");
                gradientFill.addColorStop(1, "rgba(255, 255, 255, 0.24)");

                if(myChartExpAna != null) // i initialize myBarChart var with null
                    myChartExpAna.destroy(); // if not null call destroy

                myChartExpAna = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        //labels: ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"],
                        labels: imp_supana_importers,
                        datasets: [{
                            label: "Top 15 Importer Companies Total Cost in USD",
                            borderColor: chartColor,
                            pointBorderColor: chartColor,
                            pointBackgroundColor: "#1e3d60",
                            pointHoverBackgroundColor: "#1e3d60",
                            pointHoverBorderColor: chartColor,
                            pointBorderWidth: 1,
                            pointHoverRadius: 7,
                            pointHoverBorderWidth: 2,
                            pointRadius: 5,
                            fill: true,
                            backgroundColor: gradientFill,
                            borderWidth: 2,
                            //data: [50, 150, 100, 190, 130, 90, 150, 160, 120, 140, 190, 95]
                            data: imp_supana_usd
                        }]
                    },
                    options: {
                        layout: {
                            padding: {
                                left: 20,
                                right: 20,
                                top: 0,
                                bottom: 0
                            }
                        },
                        maintainAspectRatio: false,
                        tooltips: {
                            backgroundColor: '#fff',
                            titleFontColor: '#333',
                            bodyFontColor: '#666',
                            bodySpacing: 4,
                            xPadding: 12,
                            mode: "nearest",
                            intersect: 0,
                            position: "nearest"
                        },
                        legend: {
                            position: "bottom",
                            fillStyle: "#FFF",
                            display: false
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    fontColor: "rgba(255,255,255,0.4)",
                                    fontStyle: "bold",
                                    beginAtZero: true,
                                    maxTicksLimit: 5,
                                    padding: 10
                                },
                                gridLines: {
                                    drawTicks: true,
                                    drawBorder: false,
                                    display: true,
                                    color: "rgba(255,255,255,0.1)",
                                    zeroLineColor: "transparent"
                                }

                            }],
                            xAxes: [{
                                gridLines: {
                                    zeroLineColor: "transparent",
                                    display: false,

                                },
                                ticks: {
                                    padding: 10,
                                    fontColor: "rgba(255,255,255,0.4)",
                                    fontStyle: "bold"
                                }
                            }]
                        }
                    }
                });

                var cardStatsMiniLineColor = "#fff",
                    cardStatsMiniDotColor = "#fff";

            },
            error: function(data) {
                console.log(data);
            }
        });
        $.ajax({
            
            url: '{!! route('importerbill.ga_imp_supana_usd_cost') !!}',
            method: "POST",
            data:postData,
            beforeSend: function() {
                  
                    $("#loading-container_expana_cost").show();
                    $("#container_expana_cost_chart").hide();
                },

            success: function(data) {
                $("#loading-container_expana_cost").hide();
                $("#container_expana_cost_chart").show();
                localStorage.setItem("importer_ExpAna_data",(localStorage.getItem("importer_ExpAna_data")-1));
                var chdata21 = [];
                for(var i in data.data) {
                    chdata21.push([data.data[i].supplier, parseFloat(data.data[i].top15_usd)]);
                }
                //console.log(chdata21);

                Highcharts.chart('container_expana_cost', {
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 45
                        }
                    },
                    title: {
                        text: 'Importer Market Share total Cost'
                    },
                    subtitle: {
                        text: 'In USD'
                    },
                    plotOptions: {
                        pie: {
                            innerSize: 100,
                            depth: 45
                        }
                    },
                    series: [{
                        name: 'Total Value USD',
                        data: chdata21
                    }]
                });
            },
            error: function(data) {
                console.log(data);
            }
        });
        $.ajax({
            url: '{!! route('importerbill.ga_imp_supana_usd_quantity') !!}',
            method: "POST",
            data:postData,
            beforeSend: function() {
                    $("#loading-container_expana_quantity").show();
                    $("#container_expana_quantity_chart").hide();
                },
            success: function(data) {
                $("#loading-container_expana_quantity").hide();
                $("#container_expana_quantity_chart").show();
                localStorage.setItem("importer_ExpAna_data",(localStorage.getItem("importer_ExpAna_data")-1));
                var chdata22= [];
                for(var i in data.data) {
                    chdata22.push([data.data[i].supplier, parseFloat(data.data[i].top_quantity)]);
                }
                //console.log(chdata22);

                Highcharts.chart('container_expana_quantity', {
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 45
                        }
                    },
                    title: {
                        text: 'Importer Market Share By Quantity'
                    },
                    subtitle: {
                        text: ''
                    },
                    plotOptions: {
                        pie: {
                            innerSize: 100,
                            depth: 45
                        }
                    },
                    series: [{
                        name: 'Total Quantity',
                        data: chdata22
                    }]
                });
            },
            error: function(data) {
                console.log(data);
            }
        });


    }
    function importer_analysis_empty(){
      var impana_usd101 = [];
      var impana_importers101 = [];

      $('#importerana_bigchart_h2_span').html('Importers');

      var ctx = document.getElementById('bigImporterAnaChart').getContext("2d");

      var gradientFill = ctx.createLinearGradient(0, 200, 0, 50);
      gradientFill.addColorStop(0, "rgba(128, 182, 244, 0)");
      gradientFill.addColorStop(1, "rgba(255, 255, 255, 0.24)");

      if(myChartImpAna != null) // i initialize myBarChart var with null
          myChartImpAna.destroy(); // if not null call destroy

      myChartImpAna = new Chart(ctx, {
          type: 'bar',
          data: {
              //labels: ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"],
              labels: impana_importers101,
              datasets: [{
                  label: "Top 15 Importer Companies Total Cost in USD",
                  borderColor: '',
                  pointBorderColor: '',
                  pointBackgroundColor: "#1e3d60",
                  pointHoverBackgroundColor: "#1e3d60",
                  pointHoverBorderColor: '',
                  pointBorderWidth: 1,
                  pointHoverRadius: 7,
                  pointHoverBorderWidth: 2,
                  pointRadius: 5,
                  fill: true,
                  backgroundColor: gradientFill,
                  borderWidth: 2,
                  //data: [50, 150, 100, 190, 130, 90, 150, 160, 120, 140, 190, 95]
                  data: impana_usd101
              }]
          },
          options: {
              layout: {
                  padding: {
                      left: 20,
                      right: 20,
                      top: 0,
                      bottom: 0
                  }
              },
              maintainAspectRatio: false,
              tooltips: {
                  backgroundColor: '#fff',
                  titleFontColor: '#333',
                  bodyFontColor: '#666',
                  bodySpacing: 4,
                  xPadding: 12,
                  mode: "nearest",
                  intersect: 0,
                  position: "nearest"
              },
              legend: {
                  position: "bottom",
                  fillStyle: "#FFF",
                  display: false
              },
              scales: {
                  yAxes: [{
                      ticks: {
                          fontColor: "rgba(255,255,255,0.4)",
                          fontStyle: "bold",
                          beginAtZero: true,
                          maxTicksLimit: 5,
                          padding: 10
                      },
                      gridLines: {
                          drawTicks: true,
                          drawBorder: false,
                          display: true,
                          color: "rgba(255,255,255,0.1)",
                          zeroLineColor: "transparent"
                      }

                  }],
                  xAxes: [{
                      gridLines: {
                          zeroLineColor: "transparent",
                          display: false,

                      },
                      ticks: {
                          padding: 10,
                          fontColor: "rgba(255,255,255,0.4)",
                          fontStyle: "bold"
                      }
                  }]
              }
          }
      });

      var chdata101 = [];
      Highcharts.chart('container_importerana_port', {
          chart: {
              type: 'pie',
              options3d: {
                  enabled: true,
                  alpha: 45
              }
          },
          title: {
              text: 'Importer Market Share total Cost'
          },
          subtitle: {
              text: 'In USD'
          },
          plotOptions: {
              pie: {
                  innerSize: 100,
                  depth: 45
              }
          },
          series: [{
              name: 'Total Value USD',
              data: chdata101
          }]
      });

      var chdata102= [];
      Highcharts.chart('chart_importerana_country', {
          chart: {
              type: 'pie',
              options3d: {
                  enabled: true,
                  alpha: 45
              }
          },
          title: {
              text: 'Importer Market Share By Quantity'
          },
          subtitle: {
              text: ''
          },
          plotOptions: {
              pie: {
                  innerSize: 100,
                  depth: 45
              }
          },
          series: [{
              name: 'Total Quantity',
              data: chdata102
          }]
      });
    }
    function importer_marketshare(form_data){
        form_data = JSON.parse(localStorage.getItem("search") || "[]");
        console.log(form_data);
        var postData = {}; var data={};

        if(localStorage.getItem("search") != null) {
          postData = form_data.reduce(function(a, x) { a[x.name] = x.value; return a; }, {});
        } else {
          postData['_token'] = "{{ csrf_token() }}";
        }
        $.ajax({
            url: '{!! route('importerbill.ga_marketshare_cost_usd_port') !!}',
            method: "POST",
            data:postData,
            beforeSend: function() {
                    $("#loading-container_ms_cost_usd_port").show();
                    $("#container_ms_cost_usd_port_chart").hide();
                },

            success: function(data) {
                $("#loading-container_ms_cost_usd_port").hide();
                $("#container_ms_cost_usd_port_chart").show();
                localStorage.setItem("importer_marSha_data",(localStorage.getItem("importer_marSha_data")-1));

                var chdata = []
                for(var i in data.data) {
                    chdata.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
                }
                //console.log(chdata);

                Highcharts.chart('container_ms_cost_usd_port', {
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 45
                        }
                    },
                    title: {
                        text: 'Market Share Total Cost in USD'
                    },
                    subtitle: {
                        text: 'Port Wise'
                    },
                    plotOptions: {
                        pie: {
                            innerSize: 100,
                            depth: 45
                        }
                    },
                    series: [{
                        name: 'Total Value USD',
                        data: chdata
                    }]
                });
            },
            error: function(data) {
                console.log(data);
            }
        });
        $.ajax({
          url: '{!! route('importerbill.ga_marketshare_cost_qua_port') !!}',
          method: "POST",
          data:postData,
          beforeSend: function() {
                    $("#loading-container_ms_cost_qua_port").show();
                    $("#container_ms_cost_qua_port_chart").hide();
            },
          success: function(data) {
            $("#loading-container_ms_cost_qua_port").hide();
            $("#container_ms_cost_qua_port_chart").show();
            localStorage.setItem("importer_marSha_data",(localStorage.getItem("importer_marSha_data")-1));

                var chdata = []
                for(var i in data.data) {
                    chdata.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
                }
                //console.log(chdata);

                Highcharts.chart('container_ms_cost_qua_port', {
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 45
                        }
                    },
                    title: {
                        text: 'Market Share by Quantity'
                    },
                    subtitle: {
                        text: 'Port Wise'
                    },
                    plotOptions: {
                        pie: {
                            innerSize: 100,
                            depth: 45
                        }
                    },
                    series: [{
                        name: 'Total Quantity',
                        data: chdata
                    }]
                });
            },
          error: function(data) {
              console.log(data);
          }
        });
        $.ajax({
          url: '{!! route('importerbill.ga_marketshare_cost_qua_country') !!}',
          method: "POST",
          data:postData,
          beforeSend: function() {
                    $("#loading-container_ms_cost_qua_country").show();
                    $("#container_ms_cost_qua_country_chart").hide();
            },
          success: function(data) {
            $("#loading-container_ms_cost_qua_country").hide();
            $("#container_ms_cost_qua_country_chart").show();
            localStorage.setItem("importer_marSha_data",(localStorage.getItem("importer_marSha_data")-1));

                var chdata = []
                for(var i in data.data) {
                    chdata.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
                }
                //console.log(chdata);

                Highcharts.chart('container_ms_cost_qua_country', {
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 45
                        }
                    },
                    title: {
                        text: 'Market Share by Quantity'
                    },
                    subtitle: {
                        text: 'Country Wise'
                    },
                    plotOptions: {
                        pie: {
                            innerSize: 100,
                            depth: 45
                        }
                    },
                    series: [{
                        name: 'Total Quantity',
                        data: chdata
                    }]
                });
            },
          error: function(data) {
              console.log(data);
          }
        });
        $.ajax({
            url: '{!! route('importerbill.ga_marketshare_cost_usd_country') !!}',
            method: "POST",
            data:postData,
            beforeSend: function() {
                    $("#loading-container_ms_cost_usd_country").show();
                    $("#container_ms_cost_usd_country_chart").hide();
                },

            success: function(data) {
                $("#loading-container_ms_cost_usd_country").hide();
                $("#container_ms_cost_usd_country_chart").show();
                localStorage.setItem("importer_marSha_data",(localStorage.getItem("importer_marSha_data")-1));

                var chdata = []
                for(var i in data.data) {
                    chdata.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
                }
                //console.log(chdata);

                Highcharts.chart('container_ms_cost_usd_country', {
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 45
                        }
                    },
                    title: {
                        text: 'Market Share Total Cost in USD'
                    },
                    subtitle: {
                        text: 'Country Wise'
                    },
                    plotOptions: {
                        pie: {
                            innerSize: 100,
                            depth: 45
                        }
                    },
                    series: [{
                        name: 'Total Value USD',
                        data: chdata
                    }]
                });
            },
            error: function(data) {
                console.log(data);
            }
        });

      }
    function importer_priceana(form_data){
        form_data = JSON.parse(localStorage.getItem("search") || "[]");
        console.log(form_data);
        var postData = {}; var data={};


        if(localStorage.getItem("search") != null) {
          postData = form_data.reduce(function(a, x) { a[x.name] = x.value; return a; }, {});
        } else {
          postData['_token'] = "{{ csrf_token() }}";
        }
       
        $.ajax({
            url: '{!! route('importerbill.ga_priceana_usd_country') !!}',
            method: "POST",
            data:postData,
            beforeSend: function() {
                    $("#loading-container_priceana_usd_country").show();
                },
            success: function(data) {
                $("#loading-container_priceana_usd_country").hide();
                localStorage.setItem("importer_price_data",(localStorage.getItem("importer_price_data")-1));

                var pucdata = [],resdates = [], rescountry=[];

                for(var i in data.data) {
                    resdates.push(new Date(data.data[i].week_start));
                    rescountry.push(data.data[i].labeltitle);
                }
                var maxDate=new Date(Math.max.apply(null,resdates)).toISOString();
                var minDate=new Date(Math.min.apply(null,resdates)).toISOString().slice(0,10);
                var lastSunday = new Date(lastMondayinmonth( new Date(maxDate).getMonth()+1, new Date(maxDate).getFullYear()));
                //console.log('maxdate = '+maxDate+', mindate = '+minDate+', last sunday = '+lastSunday);
                rescountry = Array.from(new Set(rescountry));
                for(var i in rescountry) {
                    if (!this[rescountry[i]]) {
                        var countrydata = [];
                        var currentSunday = new Date(lastSunday);
                        var c2Sunday = currentSunday;
                        var weekdate = [];
                        while(currentSunday >= new Date(minDate)) {
                            c2Sunday = currentSunday;
                            weekdate.push(new Date(c2Sunday.setDate(c2Sunday.getDate())).toDateString().slice(3,15));
                            var pushed = false;
                            for (var ii in data.data) {
                                if (new Date(data.data[ii].week_start).getTime() == new Date(currentSunday).getTime() && data.data[ii].labeltitle == rescountry[i]) {
                                    countrydata.push(parseFloat(data.data[ii].labelvalue));
                                    pushed = true;
                                    break;
                                }
                            }
                            if(pushed == false){
                                countrydata.push(parseFloat(0));
                            }
                            currentSunday = new Date(currentSunday.setDate(currentSunday.getDate() - 7));
                        }
                        this[rescountry[i]] = { name: rescountry[i], data: countrydata };
                        pucdata.push(this[rescountry[i]]);
                    }
                }
            
                Highcharts.chart('container_priceana_usd_country', {
                    title: {
                        text: 'Total  Value of Top 5 Countries in USD  by Time [Weekly]'
                    },

                    yAxis: {
                        title: {
                            text: 'Value In USD'
                        }
                    },
                    xAxis: {
                        tickmarkPlacement: 'between',
                        //tickmarkPlacement: 'on',
                        categories: weekdate
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle'
                    },
                    plotOptions: {
                        series: {
                            label: {
                                connectorAllowed: false
                            }
                        }
                    },

                    series: pucdata,
                    responsive: {
                        rules: [{
                            condition: {
                                maxWidth: 500
                            },
                            chartOptions: {
                                legend: {
                                    layout: 'horizontal',
                                    align: 'center',
                                    verticalAlign: 'bottom'

                                },
                                xAxis: {
                                    labels: {
                                        step: 2
                                    }
                                }
                            }
                        }]
                    },
                    /*chart:{
                    backgroundColor: "rgba(255, 255, 255, 0)",
                    plotBackgroundColor: "#ffffff"
                    }*/
                });
            },
            error: function(data) {
                console.log(data);
            }
        });
        $.ajax({
            url: '{!! route('importerbill.ga_priceana_usd_port') !!}',
            method: "POST",
            data:postData,
            beforeSend: function() {
                    $("#loading-container_priceana_usd_port").show();
                    $("#container_priceana_usd_port_chart").hide();
                    
                },

            success: function(data) {
                $("#loading-container_priceana_usd_port").hide();
                $("#container_priceana_usd_port_chart").show();
                localStorage.setItem("importer_price_data",(localStorage.getItem("importer_price_data")-1));
                var pucdata = [],resdates = [], rescountry=[];

                for(var i in data.data) {
                    resdates.push(new Date(data.data[i].week_start));
                    rescountry.push(data.data[i].labeltitle);
                }
                var maxDate=new Date(Math.max.apply(null,resdates)).toISOString();
                var minDate=new Date(Math.min.apply(null,resdates)).toISOString().slice(0,10);
                var lastSunday = new Date(lastMondayinmonth( new Date(maxDate).getMonth()+1, new Date(maxDate).getFullYear()));

                rescountry = Array.from(new Set(rescountry));
                for(var i in rescountry) {
                    if (!this[rescountry[i]]) {
                        var countrydata = [];
                        var currentSunday = new Date(lastSunday);
                        var c2Sunday = currentSunday;
                        var weekdate = [];
                        while(currentSunday >= new Date(minDate)) {
                            c2Sunday = currentSunday;
                            weekdate.push(new Date(c2Sunday.setDate(c2Sunday.getDate())).toDateString().slice(3,15));
                            var pushed = false;
                            for (var ii in data.data) {
                                if (new Date(data.data[ii].week_start).getTime() == new Date(currentSunday).getTime() && data.data[ii].labeltitle == rescountry[i]) {
                                    countrydata.push(parseFloat(data.data[ii].labelvalue));
                                    pushed = true;
                                    break;
                                }
                            }
                            if(pushed == false){
                                countrydata.push(parseFloat(0));
                            }
                            currentSunday = new Date(currentSunday.setDate(currentSunday.getDate() - 7));
                        }
                        this[rescountry[i]] = { name: rescountry[i], data: countrydata };
                        pucdata.push(this[rescountry[i]]);
                    }
                }
                Highcharts.chart('container_priceana_usd_port', {
                    title: {
                        text: 'Total  Value of Top 5 Ports in USD  by Time [Weekly]'
                    },

                    yAxis: {
                        title: {
                            text: 'Value In USD'
                        }
                    },

                    xAxis: {
                        tickmarkPlacement: 'between',
                        //tickmarkPlacement: 'on',
                        categories: weekdate
                    },

                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle'
                    },

                    plotOptions: {
                        series: {
                            label: {
                                connectorAllowed: false
                            }
                        }
                    },

                    series: pucdata,
                    responsive: {
                        rules: [{
                            condition: {
                                maxWidth: 500
                            },
                            chartOptions: {
                                legend: {
                                    layout: 'horizontal',
                                    align: 'center',
                                    verticalAlign: 'bottom'

                                },
                                xAxis: {
                                    labels: {
                                        step: 2
                                    }
                                }
                            }
                        }]
                    },
                    /*chart:{
                     backgroundColor: "rgba(255, 255, 255, 0)",
                     plotBackgroundColor: "#ffffff"
                     }*/
                });
            },
            error: function(data) {
                console.log(data);
            }
        });
        $.ajax({
            url: '{!! route('importerbill.ga_priceana_usd_importer') !!}',
            method: "POST",
            data:postData,
            beforeSend: function() {
                    $("#loading-chart_priceana_usd_impexp").show();
                    $("#chart_priceana_usd_impexp_chart").hide();
                },

            success: function(data) {
                $("#loading-chart_priceana_usd_impexp").hide();
                $("#chart_priceana_usd_impexp_chart").show();
                localStorage.setItem("importer_price_data",(localStorage.getItem("importer_price_data")-1));

                var pucdata = [],resdates = [], rescountry=[];

                for(var i in data.data) {
                    resdates.push(new Date(data.data[i].week_start));
                    rescountry.push(data.data[i].labeltitle);
                }
                var maxDate=new Date(Math.max.apply(null,resdates)).toISOString();
                var minDate=new Date(Math.min.apply(null,resdates)).toISOString().slice(0,10);
                var lastSunday = new Date(lastMondayinmonth( new Date(maxDate).getMonth()+1, new Date(maxDate).getFullYear()));

                rescountry = Array.from(new Set(rescountry));
                for(var i in rescountry) {
                    if (!this[rescountry[i]]) {
                        var countrydata = [];
                        var currentSunday = new Date(lastSunday);
                        var c2Sunday = currentSunday;
                        var weekdate = [];
                        while(currentSunday >= new Date(minDate)) {
                            c2Sunday = currentSunday;
                            weekdate.push(new Date(c2Sunday.setDate(c2Sunday.getDate())).toDateString().slice(3,15));
                            var pushed = false;
                            for (var ii in data.data) {
                                if (new Date(data.data[ii].week_start).getTime() == new Date(currentSunday).getTime() && data.data[ii].labeltitle == rescountry[i]) {
                                    countrydata.push(parseFloat(data.data[ii].labelvalue));
                                    pushed = true;
                                    break;
                                }
                            }
                            if(pushed == false){
                                countrydata.push(parseFloat(0));
                            }
                            currentSunday = new Date(currentSunday.setDate(currentSunday.getDate() - 7));
                        }
                        this[rescountry[i]] = { name: rescountry[i], data: countrydata };
                        pucdata.push(this[rescountry[i]]);
                    }
                }
                Highcharts.chart('chart_priceana_usd_impexp', {
                    title: {
                        text: 'Total  Value of Top 15 Importers in USD  by Time [Weekly]'
                    },

                    yAxis: {
                        title: {
                            text: 'Value In USD'
                        }
                    },

                    xAxis: {
                        tickmarkPlacement: 'between',
                        //tickmarkPlacement: 'on',
                        categories: weekdate
                    },

                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle'
                    },

                    plotOptions: {
                        series: {
                            label: {
                                connectorAllowed: false
                            }
                        }
                    },

                    series: pucdata,
                    responsive: {
                        rules: [{
                            condition: {
                                maxWidth: 500
                            },
                            chartOptions: {
                                legend: {
                                    layout: 'horizontal',
                                    align: 'center',
                                    verticalAlign: 'bottom'

                                },
                                xAxis: {
                                    labels: {
                                        step: 2
                                    }
                                }
                            }
                        }]
                    },
                    /*chart:{
                     backgroundColor: "rgba(255, 255, 255, 0)",
                     plotBackgroundColor: "#ffffff"
                     }*/
                });
            },
            error: function(data) {
                console.log(data);
            }
        });

    }
    function importer_comparison(form_data){
        form_data = JSON.parse(localStorage.getItem("search") || "[]");
     console.log(form_data);
      var postData = {}; var data={};
      if(localStorage.getItem("search") != null) {
          postData = form_data.reduce(function(a, x) { a[x.name] = x.value; return a; }, {});
      } else {
          postData['_token'] = "{{ csrf_token() }}";
      }
      $.ajax({
          url: '{!! route('importerbill.ga_comparison_usd_importer') !!}',
          method: "POST",
          data:postData,
          beforeSend: function() {
                    $("#loading-chart_comparisontt_usd_impexp").show();
                    $("#chart_comparisontt_usd_impexp_chart").hide();
                },

          success: function(data) {
            $("#loading-chart_comparisontt_usd_impexp").hide();
            $("#chart_comparisontt_usd_impexp_chart").show();
            localStorage.setItem("importer_comp_data",(localStorage.getItem("importer_comp_data")-1));

              var pucdata = [],resdates = [], rescountry=[];

              for(var i in data.data) {
                  resdates.push(new Date(data.data[i].week_start));
                  rescountry.push(data.data[i].labeltitle);
              }
              var maxDate=new Date(Math.max.apply(null,resdates)).toISOString();
              var minDate=new Date(Math.min.apply(null,resdates)).toISOString().slice(0,10);
              var lastSunday = new Date(lastMondayinmonth( new Date(maxDate).getMonth()+1, new Date(maxDate).getFullYear()));

              rescountry = Array.from(new Set(rescountry));
              for(var i in rescountry) {
                  if (!this[rescountry[i]]) {
                      var countrydata = [];
                      var currentSunday = new Date(lastSunday);
                      var c2Sunday = currentSunday;
                      var weekdate = [];
                      while(currentSunday >= new Date(minDate)) {
                          c2Sunday = currentSunday;
                          weekdate.push(new Date(c2Sunday.setDate(c2Sunday.getDate())).toDateString().slice(3,15));
                          var pushed = false;
                          for (var ii in data.data) {
                              if (new Date(data.data[ii].week_start).getTime() == new Date(currentSunday).getTime() && data.data[ii].labeltitle == rescountry[i]) {
                                  countrydata.push(parseFloat(data.data[ii].labelvalue));
                                  pushed = true;
                                  break;
                              }
                          }
                          if(pushed == false){
                              countrydata.push(parseFloat(0));
                          }
                          currentSunday = new Date(currentSunday.setDate(currentSunday.getDate() - 7));
                      }
                      this[rescountry[i]] = { name: rescountry[i], data: countrydata };
                      pucdata.push(this[rescountry[i]]);
                  }
              }
              Highcharts.chart('chart_comparisontt_usd_impexp', {
                  title: {
                      text: 'Total  Value Comparison of Top  Importers in USD  by Time [Weekly]'
                  },

                  yAxis: {
                      title: {
                          text: 'Value In USD'
                      }
                  },

                  xAxis: {
                      tickmarkPlacement: 'between',
                      //tickmarkPlacement: 'on',
                      categories: weekdate
                  },

                  /*legend: {
                      layout: 'vertical',
                      align: 'right',
                      verticalAlign: 'middle'
                  },*/

                  plotOptions: {
                      series: {
                          label: {
                              connectorAllowed: false
                          }
                      }
                  },

                  series: pucdata,
                  responsive: {
                      rules: [{
                          condition: {
                              maxWidth: 500
                          },
                          chartOptions: {
                              legend: {
                                  layout: 'horizontal',
                                  align: 'center',
                                  verticalAlign: 'bottom'

                              },
                              xAxis: {
                                  labels: {
                                      step: 2
                                  }
                              }
                          }
                      }]
                  },
                  /*chart:{
                   backgroundColor: "rgba(255, 255, 255, 0)",
                   plotBackgroundColor: "#ffffff"
                   }*/
              });
          },
          error: function(data) {
              console.log(data);
          }
      });
      $.ajax({
          url: '{!! route('importerbill.ga_comparison_usd_country') !!}',
          method: "POST",
          data:postData,
          beforeSend: function() {
                    $("#loading-chart_comparisontt_usd_country").show();
                    $("#chart_comparisontt_usd_country_chart").hide();
                },
          success: function(data) {
            $("#loading-chart_comparisontt_usd_country").hide();
            $("#chart_comparisontt_usd_country_chart").show();
            localStorage.setItem("importer_comp_data",(localStorage.getItem("importer_comp_data")-1));
              var pucdata = [],resdates = [], rescountry=[];

              for(var i in data.data) {
                  resdates.push(new Date(data.data[i].week_start));
                  rescountry.push(data.data[i].labeltitle);
              }
              var maxDate=new Date(Math.max.apply(null,resdates)).toISOString();
              var minDate=new Date(Math.min.apply(null,resdates)).toISOString().slice(0,10);
              var lastSunday = new Date(lastMondayinmonth( new Date(maxDate).getMonth()+1, new Date(maxDate).getFullYear()));

              rescountry = Array.from(new Set(rescountry));
              for(var i in rescountry) {
                  if (!this[rescountry[i]]) {
                      var countrydata = [];
                      var currentSunday = new Date(lastSunday);
                      var c2Sunday = currentSunday;
                      var weekdate = [];
                      while(currentSunday >= new Date(minDate)) {
                          c2Sunday = currentSunday;
                          weekdate.push(new Date(c2Sunday.setDate(c2Sunday.getDate())).toDateString().slice(3,15));
                          var pushed = false;
                          for (var ii in data.data) {
                              if (new Date(data.data[ii].week_start).getTime() == new Date(currentSunday).getTime() && data.data[ii].labeltitle == rescountry[i]) {
                                  countrydata.push(parseFloat(data.data[ii].labelvalue));
                                  pushed = true;
                                  break;
                              }
                          }
                          if(pushed == false){
                              countrydata.push(parseFloat(0));
                          }
                          currentSunday = new Date(currentSunday.setDate(currentSunday.getDate() - 7));
                      }
                      this[rescountry[i]] = { name: rescountry[i], data: countrydata };
                      pucdata.push(this[rescountry[i]]);
                  }
              }
              Highcharts.chart('chart_comparisontt_usd_country', {
                  title: {
                      text: 'Total  Value Comparison of Top  Countries in USD  by Time [Weekly]'
                  },

                  yAxis: {
                      title: {
                          text: 'Value In USD'
                      }
                  },

                  xAxis: {
                      tickmarkPlacement: 'between',
                      //tickmarkPlacement: 'on',
                      categories: weekdate
                  },

                  /*legend: {
                      layout: 'vertical',
                      align: 'right',
                      verticalAlign: 'middle'
                  },*/

                  plotOptions: {
                      series: {
                          label: {
                              connectorAllowed: false
                          }
                      }
                  },

                  series: pucdata,
                  responsive: {
                      rules: [{
                          condition: {
                              maxWidth: 500
                          },
                          chartOptions: {
                              legend: {
                                  layout: 'horizontal',
                                  align: 'center',
                                  verticalAlign: 'bottom'

                              },
                              xAxis: {
                                  labels: {
                                      step: 2
                                  }
                              }
                          }
                      }]
                  },
                  /*chart:{
                   backgroundColor: "rgba(255, 255, 255, 0)",
                   plotBackgroundColor: "#ffffff"
                   }*/
              });
          },
          error: function(data) {
              console.log(data);
          }
      });
      $.ajax({
          url: '{!! route('importerbill.ga_comparison_usd_ports') !!}',
          method: "POST",
          data:postData,
          beforeSend: function() {
                    $("#loading-chart_comparisontt_usd_ports").show();
                    $("#chart_comparisontt_usd_ports_chart").hide();
                },

          success: function(data) {
            $("#loading-chart_comparisontt_usd_ports").hide();
            $("#chart_comparisontt_usd_ports_chart").show();
            localStorage.setItem("importer_comp_data",(localStorage.getItem("importer_comp_data")-1));
              var pucdata = [],resdates = [], rescountry=[];

              for(var i in data.data) {
                  resdates.push(new Date(data.data[i].week_start));
                  rescountry.push(data.data[i].labeltitle);
              }
              var maxDate=new Date(Math.max.apply(null,resdates)).toISOString();
              var minDate=new Date(Math.min.apply(null,resdates)).toISOString().slice(0,10);
              var lastSunday = new Date(lastMondayinmonth( new Date(maxDate).getMonth()+1, new Date(maxDate).getFullYear()));

              rescountry = Array.from(new Set(rescountry));
              for(var i in rescountry) {
                  if (!this[rescountry[i]]) {
                      var countrydata = [];
                      var currentSunday = new Date(lastSunday);
                      var c2Sunday = currentSunday;
                      var weekdate = [];
                      while(currentSunday >= new Date(minDate)) {
                          c2Sunday = currentSunday;
                          weekdate.push(new Date(c2Sunday.setDate(c2Sunday.getDate())).toDateString().slice(3,15));
                          var pushed = false;
                          for (var ii in data.data) {
                              if (new Date(data.data[ii].week_start).getTime() == new Date(currentSunday).getTime() && data.data[ii].labeltitle == rescountry[i]) {
                                  countrydata.push(parseFloat(data.data[ii].labelvalue));
                                  pushed = true;
                                  break;
                              }
                          }
                          if(pushed == false){
                              countrydata.push(parseFloat(0));
                          }
                          currentSunday = new Date(currentSunday.setDate(currentSunday.getDate() - 7));
                      }
                      this[rescountry[i]] = { name: rescountry[i], data: countrydata };
                      pucdata.push(this[rescountry[i]]);
                  }
              }
              Highcharts.chart('chart_comparisontt_usd_ports', {
                  title: {
                      text: 'Total  Value Comparison of Top  Ports in USD  by Time [Weekly]'
                  },

                  yAxis: {
                      title: {
                          text: 'Value In USD'
                      }
                  },

                  xAxis: {
                      tickmarkPlacement: 'between',
                      //tickmarkPlacement: 'on',
                      categories: weekdate
                  },

                  /*legend: {
                      layout: 'vertical',
                      align: 'right',
                      verticalAlign: 'middle'
                  },*/

                  plotOptions: {
                      series: {
                          label: {
                              connectorAllowed: false
                          }
                      }
                  },

                  series: pucdata,
                  responsive: {
                      rules: [{
                          condition: {
                              maxWidth: 500
                          },
                          chartOptions: {
                              legend: {
                                  layout: 'horizontal',
                                  align: 'center',
                                  verticalAlign: 'bottom'

                              },
                              xAxis: {
                                  labels: {
                                      step: 2
                                  }
                              }
                          }
                      }]
                  },
                  /*chart:{
                   backgroundColor: "rgba(255, 255, 255, 0)",
                   plotBackgroundColor: "#ffffff"
                   }*/
              });
          },
          error: function(data) {
              console.log(data);
          }
      });

    }
    function importer_gridsummaries(form_data){
        // alert("importer_gridsummaries");
      form_data = JSON.parse(localStorage.getItem("search") || "[]");
     console.log(form_data);
      var postData = {}; var data={};
      if(localStorage.getItem("search") != null) {
        postData = form_data.reduce(function(a, x) { a[x.name] = x.value; return a; }, {});
      } else {
        postData['_token'] = "{{ csrf_token() }}";
      }
      $('#gsum-8digit-table').DataTable({
            processing: true,
            serverSide: false,
            processData: false,
            contentType: false,
            autoWidth: false,
            enctype: 'multipart/form-data',
            ajax: {
                url:'{!! route('importerbill.ga_gsum_8digit') !!}',
                type:'POST',
                data: postData,
                

                error: function (xhr, error, code)
                {
                    console.log(xhr);
                    console.log(code);
                },
                
             
                
                statusCode: {
                    401: function() {
                        window.location.href = 'login'; //or what ever is your login URI
                    },
                    419: function(){
                        window.location.href = 'login'; //or what ever is your login URI
                    }
                },
            },
            columns: [
                { title: 'HS Code'         , data: 'hs_code'        , name: 'hs_code'},
                { title: 'Avg Unit Price'  , data: 'avg_unit_price' , name: 'avg_unit_price' },
                { title: 'Quantity Sum'    , data: 'quantity_sum'   , name: 'quantity_sum'   },
                { title: 'Value Sum (USD)' , data: 'value_sum_usd'  , name: 'value_sum_usd'  },
                { title: 'Record Count'    , data: 'record_count'   , name: 'record_count'   },
                { title: 'Duty Sum'        , data: 'duty_sum'       , name: 'duty_sum'       }
            ],
            columnDefs: [
             { width: '16%', targets: 0 },
             { width: '16%', targets: 1 },
             { width: '16%', targets: 2 },
             { width: '16%', targets: 3 },
             { width: '16%', targets: 4 },
             { width: '16%', targets: 5 }
             ],
            scrollX:        true,
            scrollCollapse: true,
            fixedColumns: true,
            "dom": '<"top"ilf>rt<"bottom"ip><"clear">'
        })
      $('#gsum-2digit-table').DataTable({
            processing: true,
            serverSide: false,
            processData: false,
            contentType: false,
            autoWidth: false,
            enctype: 'multipart/form-data',
            ajax: {
                url:'{!! route('importerbill.ga_gsum_2digit') !!}',
                type:'POST',
                data: postData,
                
                error: function (xhr, error, code)
                {
                    console.log(xhr);
                    console.log(code);
                },
                statusCode: {
                    401: function() {
                        window.location.href = 'login'; //or what ever is your login URI
                    },
                    419: function(){
                        window.location.href = 'login'; //or what ever is your login URI
                    }
                },
            },
            columns: [
                { title: 'HS Code'         , data: 'hs_code'        , name: 'hs_code'},
                { title: 'Avg Unit Price'  , data: 'avg_unit_price' , name: 'avg_unit_price' },
                { title: 'Quantity Sum'    , data: 'quantity_sum'   , name: 'quantity_sum'   },
                { title: 'Value Sum (USD)' , data: 'value_sum_usd'  , name: 'value_sum_usd'  },
                { title: 'Record Count'    , data: 'record_count'   , name: 'record_count'   },
                { title: 'Duty Sum'        , data: 'duty_sum'       , name: 'duty_sum'       }
            ],
            scrollX:        true,
            scrollCollapse: true,
            fixedColumns: true,
            "dom": '<"top"ilf>rt<"bottom"ip><"clear">'
        })
      $('#gsum-4digit-table').DataTable({
            processing: true,
            serverSide: false,
            processData: false,
            contentType: false,
            autoWidth: false,
            enctype: 'multipart/form-data',
            ajax: {
                url:'{!! route('importerbill.ga_gsum_4digit') !!}',
                type:'POST',
                data: postData,
              
                error: function (xhr, error, code)
                {
                    console.log(xhr);
                    console.log(code);
                },
                statusCode: {
                    401: function() {
                        window.location.href = 'login'; //or what ever is your login URI
                    },
                    419: function(){
                        window.location.href = 'login'; //or what ever is your login URI
                    }
                },
            },
            columns: [
                { title: 'HS Code'         , data: 'hs_code'        , name: 'hs_code'},
                { title: 'Avg Unit Price'  , data: 'avg_unit_price' , name: 'avg_unit_price' },
                { title: 'Quantity Sum'    , data: 'quantity_sum'   , name: 'quantity_sum'   },
                { title: 'Value Sum (USD)' , data: 'value_sum_usd'  , name: 'value_sum_usd'  },
                { title: 'Record Count'    , data: 'record_count'   , name: 'record_count'   },
                { title: 'Duty Sum'        , data: 'duty_sum'       , name: 'duty_sum'       }
            ],
            scrollX:        true,
            scrollCollapse: true,
            fixedColumns: true,
            "dom": '<"top"ilf>rt<"bottom"ip><"clear">'
        })
      $('#gsum-port-table').DataTable({
            processing: true,
            serverSide: false,
            processData: false,
            contentType: false,
            autoWidth: false,
            enctype: 'multipart/form-data',
            ajax: {
                url:'{!! route('importerbill.ga_gsum_port') !!}',
                type:'POST',
                data: postData,
                
                error: function (xhr, error, code)
                {
                    console.log(xhr);
                    console.log(code);
                },
                statusCode: {
                    401: function() {
                        window.location.href = 'login'; //or what ever is your login URI
                    },
                    419: function(){
                        window.location.href = 'login'; //or what ever is your login URI
                    }
                },
            },
            columns: [
                { title: 'Port'            , data: 'label_title'    , name: 'label_title'},
                { title: 'Avg Unit Price'  , data: 'avg_unit_price' , name: 'avg_unit_price' },
                { title: 'Quantity Sum'    , data: 'quantity_sum'   , name: 'quantity_sum'   },
                { title: 'Value Sum (USD)' , data: 'value_sum_usd'  , name: 'value_sum_usd'  },
                { title: 'Record Count'    , data: 'record_count'   , name: 'record_count'   },
                { title: 'Duty Sum'        , data: 'duty_sum'       , name: 'duty_sum'       }
            ],
            /*columnDefs: [
             { width: 40, targets: 0 },
             { width: 110, targets: 1 },
             { width: 250, targets: 2 },
             { width: 350, targets: 3 },
             { width: 110, targets: 4 },
             { width: 130, targets: 5 }
             ],*/
            scrollX:        true,
            scrollCollapse: true,
            fixedColumns: true,
            "dom": '<"top"ilf>rt<"bottom"ip><"clear">'
        })
      $('#gsum-country-table').DataTable({
            processing: true,
            serverSide: false,
            processData: false,
            contentType: false,
            autoWidth: false,
            enctype: 'multipart/form-data',
            ajax: {
                url:'{!! route('importerbill.ga_gsum_country') !!}',
                type:'POST',
                data: postData,
                
                error: function (xhr, error, code)
                {
                    console.log(xhr);
                    console.log(code);
                },
                statusCode: {
                    401: function() {
                        window.location.href = 'login'; //or what ever is your login URI
                    },
                    419: function(){
                        window.location.href = 'login'; //or what ever is your login URI
                    }
                },
            },
            columns: [
                { title: 'Country'         , data: 'label_title'    , name: 'label_title'},
                { title: 'Avg Unit Price'  , data: 'avg_unit_price' , name: 'avg_unit_price' },
                { title: 'Quantity Sum'    , data: 'quantity_sum'   , name: 'quantity_sum'   },
                { title: 'Value Sum (USD)' , data: 'value_sum_usd'  , name: 'value_sum_usd'  },
                { title: 'Record Count'    , data: 'record_count'   , name: 'record_count'   },
                { title: 'Duty Sum'        , data: 'duty_sum'       , name: 'duty_sum'       }
            ],
            scrollX:        true,
            scrollCollapse: true,
            fixedColumns: true,
            "dom": '<"top"ilf>rt<"bottom"ip><"clear">'
        })
      $('#gsum-unit-table').DataTable({
        processing: true,
        serverSide: false,
        processData: false,
        contentType: false,
        autoWidth: false,
        enctype: 'multipart/form-data',
        ajax: {
            url:'{!! route('importerbill.ga_gsum_unit') !!}',
            type:'POST',
            data: postData,
           
            error: function (xhr, error, code)
            {
                console.log(xhr);
                console.log(code);
            },
            statusCode: {
                401: function() {
                    window.location.href = 'login'; //or what ever is your login URI
                },
                419: function(){
                    window.location.href = 'login'; //or what ever is your login URI
                }
            },
        },
        columns: [
            { title: 'Unit'         , data: 'label_title'        , name: 'label_title'},
            { title: 'Avg Unit Price'  , data: 'avg_unit_price' , name: 'avg_unit_price' },
            { title: 'Quantity Sum'    , data: 'quantity_sum'   , name: 'quantity_sum'   },
            { title: 'Value Sum (USD)' , data: 'value_sum_usd'  , name: 'value_sum_usd'  },
            { title: 'Record Count'    , data: 'record_count'   , name: 'record_count'   },
            { title: 'Duty Sum'        , data: 'duty_sum'       , name: 'duty_sum'       }
        ],
        scrollX:        true,
        scrollCollapse: true,
        fixedColumns: true,
        "dom": '<"top"ilf>rt<"bottom"ip><"clear">'
      })

    }
    function importer_pricecompare(form_data){
        form_data = JSON.parse(localStorage.getItem("search") || "[]");
     console.log(form_data);
      var postData = {}; var data={};
      if(localStorage.getItem("search") != null) {
          postData = form_data.reduce(function(a, x) { a[x.name] = x.value; return a; }, {});
      } else {
          postData['_token'] = "{{ csrf_token() }}";
      }
      $.ajax({
            
          url: '{!! route('importerbill.ga_pc_usd_country_max') !!}',
          method: "POST",
          data:postData,
          beforeSend: function() {
                    $("#loading-chart_pc_unit_usd_country_max").show();
                    $("#chart_pc_unit_usd_country_max_chart").hide();
                },
          success: function(data) {
            $("#loading-chart_pc_unit_usd_country_max").hide();
            $("#chart_pc_unit_usd_country_max_chart").show();
            localStorage.setItem("importer_priCom_data",(localStorage.getItem("importer_priCom_data")-1));


              var chdata = []
              for(var i in data.data) {
                  chdata.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
              }
              //console.log(chdata);

              Highcharts.chart('chart_pc_unit_usd_country_max', {
                  chart: {
                      type: 'pie',
                      options3d: {
                          enabled: true,
                          alpha: 45
                      }
                  },
                  title: {
                      text: 'Max Unit Price In USD'
                  },
                  subtitle: {
                      text: 'Country Wise'
                  },
                  plotOptions: {
                      pie: {
                          innerSize: 100,
                          depth: 45
                      }
                  },
                  series: [{
                      name: 'Max Unit Price Value in USD',
                      data: chdata
                  }]
              });
          },
          error: function(data) {
              console.log(data);
          }
      });

      $.ajax({
          url: '{!! route('importerbill.ga_pc_qua_country_max') !!}',
          method: "POST",
          data:postData,
          beforeSend: function() {
            $("#loading-chart_pc_quantity_country_max").show();
            $("#chart_pc_quantity_country_max_chart").hide();
            },

          success: function(data) {
            $("#loading-chart_pc_quantity_country_max").hide();
            $("#chart_pc_quantity_country_max_chart").show();
            localStorage.setItem("importer_priCom_data",(localStorage.getItem("importer_priCom_data")-1));
              var chdata = []
              for(var i in data.data) {
                  chdata.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
              }
              //console.log(chdata);

              Highcharts.chart('chart_pc_quantity_country_max', {
                  chart: {
                      type: 'pie',
                      options3d: {
                          enabled: true,
                          alpha: 45
                      }
                  },
                  title: {
                      text: 'Max Quantity'
                  },
                  subtitle: {
                      text: 'Country Wise'
                  },
                  plotOptions: {
                      pie: {
                          innerSize: 100,
                          depth: 45
                      }
                  },
                  series: [{
                      name: 'Quantity Value',
                      data: chdata
                  }]
              });
          },
          error: function(data) {
              console.log(data);
          }
      });
      $.ajax({
          url: '{!! route('importerbill.ga_pc_usd_country_min') !!}',
          method: "POST",
          data:postData,
          beforeSend: function() {
            $("#loading-chart_pc_unit_usd_country_min").show();
            $("#chart_pc_unit_usd_country_min_chart").hide();
            },

          success: function(data) {
            $("#loading-chart_pc_unit_usd_country_min").hide();
            $("#chart_pc_unit_usd_country_min_chart").show();
            localStorage.setItem("importer_priCom_data",(localStorage.getItem("importer_priCom_data")-1));
              var chdata = []
              for(var i in data.data) {
                  chdata.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
              }
              //console.log(chdata);

              Highcharts.chart('chart_pc_unit_usd_country_min', {
                  chart: {
                      type: 'pie',
                      options3d: {
                          enabled: true,
                          alpha: 45
                      }
                  },
                  title: {
                      text: 'Min Unit Price In USD'
                  },
                  subtitle: {
                      text: 'Country Wise'
                  },
                  plotOptions: {
                      pie: {
                          innerSize: 100,
                          depth: 45
                      }
                  },
                  series: [{
                      name: 'Min Unit Price Value in USD',
                      data: chdata
                  }]
              });
          },
          error: function(data) {
              console.log(data);
          }
      });
      $.ajax({
          url: '{!! route('importerbill.ga_pc_qua_country_min') !!}',
          method: "POST",
          data:postData,
          beforeSend: function() {
            $("#loading-chart_pc_quantity_country_min").show();
            $("#chart_pc_quantity_country_min_chart").hide();
            },
          success: function(data) {
            $("#loading-chart_pc_quantity_country_min").hide();
            $("#chart_pc_quantity_country_min_chart").show();
            localStorage.setItem("importer_priCom_data",(localStorage.getItem("importer_priCom_data")-1));
              var chdata = []
              for(var i in data.data) {
                  chdata.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
              }
              //console.log(chdata);

              Highcharts.chart('chart_pc_quantity_country_min', {
                  chart: {
                      type: 'pie',
                      options3d: {
                          enabled: true,
                          alpha: 45
                      }
                  },
                  title: {
                      text: 'Min Quantity'
                  },
                  subtitle: {
                      text: 'Country Wise'
                  },
                  plotOptions: {
                      pie: {
                          innerSize: 100,
                          depth: 45
                      }
                  },
                  series: [{
                      name: 'Quantity Value',
                      data: chdata
                  }]
              });
          },
          error: function(data) {
              console.log(data);
          }
      });
        $.ajax({
            url: '{!! route('importerbill.ga_pc_usd_port_max') !!}',
            method: "POST",
            data:postData,
            beforeSend: function() {
            $("#loading-chart_pc_unit_usd_port_max").show();
            $("#chart_pc_unit_usd_port_max_chart").hide();

            },

            success: function(data) {
            $("#loading-chart_pc_unit_usd_port_max").hide();
            $("#chart_pc_unit_usd_port_max_chart").show();
            localStorage.setItem("importer_priCom_data",(localStorage.getItem("importer_priCom_data")-1));
                var chdata = []
                for(var i in data.data) {
                    chdata.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
                }
                //console.log(chdata);

                Highcharts.chart('chart_pc_unit_usd_port_max', {
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 45
                        }
                    },
                    title: {
                        text: 'Max Unit Price In USD'
                    },
                    subtitle: {
                        text: 'Port Wise'
                    },
                    plotOptions: {
                        pie: {
                            innerSize: 100,
                            depth: 45
                        }
                    },
                    series: [{
                        name: 'Max Unit Price Value in USD',
                        data: chdata
                    }]
                });
            },
            error: function(data) {
                console.log(data);
            }
        });

        $.ajax({
            url: '{!! route('importerbill.ga_pc_qua_port_max') !!}',
            method: "POST",
            data:postData,
            beforeSend: function() {
                $("#loading-chart_pc_quantity_port_max").show();
                $("#chart_pc_quantity_port_max_chart").hide();
            },
            success: function(data) {
                $("#loading-chart_pc_quantity_port_max").hide();
                $("#chart_pc_quantity_port_max_chart").show();
                localStorage.setItem("importer_priCom_data",(localStorage.getItem("importer_priCom_data")-1));
                var chdata = []
                for(var i in data.data) {
                    chdata.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
                }
                //console.log(chdata);

                Highcharts.chart('chart_pc_quantity_port_max', {
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 45
                        }
                    },
                    title: {
                        text: 'Max Quantity'
                    },
                    subtitle: {
                        text: 'Port Wise'
                    },
                    plotOptions: {
                        pie: {
                            innerSize: 100,
                            depth: 45
                        }
                    },
                    series: [{
                        name: 'Quantity Value',
                        data: chdata
                    }]
                });
            },
            error: function(data) {
                console.log(data);
            }
        });
        $.ajax({
            url: '{!! route('importerbill.ga_pc_usd_port_min') !!}',
            method: "POST",
            data:postData,
            beforeSend: function() {
            $("#loading-chart_pc_unit_usd_port_min").show();
            $("#chart_pc_unit_usd_port_min_chart").hide();

            },

            success: function(data) {
                $("#loading-chart_pc_unit_usd_port_min").hide();
                $("#chart_pc_unit_usd_port_min_chart").show();
                localStorage.setItem("importer_priCom_data",(localStorage.getItem("importer_priCom_data")-1));
                var chdata = []
                for(var i in data.data) {
                    chdata.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
                }
                //console.log(chdata);

                Highcharts.chart('chart_pc_unit_usd_port_min', {
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 45
                        }
                    },
                    title: {
                        text: 'Min Unit Price In USD'
                    },
                    subtitle: {
                        text: 'Port Wise'
                    },
                    plotOptions: {
                        pie: {
                            innerSize: 100,
                            depth: 45
                        }
                    },
                    series: [{
                        name: 'Min Unit Price Value in USD',
                        data: chdata
                    }]
                });
            },
            error: function(data) {
                console.log(data);
            }
        });
        $.ajax({
            url: '{!! route('importerbill.ga_pc_qua_port_min') !!}',
            method: "POST",
            data:postData,
            beforeSend: function() {
            $("#loading-chart_pc_quantity_port_min").show();
            $("#chart_pc_quantity_port_min_chart").hide();

 },

            success: function(data) {
                $("#loading-chart_pc_quantity_port_min").hide();
                $("#chart_pc_quantity_port_min_chart").show();
                localStorage.setItem("importer_priCom_data",(localStorage.getItem("importer_priCom_data")-1));
                var chdata = []
                for(var i in data.data) {
                    chdata.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
                }
                //console.log(chdata);

                Highcharts.chart('chart_pc_quantity_port_min', {
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 45
                        }
                    },
                    title: {
                        text: 'Min Quantity'
                    },
                    subtitle: {
                        text: 'Port Wise'
                    },
                    plotOptions: {
                        pie: {
                            innerSize: 100,
                            depth: 45
                        }
                    },
                    series: [{
                        name: 'Quantity Value',
                        data: chdata
                    }]
                });
            },
            error: function(data) {
                console.log(data);
            }
        });
    }

    function exporter_datatable(form_data) {

        
        form_data = JSON.parse(localStorage.getItem("search") || "[]");
        console.log(form_data);

        var postData = {}; var data={};

        if(localStorage.getItem("search") != null) {
            postData = form_data.reduce(function(a, x) { a[x.name] = x.value; return a; }, {});
        } else {
            postData['_token'] = "{{ csrf_token() }}";
        }
        var exporter_table = $('#importer-table').DataTable({
            processing: true,
            serverSide: true,
            processData: false,
            contentType: false,
            ajax: {
                url:'{!! route('exporterbill.get_ajax') !!}',
                type:'POST',
                data: postData,
            error: function (xhr, error, code)
            {
                console.log(xhr);
                console.log(code);
            },
            statusCode: {
                401: function() {
                    window.location.href = 'login'; //or what ever is your login URI
                },
                419: function(){
                    window.location.href = 'login'; //or what ever is your login URI
                }
            },
            },
            columns: [

                /*{ title: 'Id'                       , data: 'id'                       , name: 'id'},*/
                { title: ''                         , data: null, 'defaultContent': ''},
                { title: 'Shipping Bill No'         , data: 'shipping_bill_no'         , name: 'shipping_bill_no'      , "visible": false},
                { title: 'Reg Date'                 , data: 'shipping_bill_date'       , name: 'shipping_bill_date'   },
                { title: 'IEC'                      , data: 'iec'                      , name: 'iec'          , "visible": false},
                { title: 'Exporter'                 , data: 'exporter'                 , name: 'exporter'        },
                { title: 'Exporter Address'         , data: 'exporter_address_n_city'  , name: 'exporter_address_n_city', "visible": false },
                { title: 'City'                     , data: 'city'                     , name: 'city'                 , "visible": false },
                { title: 'PIN Code'                 , data: 'pin'                      , name: 'pin'                  , "visible": false },
                { title: 'State'                    , data: 'state'                    , name: 'state'                , "visible": false },
                { title: 'Contact No'               , data: 'contact_no'               , name: 'contact_no'           , "visible": false },
                { title: 'Email'                    , data: 'e_mail_id'                , name: 'e_mail_id'            , "visible": false },
                { title: 'Consinee'                 , data: 'consinee'                 , name: 'consinee'             },
                { title: 'Consinee Address'         , data: 'consinee_address'         , name: 'consinee_address'     , "visible": false },
                { title: 'Port Code'                , data: 'port_code'                , name: 'port_code'            , "visible": false },
                { title: 'Foreign Port'             , data: 'foreign_port'             , name: 'foreign_port'         },
                { title: 'Foreign Country'          , data: 'foreign_country'          , name: 'foreign_country'      },
                { title: 'HS Code'                  , data: 'hs_code'                  , name: 'hs_code'              },
                { title: 'Chapter'                  , data: 'chapter'                  , name: 'chapter'              },
                { title: 'Product Descripition'     , data: 'product_descripition'     , name: 'product_descripition' },
                { title: 'Quantity'                 , data: 'quantity'                 , name: 'quantity'             },
                { title: 'Unit Quantity'            , data: 'unit_quantity'            , name: 'unit_quantity'        },
                { title: 'Item Rate In Fc'          , data: 'item_rate_in_fc'          , name: 'item_rate_in_fc'      },
                { title: 'Currency'                 , data: 'currency'                 , name: 'currency'             },
                { title: 'Total Value In USD'       , data: 'total_value_in_usd'       , name: 'total_value_in_usd'   },
                { title: 'Unit Rate in USD'         , data: 'unit_rate_in_usd_exchange', name: 'unit_rate_in_usd_exchange', "visible": false },
                { title: 'Exchange Rate USD'        , data: 'exchange_rate_usd'        , name: 'exchange_rate_usd'    , "visible": false },
                { title: 'Total Value in USD (Exchange)', data: 'total_value_in_usd_exchange' , name: 'total_value_in_usd_exchange' },
                { title: 'Unit Value in INR'        , data: 'unit_value_in_inr'        , name: 'unit_value_in_inr'    },
                { title: 'FOB In INR'               , data: 'fob_in_inr'               , name: 'fob_in_inr'  },
                { title: 'Invoice Sr. No.'          , data: 'invoice_serial_number'    , name: 'invoice_serial_number', "visible": false },
                { title: 'Invoice No.'              , data: 'invoice_number'           , name: 'invoice_number'       , "visible": false },
                { title: 'Item No.'                 , data: 'item_number'              , name: 'item_number'          , "visible": false },
                { title: 'Drawback'                 , data: 'drawback'                 , name: 'drawback'             , "visible": false },
                { title: 'Month'                    , data: 'month'                    , name: 'month'                },
                { title: 'Year'                     , data: 'year'                     , name: 'year'                 },
                { title: 'Mode'                     , data: 'mode'                     , name: 'mode'                 , "visible": false },
                { title: 'Indian Port'              , data: 'indian_port'              , name: 'indian_port'          },
                { title: 'CUSH'                     , data: 'cush'                     , name: 'cush'                 , "visible": false },
                /*{ title: 'Created by', data: 'created_by'         , name: 'created_by', "visible": false },
                { title: 'Updated by', data: 'updated_by'         , name: 'updated_by', "visible": false },
                { title: 'Created at', data: 'created_at'         , name: 'created_at', "visible": false },
                { title: 'Updated at', data: 'updated_at'         , name: 'updated_at', "visible": false }*/

            ],
            columnDefs: [
                { width: 30, searchable: false, orderable: false, className: 'select-checkbox', targets: 0 },
                { width: 110, targets: 2 },
                { width: 200, targets: 4 },
                { width: 250, targets: 11 },
                { width: 200, targets: 12 },
                { width: 110, targets: 14 },
                { width: 130, targets: 15 },
                { width: 80, targets: 16 },
                { width: 80, targets: 17 },
                { width: 700, targets: 18 },
                { width: 100, targets: 19 },
                { width: 110, targets: 20 },
                { width: 200, targets: 21 },
                { width: 170, targets: 22 },
                { width: 120, targets: 23 },
                { width: 210, targets: 26 },
                { width: 140, targets: 27 },
                { width: 160, targets: 28 },
                { width: 250, targets: 36 },

            ],
            select: {
                style:    'multi',
                selector: 'td:first-child'
            },
            scrollX:        true,
            scrollCollapse: true,
            fixedColumns: true,
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            dom: '<T<"clear">i<"clear">B<"clear">lf<rt>p>',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel-o" style="color: green;"></i> Download All',
                    titleAttr: 'Excel',
                    exportOptions: {
                        columns: ':not(:first-child)'
                    },
                    action: newexportaction1
                },
                /*
                {
                    extend: 'excel',
                    text: 'Download all',
                    exportOptions: {
                        modifier: {
                            // DataTables core
                            order : 'index',  // 'current', 'applied', 'index',  'original'
                            page : 'all',      // 'all',     'current'
                            search : 'none',     // 'none',    'applied', 'removed'
                            selected: null,
                        },
                        columns: ':not(:first-child)'
                    },
                    action: function ( e, dt, node, config ) {
                        var that = this, txt='';
                        // show sweetalert ...
                        exporter_table.rows().select();

                        if(!$('.dataTables_scrollHeadInner table thead th.select-checkbox').parent().hasClass('selected')){
                            $('.dataTables_scrollHeadInner table thead th.select-checkbox').parent().addClass('selected')
                        }
                        if(dt.rows( '.selected' ).count()) {
                            if ('{{ Auth::user()->hasRole('Administrator') }}' == '1' || '{{ Auth::user()->hasRole('Manager') }}' == '1'
                                || '{{ Auth::user()->hasRole('Supervisor') }}' == '1') {
                                //console.log('{{ Auth::user()->user_role['name'] }}');
                                //console.log('is Administrator = {{ Auth::user()->hasRole('Administrator') }}');
                                //console.log('is Manager = {{ Auth::user()->hasRole('Manager') }}');
                                //console.log('is Supervisor = {{ Auth::user()->hasRole('Supervisor') }}');
                                setTimeout(function() {
                                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(that, e, dt, node, config);
                                    //hide sweetalert
                                }, 50);
                            }
                            else if ('{{ Auth::user()->hasRole('User') }}' == '1') {
                                //console.log('is User = {{ Auth::user()->hasRole('User') }}');
                                var postData = {}; var data={};
                                postData['_token'] = "{{ csrf_token() }}";

                                $.ajax({
                                    url: '{!! route('importerbill.get_ajax_points_bal') !!}',
                                    method: "POST",
                                    data: postData,
                                    success: function (user_points) {
                                        //console.log(user_points);
                                        if (user_points >= dt.rows('.selected').count()) {
                                            postData['drows'] = dt.rows('.selected').count();
                                            $('#nav_user_points').text($('#nav_user_points').text() - dt.rows('.selected').count() );
                                            $.ajax({
                                                url: '{!! route('importerbill.put_ajax_points') !!}',
                                                method: "POST",
                                                data: postData,
                                                success: function (data) {
                                                    console.log(data);
                                                },
                                                error: function (data) {
                                                    console.log(data);
                                                    alert('Error Updating Points.');
                                                }
                                            });
                                            setTimeout(function() {
                                                $.fn.dataTable.ext.buttons.excelHtml5.action.call(that, e, dt, node, config);
                                                //hide sweetalert
                                            }, 50);
                                        }
                                        else {
                                            alert('Insuficient Points');
                                        }
                                    },
                                    error: function (data) {
                                        console.log(data);
                                        alert('Error Getting Points.');
                                    }
                                });
                            }
                        }
                        else {
                            alert('No row selected');
                        }
                    }
                },
                */
                {
                    extend: 'excel',
                    text: 'Download selected',
                    exportOptions: {
                        columns: ':not(:first-child)'
                    },
                    action: function ( e, dt, node, config ) {
                        var that = this, txt='';
                        // show sweetalert ...
                        if(dt.rows( '.selected' ).count()) {
                            if ('{{ Auth::user()->hasRole('Administrator') }}' == '1' || '{{ Auth::user()->hasRole('Manager') }}' == '1'
                                || '{{ Auth::user()->hasRole('Supervisor') }}' == '1') {
                                //console.log('{{ Auth::user()->user_role['name'] }}');
                                //console.log('is Administrator = {{ Auth::user()->hasRole('Administrator') }}');
                                //console.log('is Manager = {{ Auth::user()->hasRole('Manager') }}');
                                //console.log('is Supervisor = {{ Auth::user()->hasRole('Supervisor') }}');
                                setTimeout(function() {
                                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(that, e, dt, node, config);
                                    //hide sweetalert
                                }, 50);
                            }
                            else if ('{{ Auth::user()->hasRole('User') }}' == '1') {
                                //console.log('is User = {{ Auth::user()->hasRole('User') }}');
                                var postData = {}; var data={};
                                postData['_token'] = "{{ csrf_token() }}";

                                $.ajax({
                                    url: '{!! route('importerbill.get_ajax_points_bal') !!}',
                                    method: "POST",
                                    data: postData,
                                    success: function (user_points) {
                                        //console.log(user_points);
                                        if (user_points >= dt.rows('.selected').count()) {
                                            postData['drows'] = dt.rows('.selected').count();
                                            $('#nav_user_points').text($('#nav_user_points').text() - dt.rows('.selected').count() );
                                            $.ajax({
                                                url: '{!! route('importerbill.put_ajax_points') !!}',
                                                method: "POST",
                                                data: postData,
                                                success: function (data) {
                                                    console.log(data);
                                                },
                                                error: function (data) {
                                                    console.log(data);
                                                    alert('Error Updating Points.');
                                                }
                                            });
                                            setTimeout(function() {
                                                $.fn.dataTable.ext.buttons.excelHtml5.action.call(that, e, dt, node, config);
                                                //hide sweetalert
                                            }, 50);
                                        }
                                        else {
                                            alert('Insuficient Points');
                                        }
                                    },
                                    error: function (data) {
                                        console.log(data);
                                        alert('Error Getting Points.');
                                    }
                                });
                            }
                        }
                        else {
                            alert('No row selected');
                        }
                    }
                },
                /*{
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel-o" style="color: green;"></i>Download All',
                    titleAttr: 'Excel',
                    exportOptions: {
                        columns: ':not(:first-child)'
                    },
                    action: newexportaction
                },*/
            ],
        });
        //exporter_table.
        $('.dataTables_scrollHeadInner table').on( 'click', 'thead th.select-checkbox', function () {
            if($(this).parent().hasClass('selected')){
                $(this).parent().removeClass('selected');
                exporter_table.rows().deselect();
            }
            else {
                $(this).parent().addClass('selected');
                exporter_table.rows().select();
            }
            console.log('Datatable Select All/None hit');
        } );
    }
    function exporter_dashboard(form_data){
        
        // alert('exporter_dashboard');
        form_data = JSON.parse(localStorage.getItem("search") || "[]");
        console.log(form_data);

        var postData = {}; var data={};
        if(localStorage.getItem("search") != null) {
              postData = form_data.reduce(function(a, x) { a[x.name] = x.value; return a; }, {});
          } else {
              postData['_token'] = "{{ csrf_token() }}";
          }
          $.ajax({
              url: '{!! route('exporterbill.get_ajax_top_usd') !!}',
              method: "POST",
              data:postData,
              beforeSend: function() {
                $("#loading-bigDashboardChart").show();
                $("#bigDashboardChart_chart").hide();
            },
              success: function(data) {
                $("#loading-bigDashboardChart").hide();
                $("#bigDashboardChart_chart").show();
                localStorage.setItem("exporter_dashboard_data",(localStorage.getItem("exporter_dashboard_data")-1));
                  var top15_usd = [];
                  var top15_exporters = [];

                  for(var i in data.data) {
                      top15_exporters.push(data.data[i].exporter.substring(0, 8));
                      top15_usd.push(parseFloat(data.data[i].top15_usd));
                  }
                  //console.log(top15_exporters);
                  //console.log(top15_usd);
                  $('#dashboard_bigchart_h2_span').html('Exporters');
                  var ctx = document.getElementById('bigDashboardChart').getContext("2d");

                  var gradientStroke = ctx.createLinearGradient(500, 0, 100, 0);
                  gradientStroke.addColorStop(0, '#80b6f4');
                  gradientStroke.addColorStop(1, chartColor);

                  var gradientFill = ctx.createLinearGradient(0, 200, 0, 50);
                  gradientFill.addColorStop(0, "rgba(128, 182, 244, 0)");
                  gradientFill.addColorStop(1, "rgba(255, 255, 255, 0.24)");

                  if(myChartDashboard != null) // i initialize myBarChart var with null
                      myChartDashboard.destroy(); // if not null call destroy

                  myChartDashboard = new Chart(ctx, {
                      type: 'bar',
                      data: {
                          //labels: ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"],
                          labels: top15_exporters,
                          datasets: [{
                              label: "Data",
                              borderColor: chartColor,
                              pointBorderColor: chartColor,
                              pointBackgroundColor: "#1e3d60",
                              pointHoverBackgroundColor: "#1e3d60",
                              pointHoverBorderColor: chartColor,
                              pointBorderWidth: 1,
                              pointHoverRadius: 7,
                              pointHoverBorderWidth: 2,
                              pointRadius: 5,
                              fill: true,
                              backgroundColor: gradientFill,
                              borderWidth: 2,
                              //data: [50, 150, 100, 190, 130, 90, 150, 160, 120, 140, 190, 95]
                              data: top15_usd
                          }]
                      },
                      options: {
                          layout: {
                              padding: {
                                  left: 20,
                                  right: 20,
                                  top: 0,
                                  bottom: 0
                              }
                          },
                          maintainAspectRatio: false,
                          tooltips: {
                              backgroundColor: '#fff',
                              titleFontColor: '#333',
                              bodyFontColor: '#666',
                              bodySpacing: 4,
                              xPadding: 12,
                              mode: "nearest",
                              intersect: 0,
                              position: "nearest"
                          },
                          legend: {
                              position: "bottom",
                              fillStyle: "#FFF",
                              display: false
                          },
                          scales: {
                              yAxes: [{
                                  ticks: {
                                      fontColor: "rgba(255,255,255,0.4)",
                                      fontStyle: "bold",
                                      beginAtZero: true,
                                      maxTicksLimit: 5,
                                      padding: 10
                                  },
                                  gridLines: {
                                      drawTicks: true,
                                      drawBorder: false,
                                      display: true,
                                      color: "rgba(255,255,255,0.1)",
                                      zeroLineColor: "transparent"
                                  }

                              }],
                              xAxes: [{
                                  gridLines: {
                                      zeroLineColor: "transparent",
                                      display: false,

                                  },
                                  ticks: {
                                      padding: 10,
                                      fontColor: "rgba(255,255,255,0.4)",
                                      fontStyle: "bold"
                                  }
                              }]
                          }
                      }
                  });

                  var cardStatsMiniLineColor = "#fff",
                      cardStatsMiniDotColor = "#fff";
              },
              error: function(data) {
                  console.log(data);
              }
          });
          $.ajax({
              url: '{!! route('exporterbill.get_ajax_top_usd_port') !!}',
              method: "POST",
              data:postData,
              beforeSend: function() {
                    $("#loading-container_top15_usd_port").show();
                    $("#container_top15_usd_port_chart").hide();
                },
              success: function(data) {
                $("#loading-container_top15_usd_port").hide();
                $("#container_top15_usd_port_chart").show();
                localStorage.setItem("exporter_dashboard_data",(localStorage.getItem("exporter_dashboard_data")-1));
                  var chdata41 = [];
                  for(var i in data.data) {
                      chdata41.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
                  }
                  //console.log(chdata41);

                  Highcharts.chart('container_top15_usd_port', {
                      chart: {
                          type: 'pie',
                          options3d: {
                              enabled: true,
                              alpha: 45
                          }
                      },
                      title: {
                          text: 'Market Share by Cost Value in USD'
                      },
                      subtitle: {
                          text: 'Port Wise'
                      },
                      plotOptions: {
                          pie: {
                              innerSize: 100,
                              depth: 45
                          }
                      },
                      series: [{
                          name: 'Total Value USD',
                          data: chdata41
                      }]
                  });
              },
              error: function(data) {
                  console.log(data);
              }
          });
          $.ajax({
              url: '{!! route('exporterbill.get_ajax_top_usd_country') !!}',
              method: "POST",
              data:postData,
              beforeSend: function() {
                    $("#loading-chart_top15_usd_country").show();
                    $("#chart_top15_usd_country_chart").hide();
                },
              success: function(data) {
                $("#loading-chart_top15_usd_country").hide();
                $("#chart_top15_usd_country_chart").show();
                localStorage.setItem("exporter_dashboard_data",(localStorage.getItem("exporter_dashboard_data")-1));
                  var chdata42= [];
                  for(var i in data.data) {
                      chdata42.push([data.data[i].foreign_country, parseFloat(data.data[i].top15_usd)]);
                  }
                  //console.log(chdata42);

                  Highcharts.chart('chart_top15_usd_country', {
                      chart: {
                          type: 'pie',
                          options3d: {
                              enabled: true,
                              alpha: 45
                          }
                      },
                      title: {
                          text: 'Market Share by Cost Value in USD'
                      },
                      subtitle: {
                          text: 'Country Wise'
                      },
                      plotOptions: {
                          pie: {
                              innerSize: 100,
                              depth: 45
                          }
                      },
                      series: [{
                          name: 'Total Value USD',
                          data: chdata42
                      }]
                  });
              },
              error: function(data) {
                  console.log(data);
              }
          });


      }
    function exporter_consinee_analysis(form_data){
       
        var postData = {}; var data={};
        if(typeof form_data !== 'undefined') {
            postData = form_data.reduce(function(a, x) { a[x.name] = x.value; return a; }, {});
        } else {
            postData['_token'] = "{{ csrf_token() }}";
        }
        $.ajax({
            url: '{!! route('exporterbill.ga_exp_conana_usd_sup') !!}',
            method: "POST",
            data:postData,
            success: function(data) {
                localStorage.setItem("exporter_ImAna_data",(localStorage.getItem("exporter_ImAna_data")-1));
                var expana_consinee = [];
                var exp_conana_usd = [];

                for(var i in data.data) {
                    expana_consinee.push(data.data[i].consinee.substring(0, 8));
                    exp_conana_usd.push(parseFloat(data.data[i].top15_usd));
                }
                console.log(expana_consinee);
                console.log(exp_conana_usd);
                $('#importerana_bigchart_h2_span').html('Importers');
                var ctx = document.getElementById('bigImporterAnaChart').getContext("2d");

                var gradientStroke = ctx.createLinearGradient(500, 0, 100, 0);
                gradientStroke.addColorStop(0, '#80b6f4');
                gradientStroke.addColorStop(1, chartColor);

                var gradientFill = ctx.createLinearGradient(0, 200, 0, 50);
                gradientFill.addColorStop(0, "rgba(128, 182, 244, 0)");
                gradientFill.addColorStop(1, "rgba(255, 255, 255, 0.24)");

                if(myChartImpAna != null) // i initialize myBarChart var with null
                    myChartImpAna.destroy(); // if not null call destroy

                myChartImpAna = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        //labels: ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"],
                        labels: expana_consinee,
                        datasets: [{
                            label: "Data",
                            borderColor: chartColor,
                            pointBorderColor: chartColor,
                            pointBackgroundColor: "#1e3d60",
                            pointHoverBackgroundColor: "#1e3d60",
                            pointHoverBorderColor: chartColor,
                            pointBorderWidth: 1,
                            pointHoverRadius: 7,
                            pointHoverBorderWidth: 2,
                            pointRadius: 5,
                            fill: true,
                            backgroundColor: gradientFill,
                            borderWidth: 2,
                            //data: [50, 150, 100, 190, 130, 90, 150, 160, 120, 140, 190, 95]
                            data: exp_conana_usd
                        }]
                    },
                    options: {
                        layout: {
                            padding: {
                                left: 20,
                                right: 20,
                                top: 0,
                                bottom: 0
                            }
                        },
                        maintainAspectRatio: false,
                        tooltips: {
                            backgroundColor: '#fff',
                            titleFontColor: '#333',
                            bodyFontColor: '#666',
                            bodySpacing: 4,
                            xPadding: 12,
                            mode: "nearest",
                            intersect: 0,
                            position: "nearest"
                        },
                        legend: {
                            position: "bottom",
                            fillStyle: "#FFF",
                            display: false
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    fontColor: "rgba(255,255,255,0.4)",
                                    fontStyle: "bold",
                                    beginAtZero: true,
                                    maxTicksLimit: 5,
                                    padding: 10
                                },
                                gridLines: {
                                    drawTicks: true,
                                    drawBorder: false,
                                    display: true,
                                    color: "rgba(255,255,255,0.1)",
                                    zeroLineColor: "transparent"
                                }

                            }],
                            xAxes: [{
                                gridLines: {
                                    zeroLineColor: "transparent",
                                    display: false,

                                },
                                ticks: {
                                    padding: 10,
                                    fontColor: "rgba(255,255,255,0.4)",
                                    fontStyle: "bold"
                                }
                            }]
                        }
                    }
                });

                var cardStatsMiniLineColor = "#fff",
                    cardStatsMiniDotColor = "#fff";
            },
            error: function(data) {
                console.log(data);
            }
        });
        $.ajax({
            url: '{!! route('exporterbill.ga_exp_conana_usd_cost') !!}',
            method: "POST",
            data:postData,
            success: function(data) {
                localStorage.setItem("exporter_ImAna_data",(localStorage.getItem("exporter_ImAna_data")-1));
                var chdata = []
                for(var i in data.data) {
                    chdata.push([data.data[i].consinee, parseFloat(data.data[i].top15_usd)]);
                }
                //console.log(chdata);

                Highcharts.chart('container_importerana_port', {
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 45
                        }
                    },
                    title: {
                        text: 'Exporter Market Share Total Cost'
                    },
                    subtitle: {
                        text: 'In USD'
                    },
                    plotOptions: {
                        pie: {
                            innerSize: 100,
                            depth: 45
                        }
                    },
                    series: [{
                        name: 'Total Value USD',
                        data: chdata
                    }]
                });
            },
            error: function(data) {
                console.log(data);
            }
        });
        $.ajax({
            url: '{!! route('exporterbill.ga_exp_conana_usd_quantity') !!}',
            method: "POST",
            data:postData,
            success: function(data) {
                localStorage.setItem("exporter_ImAna_data",(localStorage.getItem("exporter_ImAna_data")-1));
                var chdata= [];
                for(var i in data.data) {
                    chdata.push([data.data[i].consinee, parseFloat(data.data[i].top_quantity)]);
                }
                //console.log(chdata);

                Highcharts.chart('chart_importerana_country', {
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 45
                        }
                    },
                    title: {
                        text: 'Exporter Market Share by Quantity'
                    },
                    subtitle: {
                        text: ''
                    },
                    plotOptions: {
                        pie: {
                            innerSize: 100,
                            depth: 45
                        }
                    },
                    series: [{
                        name: 'Total Quantity',
                        data: chdata
                    }]
                });
            },
            error: function(data) {
                console.log(data);
            }
        });


    }
    function exporter_analysis(form_data){
       
        form_data = JSON.parse(localStorage.getItem("search") || "[]");
        console.log(form_data);


          var postData = {}; var data={};

          if(localStorage.getItem("search") != null) {
              postData = form_data.reduce(function(a, x) { a[x.name] = x.value; return a; }, {});
          } else {
              postData['_token'] = "{{ csrf_token() }}";
          }
          $.ajax({
              url: '{!! route('exporterbill.get_ajax_expana_usd_sup') !!}',
              method: "POST",
              data:postData,
              success: function(data) {
                localStorage.setItem("exporter_ExAna_data",(localStorage.getItem("exporter_ExAna_data")-1));
                  var expana_exporters = [];
                  var expana_usd = [];

                  for(var i in data.data) {
                      expana_exporters.push(data.data[i].exporter.substring(0, 8));
                      expana_usd.push(parseFloat(data.data[i].top15_usd));
                  }
                  //console.log(expana_exporters);
                  //console.log(expana_usd);
                  $('#exporterana_bigchart_h2_span').html('Suppliers');
                  var ctx = document.getElementById('bigExporterAnaChart').getContext("2d");

                  var gradientStroke = ctx.createLinearGradient(500, 0, 100, 0);
                  gradientStroke.addColorStop(0, '#80b6f4');
                  gradientStroke.addColorStop(1, chartColor);

                  var gradientFill = ctx.createLinearGradient(0, 200, 0, 50);
                  gradientFill.addColorStop(0, "rgba(128, 182, 244, 0)");
                  gradientFill.addColorStop(1, "rgba(255, 255, 255, 0.24)");

                  if(myChartExpAna != null) // i initialize myBarChart var with null
                      myChartExpAna.destroy(); // if not null call destroy

                  myChartExpAna = new Chart(ctx, {
                      type: 'bar',
                      data: {
                          //labels: ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"],
                          labels: expana_exporters,
                          datasets: [{
                              label: "Data",
                              borderColor: chartColor,
                              pointBorderColor: chartColor,
                              pointBackgroundColor: "#1e3d60",
                              pointHoverBackgroundColor: "#1e3d60",
                              pointHoverBorderColor: chartColor,
                              pointBorderWidth: 1,
                              pointHoverRadius: 7,
                              pointHoverBorderWidth: 2,
                              pointRadius: 5,
                              fill: true,
                              backgroundColor: gradientFill,
                              borderWidth: 2,
                              //data: [50, 150, 100, 190, 130, 90, 150, 160, 120, 140, 190, 95]
                              data: expana_usd
                          }]
                      },
                      options: {
                          layout: {
                              padding: {
                                  left: 20,
                                  right: 20,
                                  top: 0,
                                  bottom: 0
                              }
                          },
                          maintainAspectRatio: false,
                          tooltips: {
                              backgroundColor: '#fff',
                              titleFontColor: '#333',
                              bodyFontColor: '#666',
                              bodySpacing: 4,
                              xPadding: 12,
                              mode: "nearest",
                              intersect: 0,
                              position: "nearest"
                          },
                          legend: {
                              position: "bottom",
                              fillStyle: "#FFF",
                              display: false
                          },
                          scales: {
                              yAxes: [{
                                  ticks: {
                                      fontColor: "rgba(255,255,255,0.4)",
                                      fontStyle: "bold",
                                      beginAtZero: true,
                                      maxTicksLimit: 5,
                                      padding: 10
                                  },
                                  gridLines: {
                                      drawTicks: true,
                                      drawBorder: false,
                                      display: true,
                                      color: "rgba(255,255,255,0.1)",
                                      zeroLineColor: "transparent"
                                  }

                              }],
                              xAxes: [{
                                  gridLines: {
                                      zeroLineColor: "transparent",
                                      display: false,

                                  },
                                  ticks: {
                                      padding: 10,
                                      fontColor: "rgba(255,255,255,0.4)",
                                      fontStyle: "bold"
                                  }
                              }]
                          }
                      }
                  });

                  var cardStatsMiniLineColor = "#fff",
                      cardStatsMiniDotColor = "#fff";
              },
              error: function(data) {
                  console.log(data);
              }
          });
          $.ajax({
              url: '{!! route('exporterbill.get_ajax_expana_usd_cost') !!}',
              method: "POST",
              data:postData,
              success: function(data) {
                localStorage.setItem("exporter_ExAna_data",(localStorage.getItem("exporter_ExAna_data")-1));
                  var chdata = []
                  for(var i in data.data) {
                      chdata.push([data.data[i].exporter, parseFloat(data.data[i].top15_usd)]);
                  }
                  //console.log(chdata);

                  Highcharts.chart('container_expana_cost', {
                      chart: {
                          type: 'pie',
                          options3d: {
                              enabled: true,
                              alpha: 45
                          }
                      },
                      title: {
                          text: 'Exporter Market Share Total Cost'
                      },
                      subtitle: {
                          text: 'In USD'
                      },
                      plotOptions: {
                          pie: {
                              innerSize: 100,
                              depth: 45
                          }
                      },
                      series: [{
                          name: 'Total Value USD',
                          data: chdata
                      }]
                  });
              },
              error: function(data) {
                  console.log(data);
              }
          });
          $.ajax({
              url: '{!! route('exporterbill.get_ajax_expana_usd_quantity') !!}',
              method: "POST",
              data:postData,
              success: function(data) {
                localStorage.setItem("exporter_ExAna_data",(localStorage.getItem("exporter_ExAna_data")-1));
                  var chdata= [];
                  for(var i in data.data) {
                      chdata.push([data.data[i].exporter, parseFloat(data.data[i].top_quantity)]);
                  }
                  //console.log(chdata);

                  Highcharts.chart('container_expana_quantity', {
                      chart: {
                          type: 'pie',
                          options3d: {
                              enabled: true,
                              alpha: 45
                          }
                      },
                      title: {
                          text: 'Exporter Market Share by Quantity'
                      },
                      subtitle: {
                          text: ''
                      },
                      plotOptions: {
                          pie: {
                              innerSize: 100,
                              depth: 45
                          }
                      },
                      series: [{
                          name: 'Total Quantity',
                          data: chdata
                      }]
                  });
              },
              error: function(data) {
                  console.log(data);
              }
          });


      }
    function exporter_analysis_empty(){

      var expana_exporters10 = [];
      var expana_usd10 = [];

      $('#exporterana_bigchart_h2_span').html('Suppliers');
      var ctx = document.getElementById('bigExporterAnaChart').getContext("2d");

      /*var gradientStroke = ctx.createLinearGradient(500, 0, 100, 0);
      gradientStroke.addColorStop(0, '#80b6f4');
      gradientStroke.addColorStop(1, chartColor);*/

      var gradientFill = ctx.createLinearGradient(0, 200, 0, 50);
      gradientFill.addColorStop(0, "rgba(128, 182, 244, 0)");
      gradientFill.addColorStop(1, "rgba(255, 255, 255, 0.24)");

      if(myChartExpAna != null) // i initialize myBarChart var with null
          myChartExpAna.destroy(); // if not null call destroy

      myChartExpAna = new Chart(ctx, {
          type: 'bar',
          data: {
              //labels: ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"],
              labels: expana_exporters10,
              datasets: [{
                  label: "Data",
                  borderColor: '',
                  pointBorderColor: '',
                  pointBackgroundColor: "#1e3d60",
                  pointHoverBackgroundColor: "#1e3d60",
                  pointHoverBorderColor: '',
                  pointBorderWidth: 1,
                  pointHoverRadius: 7,
                  pointHoverBorderWidth: 2,
                  pointRadius: 5,
                  fill: true,
                  backgroundColor: gradientFill,
                  borderWidth: 2,
                  //data: [50, 150, 100, 190, 130, 90, 150, 160, 120, 140, 190, 95]
                  data: expana_usd10
              }]
          },
          options: {
              layout: {
                  padding: {
                      left: 20,
                      right: 20,
                      top: 0,
                      bottom: 0
                  }
              },
              maintainAspectRatio: false,
              tooltips: {
                  backgroundColor: '#fff',
                  titleFontColor: '#333',
                  bodyFontColor: '#666',
                  bodySpacing: 4,
                  xPadding: 12,
                  mode: "nearest",
                  intersect: 0,
                  position: "nearest"
              },
              legend: {
                  position: "bottom",
                  fillStyle: "#FFF",
                  display: false
              },
              scales: {
                  yAxes: [{
                      ticks: {
                          fontColor: "rgba(255,255,255,0.4)",
                          fontStyle: "bold",
                          beginAtZero: true,
                          maxTicksLimit: 5,
                          padding: 10
                      },
                      gridLines: {
                          drawTicks: true,
                          drawBorder: false,
                          display: true,
                          color: "rgba(255,255,255,0.1)",
                          zeroLineColor: "transparent"
                      }

                  }],
                  xAxes: [{
                      gridLines: {
                          zeroLineColor: "transparent",
                          display: false,

                      },
                      ticks: {
                          padding: 10,
                          fontColor: "rgba(255,255,255,0.4)",
                          fontStyle: "bold"
                      }
                  }]
              }
          }
      });



      var chdata09 = [];
      Highcharts.chart('container_expana_cost', {
          chart: {
              type: 'pie',
              options3d: {
                  enabled: true,
                  alpha: 45
              }
          },
          title: {
              text: 'Exporter Market Share Total Cost'
          },
          subtitle: {
              text: 'In USD'
          },
          plotOptions: {
              pie: {
                  innerSize: 100,
                  depth: 45
              }
          },
          series: [{
              name: 'Total Value USD',
              data: chdata09
          }]
      });

      var chdata10= [];
      Highcharts.chart('container_expana_quantity', {
          chart: {
              type: 'pie',
              options3d: {
                  enabled: true,
                  alpha: 45
              }
          },
          title: {
              text: 'Exporter Market Share by Quantity'
          },
          subtitle: {
              text: ''
          },
          plotOptions: {
              pie: {
                  innerSize: 100,
                  depth: 45
              }
          },
          series: [{
              name: 'Total Quantity',
              data: chdata10
          }]
      });
      }
    function exporter_marketshare(form_data){
        // alert('exporter_marketshare');
        form_data = JSON.parse(localStorage.getItem("search") || "[]");
        console.log(form_data);

          var postData = {}; var data={};
          if(localStorage.getItem("search") != null) {
              postData = form_data.reduce(function(a, x) { a[x.name] = x.value; return a; }, {});
          } else {
              postData['_token'] = "{{ csrf_token() }}";
          }
          $.ajax({
              url: '{!! route('exporterbill.ga_marketshare_cost_usd_port') !!}',
              method: "POST",
              data:postData,
              beforeSend: function() {
                    $("#loading-container_ms_cost_usd_port").show();
                    $("#container_ms_cost_usd_port_chart").hide();
                },
              success: function(data) {
                $("#loading-container_ms_cost_usd_port").hide();
                $("#container_ms_cost_usd_port_chart").show();
                localStorage.setItem("exporter_marSha_data",(localStorage.getItem("exporter_marSha_data")-1));


                  var chdata = []
                  for(var i in data.data) {
                      chdata.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
                  }
                  //console.log(chdata);

                  Highcharts.chart('container_ms_cost_usd_port', {
                      chart: {
                          type: 'pie',
                          options3d: {
                              enabled: true,
                              alpha: 45
                          }
                      },
                      title: {
                          text: 'Market Share Total Cost in USD'
                      },
                      subtitle: {
                          text: 'Port Wise'
                      },
                      plotOptions: {
                          pie: {
                              innerSize: 100,
                              depth: 45
                          }
                      },
                      series: [{
                          name: 'Total Value USD',
                          data: chdata
                      }]
                  });
              },
              error: function(data) {
                  console.log(data);
              }
          });
          $.ajax({
              url: '{!! route('exporterbill.ga_marketshare_cost_qua_port') !!}',
              method: "POST",
              data:postData,
              beforeSend: function() {
                    $("#loading-container_ms_cost_qua_port").show();
                    $("#container_ms_cost_qua_port_chart").hide();
            },
          success: function(data) {
            $("#loading-container_ms_cost_qua_port").hide();
            $("#container_ms_cost_qua_port_chart").show();
            localStorage.setItem("exporter_marSha_data",(localStorage.getItem("exporter_marSha_data")-1));

                  var chdata = []
                  for(var i in data.data) {
                      chdata.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
                  }
                  //console.log(chdata);

                  Highcharts.chart('container_ms_cost_qua_port', {
                      chart: {
                          type: 'pie',
                          options3d: {
                              enabled: true,
                              alpha: 45
                          }
                      },
                      title: {
                          text: 'Market Share by Quantity'
                      },
                      subtitle: {
                          text: 'Port Wise'
                      },
                      plotOptions: {
                          pie: {
                              innerSize: 100,
                              depth: 45
                          }
                      },
                      series: [{
                          name: 'Total Value USD',
                          data: chdata
                      }]
                  });
              },
              error: function(data) {
                  console.log(data);
              }
          });
          $.ajax({
              url: '{!! route('exporterbill.ga_marketshare_cost_qua_country') !!}',
              method: "POST",
              data:postData,
              beforeSend: function() {
                    $("#loading-container_ms_cost_qua_country").show();
                    $("#container_ms_cost_qua_country_chart").hide();
            },
          success: function(data) {
            $("#loading-container_ms_cost_qua_country").hide();
            $("#container_ms_cost_qua_country_chart").show();
            localStorage.setItem("exporter_marSha_data",(localStorage.getItem("exporter_marSha_data")-1));

                  var chdata = []
                  for(var i in data.data) {
                      chdata.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
                  }
                  //console.log(chdata);

                  Highcharts.chart('container_ms_cost_qua_country', {
                      chart: {
                          type: 'pie',
                          options3d: {
                              enabled: true,
                              alpha: 45
                          }
                      },
                      title: {
                          text: 'Market Share by Quantity'
                      },
                      subtitle: {
                          text: 'Country Wise'
                      },
                      plotOptions: {
                          pie: {
                              innerSize: 100,
                              depth: 45
                          }
                      },
                      series: [{
                          name: 'Total Value USD',
                          data: chdata
                      }]
                  });
              },
              error: function(data) {
                  console.log(data);
              }
          });
          $.ajax({
              url: '{!! route('exporterbill.ga_marketshare_cost_usd_country') !!}',
              method: "POST",
              data:postData,
              beforeSend: function() {
                    $("#loading-container_ms_cost_usd_country").show();
                    $("#container_ms_cost_usd_country_chart").hide();
                },

            success: function(data) {
                $("#loading-container_ms_cost_usd_country").hide();
                $("#container_ms_cost_usd_country_chart").show();
                localStorage.setItem("exporter_marSha_data",(localStorage.getItem("exporter_marSha_data")-1));

                  var chdata = []
                  for(var i in data.data) {
                      chdata.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
                  }
                  //console.log(chdata);

                  Highcharts.chart('container_ms_cost_usd_country', {
                      chart: {
                          type: 'pie',
                          options3d: {
                              enabled: true,
                              alpha: 45
                          }
                      },
                      title: {
                          text: 'Market Share Total Cost in USD'
                      },
                      subtitle: {
                          text: 'Country Wise'
                      },
                      plotOptions: {
                          pie: {
                              innerSize: 100,
                              depth: 45
                          }
                      },
                      series: [{
                          name: 'Total Value USD',
                          data: chdata
                      }]
                  });
              },
              error: function(data) {
                  console.log(data);
              }
          });

      }
    function exporter_priceana(form_data){

        // alert('exporter_priceana');
        form_data = JSON.parse(localStorage.getItem("search") || "[]");
        console.log(form_data);

          var postData = {}; var data={};
          if(localStorage.getItem("search") != null) {
              postData = form_data.reduce(function(a, x) { a[x.name] = x.value; return a; }, {});
          } else {
              postData['_token'] = "{{ csrf_token() }}";
          }
          $.ajax({
              url: '{!! route('exporterbill.ga_priceana_usd_country') !!}',
              method: "POST",
              data:postData,
              beforeSend: function() {
                    $("#loading-container_priceana_usd_country").show();
                },
            success: function(data) {
                $("#loading-container_priceana_usd_country").hide();
                localStorage.setItem("exporter_price_data",(localStorage.getItem("exporter_price_data")-1));

                  var pucdata = [],resdates = [], rescountry=[];

                  for(var i in data.data) {
                      resdates.push(new Date(data.data[i].week_start));
                      rescountry.push(data.data[i].labeltitle);
                  }
                  var maxDate=new Date(Math.max.apply(null,resdates)).toISOString();
                  var minDate=new Date(Math.min.apply(null,resdates)).toISOString().slice(0,10);
                  var lastSunday = new Date(lastMondayinmonth( new Date(maxDate).getMonth()+1, new Date(maxDate).getFullYear()));
                  //console.log('maxdate = '+maxDate+', mindate = '+minDate+', last sunday = '+lastSunday);
                  rescountry = Array.from(new Set(rescountry));
                  for(var i in rescountry) {
                      if (!this[rescountry[i]]) {
                          var countrydata = [];
                          var currentSunday = new Date(lastSunday);
                          var c2Sunday = currentSunday;
                          var weekdate = [];
                          while(currentSunday >= new Date(minDate)) {
                              c2Sunday = currentSunday;
                              weekdate.push(new Date(c2Sunday.setDate(c2Sunday.getDate())).toDateString().slice(3,15));
                              var pushed = false;
                              for (var ii in data.data) {
                                  if (new Date(data.data[ii].week_start).getTime() == new Date(currentSunday).getTime() && data.data[ii].labeltitle == rescountry[i]) {
                                      countrydata.push(parseFloat(data.data[ii].labelvalue));
                                      pushed = true;
                                      break;
                                  }
                              }
                              if(pushed == false){
                                  countrydata.push(parseFloat(0));
                              }
                              currentSunday = new Date(currentSunday.setDate(currentSunday.getDate() - 7));
                          }
                          this[rescountry[i]] = { name: rescountry[i], data: countrydata };
                          pucdata.push(this[rescountry[i]]);
                      }
                  }
                  //console.log('ga_priceana_usd_country');
                  //console.log(rescountry);
                  //console.log(resdates);
                  //console.log(pucdata);
                  Highcharts.chart('container_priceana_usd_country', {
                      title: {
                          text: 'Total  Value of Top 5 Countries in USD  by Time [Weekly]'
                      },

                      yAxis: {
                          title: {
                              text: 'Value In USD'
                          }
                      },

                      xAxis: {
                          tickmarkPlacement: 'between',
                          //tickmarkPlacement: 'on',
                          categories: weekdate
                      },

                      legend: {
                          layout: 'vertical',
                          align: 'right',
                          verticalAlign: 'middle'
                      },

                      plotOptions: {
                          series: {
                              label: {
                                  connectorAllowed: false
                              }
                          }
                      },

                      series: pucdata,
                      responsive: {
                          rules: [{
                              condition: {
                                  maxWidth: 500
                              },
                              chartOptions: {
                                  legend: {
                                      layout: 'horizontal',
                                      align: 'center',
                                      verticalAlign: 'bottom'

                                  },
                                  xAxis: {
                                      labels: {
                                          step: 2
                                      }
                                  }
                              }
                          }]
                      },
                      /*chart:{
                       backgroundColor: "rgba(255, 255, 255, 0)",
                       plotBackgroundColor: "#ffffff"
                       }*/
                  });
              },
              error: function(data) {
                  console.log(data);
              }
          });
          $.ajax({
              url: '{!! route('exporterbill.ga_priceana_usd_port') !!}',
              method: "POST",
              data:postData,
              beforeSend: function() {
                    $("#loading-container_priceana_usd_port").show();
                    $("#container_priceana_usd_port_chart").hide();
                    
                },

            success: function(data) {
                $("#loading-container_priceana_usd_port").hide();
                $("#container_priceana_usd_port_chart").show();
                localStorage.setItem("exporter_price_data",(localStorage.getItem("exporter_price_data")-1));

                  var pucdata = [],resdates = [], rescountry=[];

                  for(var i in data.data) {
                      resdates.push(new Date(data.data[i].week_start));
                      rescountry.push(data.data[i].labeltitle);
                  }
                  var maxDate=new Date(Math.max.apply(null,resdates)).toISOString();
                  var minDate=new Date(Math.min.apply(null,resdates)).toISOString().slice(0,10);
                  var lastSunday = new Date(lastMondayinmonth( new Date(maxDate).getMonth()+1, new Date(maxDate).getFullYear()));

                  rescountry = Array.from(new Set(rescountry));
                  for(var i in rescountry) {
                      if (!this[rescountry[i]]) {
                          var countrydata = [];
                          var currentSunday = new Date(lastSunday);
                          var c2Sunday = currentSunday;
                          var weekdate = [];
                          while(currentSunday >= new Date(minDate)) {
                              c2Sunday = currentSunday;
                              weekdate.push(new Date(c2Sunday.setDate(c2Sunday.getDate())).toDateString().slice(3,15));
                              var pushed = false;
                              for (var ii in data.data) {
                                  if (new Date(data.data[ii].week_start).getTime() == new Date(currentSunday).getTime() && data.data[ii].labeltitle == rescountry[i]) {
                                      countrydata.push(parseFloat(data.data[ii].labelvalue));
                                      pushed = true;
                                      break;
                                  }
                              }
                              if(pushed == false){
                                  countrydata.push(parseFloat(0));
                              }
                              currentSunday = new Date(currentSunday.setDate(currentSunday.getDate() - 7));
                          }
                          this[rescountry[i]] = { name: rescountry[i], data: countrydata };
                          pucdata.push(this[rescountry[i]]);
                      }
                  }
                  Highcharts.chart('container_priceana_usd_port', {
                      title: {
                          text: 'Total  Value of Top 5 Ports in USD  by Time [Weekly]'
                      },

                      yAxis: {
                          title: {
                              text: 'Value In USD'
                          }
                      },

                      xAxis: {
                          tickmarkPlacement: 'between',
                          //tickmarkPlacement: 'on',
                          categories: weekdate
                      },

                      legend: {
                          layout: 'vertical',
                          align: 'right',
                          verticalAlign: 'middle'
                      },

                      plotOptions: {
                          series: {
                              label: {
                                  connectorAllowed: false
                              }
                          }
                      },

                      series: pucdata,
                      responsive: {
                          rules: [{
                              condition: {
                                  maxWidth: 500
                              },
                              chartOptions: {
                                  legend: {
                                      layout: 'horizontal',
                                      align: 'center',
                                      verticalAlign: 'bottom'

                                  },
                                  xAxis: {
                                      labels: {
                                          step: 2
                                      }
                                  }
                              }
                          }]
                      },
                      /*chart:{
                       backgroundColor: "rgba(255, 255, 255, 0)",
                       plotBackgroundColor: "#ffffff"
                       }*/
                  });
              },
              error: function(data) {
                  console.log(data);
              }
          });
          $.ajax({
              url: '{!! route('exporterbill.ga_priceana_usd_exporter') !!}',
              method: "POST",
              data:postData,
              beforeSend: function() {
                    $("#loading-chart_priceana_usd_impexp").show();
                    $("#chart_priceana_usd_impexp_chart").hide();
                },

            success: function(data) {
                $("#loading-chart_priceana_usd_impexp").hide();
                $("#chart_priceana_usd_impexp_chart").show();
                localStorage.setItem("exporter_price_data",(localStorage.getItem("exporter_price_data")-1));


                  var pucdata = [],resdates = [], rescountry=[];

                  for(var i in data.data) {
                      resdates.push(new Date(data.data[i].week_start));
                      rescountry.push(data.data[i].labeltitle);
                  }
                  var maxDate=new Date(Math.max.apply(null,resdates)).toISOString();
                  var minDate=new Date(Math.min.apply(null,resdates)).toISOString().slice(0,10);
                  var lastSunday = new Date(lastMondayinmonth( new Date(maxDate).getMonth()+1, new Date(maxDate).getFullYear()));

                  rescountry = Array.from(new Set(rescountry));
                  for(var i in rescountry) {
                      if (!this[rescountry[i]]) {
                          var countrydata = [];
                          var currentSunday = new Date(lastSunday);
                          var c2Sunday = currentSunday;
                          var weekdate = [];
                          while(currentSunday >= new Date(minDate)) {
                              c2Sunday = currentSunday;
                              weekdate.push(new Date(c2Sunday.setDate(c2Sunday.getDate())).toDateString().slice(3,15));
                              var pushed = false;
                              for (var ii in data.data) {
                                  if (new Date(data.data[ii].week_start).getTime() == new Date(currentSunday).getTime() && data.data[ii].labeltitle == rescountry[i]) {
                                      countrydata.push(parseFloat(data.data[ii].labelvalue));
                                      pushed = true;
                                      break;
                                  }
                              }
                              if(pushed == false){
                                  countrydata.push(parseFloat(0));
                              }
                              currentSunday = new Date(currentSunday.setDate(currentSunday.getDate() - 7));
                          }
                          this[rescountry[i]] = { name: rescountry[i], data: countrydata };
                          pucdata.push(this[rescountry[i]]);
                      }
                  }
                  Highcharts.chart('chart_priceana_usd_impexp', {
                      title: {
                          text: 'Total  Value of Top 15 Exporters in USD  by Time [Weekly]'
                      },

                      yAxis: {
                          title: {
                              text: 'Value In USD'
                          }
                      },

                      xAxis: {
                          tickmarkPlacement: 'between',
                          //tickmarkPlacement: 'on',
                          categories: weekdate
                      },

                      legend: {
                          layout: 'vertical',
                          align: 'right',
                          verticalAlign: 'middle'
                      },

                      plotOptions: {
                          series: {
                              label: {
                                  connectorAllowed: false
                              }
                          }
                      },

                      series: pucdata,
                      responsive: {
                          rules: [{
                              condition: {
                                  maxWidth: 500
                              },
                              chartOptions: {
                                  legend: {
                                      layout: 'horizontal',
                                      align: 'center',
                                      verticalAlign: 'bottom'

                                  },
                                  xAxis: {
                                      labels: {
                                          step: 2
                                      }
                                  }
                              }
                          }]
                      },
                      /*chart:{
                       backgroundColor: "rgba(255, 255, 255, 0)",
                       plotBackgroundColor: "#ffffff"
                       }*/
                  });
              },
              error: function(data) {
                  console.log(data);
              }
          });

      }
    function exporter_comparison(form_data){
        // alert("exporter_comparison");

      form_data = JSON.parse(localStorage.getItem("search") || "[]");
      console.log(form_data);
      var postData = {}; var data={};

      if(localStorage.getItem("search") != null) {
        postData = form_data.reduce(function(a, x) { a[x.name] = x.value; return a; }, {});
      } else {
        postData['_token'] = "{{ csrf_token() }}";
      }
      $.ajax({
        url: '{!! route('exporterbill.ga_comparison_usd_exporter') !!}',
        method: "POST",
        data:postData,
        beforeSend: function() {
                    $("#loading-chart_comparisontt_usd_impexp").show();
                    $("#chart_comparisontt_usd_impexp_chart").hide();
                },

          success: function(data) {
            $("#loading-chart_comparisontt_usd_impexp").hide();
            $("#chart_comparisontt_usd_impexp_chart").show();
            localStorage.setItem("exporter_comp_data",(localStorage.getItem("exporter_comp_data")-1));

                var pucdata = [],resdates = [], rescountry=[];

                for(var i in data.data) {
                    resdates.push(new Date(data.data[i].week_start));
                    rescountry.push(data.data[i].labeltitle);
                }
                var maxDate=new Date(Math.max.apply(null,resdates)).toISOString();
                var minDate=new Date(Math.min.apply(null,resdates)).toISOString().slice(0,10);
                var lastSunday = new Date(lastMondayinmonth( new Date(maxDate).getMonth()+1, new Date(maxDate).getFullYear()));

                rescountry = Array.from(new Set(rescountry));
                for(var i in rescountry) {
                    if (!this[rescountry[i]]) {
                        var countrydata = [];
                        var currentSunday = new Date(lastSunday);
                        var c2Sunday = currentSunday;
                        var weekdate = [];
                        while(currentSunday >= new Date(minDate)) {
                            c2Sunday = currentSunday;
                            weekdate.push(new Date(c2Sunday.setDate(c2Sunday.getDate())).toDateString().slice(3,15));
                            var pushed = false;
                            for (var ii in data.data) {
                                if (new Date(data.data[ii].week_start).getTime() == new Date(currentSunday).getTime() && data.data[ii].labeltitle == rescountry[i]) {
                                    countrydata.push(parseFloat(data.data[ii].labelvalue));
                                    pushed = true;
                                    break;
                                }
                            }
                            if(pushed == false){
                                countrydata.push(parseFloat(0));
                            }
                            currentSunday = new Date(currentSunday.setDate(currentSunday.getDate() - 7));
                        }
                        this[rescountry[i]] = { name: rescountry[i], data: countrydata };
                        pucdata.push(this[rescountry[i]]);
                    }
                }
                Highcharts.chart('chart_comparisontt_usd_impexp', {
                    title: {
                        text: 'Total  Value Comparison of Top  Exporters in USD  by Time [Weekly]'
                    },

                    yAxis: {
                        title: {
                            text: 'Value In USD'
                        }
                    },

                    xAxis: {
                        tickmarkPlacement: 'between',
                        //tickmarkPlacement: 'on',
                        categories: weekdate
                    },

                    /*legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle'
                    },*/

                    plotOptions: {
                        series: {
                            label: {
                                connectorAllowed: false
                            }
                        }
                    },

                    series: pucdata,
                    responsive: {
                        rules: [{
                            condition: {
                                maxWidth: 500
                            },
                            chartOptions: {
                                legend: {
                                    layout: 'horizontal',
                                    align: 'center',
                                    verticalAlign: 'bottom'

                                },
                                xAxis: {
                                    labels: {
                                        step: 2
                                    }
                                }
                            }
                        }]
                    },
                    /*chart:{
                     backgroundColor: "rgba(255, 255, 255, 0)",
                     plotBackgroundColor: "#ffffff"
                     }*/
                });
            },
        error: function(data) {
          console.log(data);
        }
      });
      $.ajax({
        url: '{!! route('exporterbill.ga_comparison_usd_country') !!}',
        method: "POST",
        data:postData,
        beforeSend: function() {
                    $("#loading-chart_comparisontt_usd_country").show();
                    $("#chart_comparisontt_usd_country_chart").hide();
                },
          success: function(data) {
            $("#loading-chart_comparisontt_usd_country").hide();
            $("#chart_comparisontt_usd_country_chart").show();
            localStorage.setItem("exporter_comp_data",(localStorage.getItem("exporter_comp_data")-1));

                  var pucdata = [],resdates = [], rescountry=[];

                  for(var i in data.data) {
                      resdates.push(new Date(data.data[i].week_start));
                      rescountry.push(data.data[i].labeltitle);
                  }
                  var maxDate=new Date(Math.max.apply(null,resdates)).toISOString();
                  var minDate=new Date(Math.min.apply(null,resdates)).toISOString().slice(0,10);
                  var lastSunday = new Date(lastMondayinmonth( new Date(maxDate).getMonth()+1, new Date(maxDate).getFullYear()));

                  rescountry = Array.from(new Set(rescountry));
                  for(var i in rescountry) {
                      if (!this[rescountry[i]]) {
                          var countrydata = [];
                          var currentSunday = new Date(lastSunday);
                          var c2Sunday = currentSunday;
                          var weekdate = [];
                          while(currentSunday >= new Date(minDate)) {
                              c2Sunday = currentSunday;
                              weekdate.push(new Date(c2Sunday.setDate(c2Sunday.getDate())).toDateString().slice(3,15));
                              var pushed = false;
                              for (var ii in data.data) {
                                  if (new Date(data.data[ii].week_start).getTime() == new Date(currentSunday).getTime() && data.data[ii].labeltitle == rescountry[i]) {
                                      countrydata.push(parseFloat(data.data[ii].labelvalue));
                                      pushed = true;
                                      break;
                                  }
                              }
                              if(pushed == false){
                                  countrydata.push(parseFloat(0));
                              }
                              currentSunday = new Date(currentSunday.setDate(currentSunday.getDate() - 7));
                          }
                          this[rescountry[i]] = { name: rescountry[i], data: countrydata };
                          pucdata.push(this[rescountry[i]]);
                      }
                  }
                  Highcharts.chart('chart_comparisontt_usd_country', {
                      title: {
                          text: 'Total  Value Comparison of Top  Countries in USD  by Time [Weekly]'
                      },

                      yAxis: {
                          title: {
                              text: 'Value In USD'
                          }
                      },

                      xAxis: {
                          tickmarkPlacement: 'between',
                          //tickmarkPlacement: 'on',
                          categories: weekdate
                      },

                      /*legend: {
                          layout: 'vertical',
                          align: 'right',
                          verticalAlign: 'middle'
                      },*/

                      plotOptions: {
                          series: {
                              label: {
                                  connectorAllowed: false
                              }
                          }
                      },

                      series: pucdata,
                      responsive: {
                          rules: [{
                              condition: {
                                  maxWidth: 500
                              },
                              chartOptions: {
                                  legend: {
                                      layout: 'horizontal',
                                      align: 'center',
                                      verticalAlign: 'bottom'

                                  },
                                  xAxis: {
                                      labels: {
                                          step: 2
                                      }
                                  }
                              }
                          }]
                      },
                      /*chart:{
                       backgroundColor: "rgba(255, 255, 255, 0)",
                       plotBackgroundColor: "#ffffff"
                       }*/
                  });
              },
        error: function(data) {
          console.log(data);
        }
      });
      $.ajax({
        url: '{!! route('exporterbill.ga_comparison_usd_port') !!}',
        method: "POST",
        data:postData,
        beforeSend: function() {
                    $("#loading-chart_comparisontt_usd_ports").show();
                    $("#chart_comparisontt_usd_ports_chart").hide();
                },

          success: function(data) {
            $("#loading-chart_comparisontt_usd_ports").hide();
            $("#chart_comparisontt_usd_ports_chart").show();
            localStorage.setItem("exporter_comp_data",(localStorage.getItem("exporter_comp_data")-1));

                  var pucdata = [],resdates = [], rescountry=[];

                  for(var i in data.data) {
                      resdates.push(new Date(data.data[i].week_start));
                      rescountry.push(data.data[i].labeltitle);
                  }
                  var maxDate=new Date(Math.max.apply(null,resdates)).toISOString();
                  var minDate=new Date(Math.min.apply(null,resdates)).toISOString().slice(0,10);
                  var lastSunday = new Date(lastMondayinmonth( new Date(maxDate).getMonth()+1, new Date(maxDate).getFullYear()));

                  rescountry = Array.from(new Set(rescountry));
                  for(var i in rescountry) {
                      if (!this[rescountry[i]]) {
                          var countrydata = [];
                          var currentSunday = new Date(lastSunday);
                          var c2Sunday = currentSunday;
                          var weekdate = [];
                          while(currentSunday >= new Date(minDate)) {
                              c2Sunday = currentSunday;
                              weekdate.push(new Date(c2Sunday.setDate(c2Sunday.getDate())).toDateString().slice(3,15));
                              var pushed = false;
                              for (var ii in data.data) {
                                  if (new Date(data.data[ii].week_start).getTime() == new Date(currentSunday).getTime() && data.data[ii].labeltitle == rescountry[i]) {
                                      countrydata.push(parseFloat(data.data[ii].labelvalue));
                                      pushed = true;
                                      break;
                                  }
                              }
                              if(pushed == false){
                                  countrydata.push(parseFloat(0));
                              }
                              currentSunday = new Date(currentSunday.setDate(currentSunday.getDate() - 7));
                          }
                          this[rescountry[i]] = { name: rescountry[i], data: countrydata };
                          pucdata.push(this[rescountry[i]]);
                      }
                  }
                  Highcharts.chart('chart_comparisontt_usd_ports', {
                      title: {
                          text: 'Total  Value Comparison of Top  Ports in USD  by Time [Weekly]'
                      },

                      yAxis: {
                          title: {
                              text: 'Value In USD'
                          }
                      },

                      xAxis: {
                          tickmarkPlacement: 'between',
                          //tickmarkPlacement: 'on',
                          categories: weekdate
                      },

                      /*legend: {
                          layout: 'vertical',
                          align: 'right',
                          verticalAlign: 'middle'
                      },*/

                      plotOptions: {
                          series: {
                              label: {
                                  connectorAllowed: false
                              }
                          }
                      },

                      series: pucdata,
                      responsive: {
                          rules: [{
                              condition: {
                                  maxWidth: 500
                              },
                              chartOptions: {
                                  legend: {
                                      layout: 'horizontal',
                                      align: 'center',
                                      verticalAlign: 'bottom'

                                  },
                                  xAxis: {
                                      labels: {
                                          step: 2
                                      }
                                  }
                              }
                          }]
                      },
                      /*chart:{
                       backgroundColor: "rgba(255, 255, 255, 0)",
                       plotBackgroundColor: "#ffffff"
                       }*/
                  });
              },
        error: function(data) {
          console.log(data);
        }
      });
    }
    function exporter_gridsummaries(form_data){

        // alert('exporter_gridsummaries');
        form_data = JSON.parse(localStorage.getItem("search") || "[]");
        console.log(form_data);


      var postData = {}; var data={};
      if(localStorage.getItem("search") != null) {

          postData = form_data.reduce(function(a, x) { a[x.name] = x.value; return a; }, {});
      } else {
          postData['_token'] = "{{ csrf_token() }}";
      }
      $('#gsum-8digit-table').DataTable({
          processing: true,
          serverSide: false,
          processData: false,
          contentType: false,
          enctype: 'multipart/form-data',
          ajax: {
              url:'{!! route('exporterbill.ga_gsum_8digit') !!}',
              type:'POST',
              data: postData,
            error: function (xhr, error, code)
            {
                console.log(xhr);
                console.log(code);
            },
            statusCode: {
                401: function() {
                    window.location.href = 'login'; //or what ever is your login URI
                },
                419: function(){
                    window.location.href = 'login'; //or what ever is your login URI
                }
            },
          },
          columns: [
              { title: 'HS Code'         , data: 'hs_code'        , name: 'hs_code'},
              { title: 'Avg Unit Price'  , data: 'avg_unit_price' , name: 'avg_unit_price' },
              { title: 'Quantity Sum'    , data: 'quantity_sum'   , name: 'quantity_sum'   },
              { title: 'Value Sum (USD)' , data: 'value_sum_usd'  , name: 'value_sum_usd'  },
              { title: 'Record Count'    , data: 'record_count'   , name: 'record_count'   }
          ],
          /*columnDefs: [
           { width: 40, targets: 0 },
           { width: 110, targets: 1 },
           { width: 250, targets: 2 },
           { width: 350, targets: 3 },
           { width: 110, targets: 4 },
           { width: 130, targets: 5 }
           ],*/
          scrollX:        true,
          scrollCollapse: true,
          fixedColumns: true,
          "dom": '<"top"ilf>rt<"bottom"ip><"clear">'
      })
      $('#gsum-2digit-table').DataTable({
          processing: true,
          serverSide: false,
          processData: false,
          contentType: false,
          enctype: 'multipart/form-data',
          ajax: {
              url:'{!! route('exporterbill.ga_gsum_2digit') !!}',
              type:'POST',
              data: postData,
            error: function (xhr, error, code)
            {
                console.log(xhr);
                console.log(code);
            },
            statusCode: {
                401: function() {
                    window.location.href = 'login'; //or what ever is your login URI
                },
                419: function(){
                    window.location.href = 'login'; //or what ever is your login URI
                }
            },
          },
          columns: [
              { title: 'HS Code'         , data: 'hs_code'        , name: 'hs_code'},
              { title: 'Avg Unit Price'  , data: 'avg_unit_price' , name: 'avg_unit_price' },
              { title: 'Quantity Sum'    , data: 'quantity_sum'   , name: 'quantity_sum'   },
              { title: 'Value Sum (USD)' , data: 'value_sum_usd'  , name: 'value_sum_usd'  },
              { title: 'Record Count'    , data: 'record_count'   , name: 'record_count'   }
          ],
          scrollX:        true,
          scrollCollapse: true,
          fixedColumns: true,
          "dom": '<"top"ilf>rt<"bottom"ip><"clear">'
      })
      $('#gsum-4digit-table').DataTable({
        processing: true,
        serverSide: false,
        processData: false,
        contentType: false,
        enctype: 'multipart/form-data',
        ajax: {
            url:'{!! route('exporterbill.ga_gsum_4digit') !!}',
            type:'POST',
            data: postData,
            error: function (xhr, error, code)
            {
                console.log(xhr);
                console.log(code);
            },
            statusCode: {
                401: function() {
                    window.location.href = 'login'; //or what ever is your login URI
                },
                419: function(){
                    window.location.href = 'login'; //or what ever is your login URI
                }
            },
        },
        columns: [
            { title: 'HS Code'         , data: 'hs_code'        , name: 'hs_code'},
            { title: 'Avg Unit Price'  , data: 'avg_unit_price' , name: 'avg_unit_price' },
            { title: 'Quantity Sum'    , data: 'quantity_sum'   , name: 'quantity_sum'   },
            { title: 'Value Sum (USD)' , data: 'value_sum_usd'  , name: 'value_sum_usd'  },
            { title: 'Record Count'    , data: 'record_count'   , name: 'record_count'   }
        ],
        scrollX:        true,
        scrollCollapse: true,
        fixedColumns: true,
        "dom": '<"top"ilf>rt<"bottom"ip><"clear">'
    })
      $('#gsum-port-table').DataTable({
        processing: true,
        serverSide: false,
        processData: false,
        contentType: false,
        enctype: 'multipart/form-data',
        ajax: {
            url:'{!! route('exporterbill.ga_gsum_port') !!}',
            type:'POST',
            data: postData,
            error: function (xhr, error, code)
            {
                console.log(xhr);
                console.log(code);
            },
            statusCode: {
                401: function() {
                    window.location.href = 'login'; //or what ever is your login URI
                },
                419: function(){
                    window.location.href = 'login'; //or what ever is your login URI
                }
            },
        },
        columns: [
            { title: 'Port'            , data: 'label_title'        , name: 'label_title'},
            { title: 'Avg Unit Price'  , data: 'avg_unit_price' , name: 'avg_unit_price' },
            { title: 'Quantity Sum'    , data: 'quantity_sum'   , name: 'quantity_sum'   },
            { title: 'Value Sum (USD)' , data: 'value_sum_usd'  , name: 'value_sum_usd'  },
            { title: 'Record Count'    , data: 'record_count'   , name: 'record_count'   }
        ],
        scrollX:        true,
        scrollCollapse: true,
        fixedColumns: true,
        "dom": '<"top"ilf>rt<"bottom"ip><"clear">'
      })
      $('#gsum-country-table').DataTable({
        processing: true,
        serverSide: false,
        processData: false,
        contentType: false,
        enctype: 'multipart/form-data',
        ajax: {
            url:'{!! route('exporterbill.ga_gsum_country') !!}',
            type:'POST',
            data: postData,
            error: function (xhr, error, code)
            {
                console.log(xhr);
                console.log(code);
            },
            statusCode: {
                401: function() {
                    window.location.href = 'login'; //or what ever is your login URI
                },
                419: function(){
                    window.location.href = 'login'; //or what ever is your login URI
                }
            },
        },
        columns: [
            { title: 'Country'         , data: 'label_title'    , name: 'label_title'},
            { title: 'Avg Unit Price'  , data: 'avg_unit_price' , name: 'avg_unit_price' },
            { title: 'Quantity Sum'    , data: 'quantity_sum'   , name: 'quantity_sum'   },
            { title: 'Value Sum (USD)' , data: 'value_sum_usd'  , name: 'value_sum_usd'  },
            { title: 'Record Count'    , data: 'record_count'   , name: 'record_count'   }
        ],
        scrollX:        true,
        scrollCollapse: true,
        fixedColumns: true,
        "dom": '<"top"ilf>rt<"bottom"ip><"clear">'
      })
      $('#gsum-unit-table').DataTable({
        processing: true,
        serverSide: false,
        processData: false,
        contentType: false,
        enctype: 'multipart/form-data',
        ajax: {
            url:'{!! route('exporterbill.ga_gsum_unit') !!}',
            type:'POST',
            data: postData,
            error: function (xhr, error, code)
            {
                console.log(xhr);
                console.log(code);
            },
            statusCode: {
                401: function() {
                    window.location.href = 'login'; //or what ever is your login URI
                },
                419: function(){
                    window.location.href = 'login'; //or what ever is your login URI
                }
            },
        },
        columns: [
            { title: 'Unit'            , data: 'label_title'    , name: 'label_title'},
            { title: 'Avg Unit Price'  , data: 'avg_unit_price' , name: 'avg_unit_price' },
            { title: 'Quantity Sum'    , data: 'quantity_sum'   , name: 'quantity_sum'   },
            { title: 'Value Sum (USD)' , data: 'value_sum_usd'  , name: 'value_sum_usd'  },
            { title: 'Record Count'    , data: 'record_count'   , name: 'record_count'   }
        ],
        scrollX:        true,
        scrollCollapse: true,
        fixedColumns: true,
        "dom": '<"top"ilf>rt<"bottom"ip><"clear">'
      })

    }

    function exporter_pricecompare(form_data){
        // alert('exporter_pricecompare');
        form_data = JSON.parse(localStorage.getItem("search") || "[]");
        console.log(form_data);

      var postData = {}; var data={};
      if(localStorage.getItem("search") != null) {
          postData = form_data.reduce(function(a, x) { a[x.name] = x.value; return a; }, {});
      } else {
          postData['_token'] = "{{ csrf_token() }}";
      }
      $.ajax({
          url: '{!! route('exporterbill.ga_pc_usd_country_max') !!}',
          method: "POST",
          data:postData,
          beforeSend: function() {
                    $("#loading-chart_pc_unit_usd_country_max").show();
                    $("#chart_pc_unit_usd_country_max_chart").hide();
                },
          success: function(data) {
            $("#loading-chart_pc_unit_usd_country_max").hide();
            $("#chart_pc_unit_usd_country_max_chart").show();
            localStorage.setItem("exporter_priCom_data",(localStorage.getItem("exporter_priCom_data")-1));

              var chdata = []
              for(var i in data.data) {
                  chdata.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
              }
              //console.log(chdata);

              Highcharts.chart('chart_pc_unit_usd_country_max', {
                  chart: {
                      type: 'pie',
                      options3d: {
                          enabled: true,
                          alpha: 45
                      }
                  },
                  title: {
                      text: 'Max Unit Price In USD'
                  },
                  subtitle: {
                      text: 'Country Wise'
                  },
                  plotOptions: {
                      pie: {
                          innerSize: 100,
                          depth: 45
                      }
                  },
                  series: [{
                      name: 'Max Unit Price Value in USD',
                      data: chdata
                  }]
              });
          },
          error: function(data) {
              console.log(data);
          }
      });

      $.ajax({
          url: '{!! route('exporterbill.ga_pc_qua_country_max') !!}',
          method: "POST",
          data:postData,
          beforeSend: function() {
            $("#loading-chart_pc_quantity_country_max").show();
            $("#chart_pc_quantity_country_max_chart").hide();
            },

          success: function(data) {
            $("#loading-chart_pc_quantity_country_max").hide();
            $("#chart_pc_quantity_country_max_chart").show();
            localStorage.setItem("exporter_priCom_data",(localStorage.getItem("exporter_priCom_data")-1));
              var chdata = []
              for(var i in data.data) {
                  chdata.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
              }
              //console.log(chdata);

              Highcharts.chart('chart_pc_quantity_country_max', {
                  chart: {
                      type: 'pie',
                      options3d: {
                          enabled: true,
                          alpha: 45
                      }
                  },
                  title: {
                      text: 'Max Quantity'
                  },
                  subtitle: {
                      text: 'Country Wise'
                  },
                  plotOptions: {
                      pie: {
                          innerSize: 100,
                          depth: 45
                      }
                  },
                  series: [{
                      name: 'Quantity Value',
                      data: chdata
                  }]
              });
          },
          error: function(data) {
              console.log(data);
          }
      });
      $.ajax({
          url: '{!! route('exporterbill.ga_pc_usd_country_min') !!}',
          method: "POST",
          data:postData,
          beforeSend: function() {
            $("#loading-chart_pc_unit_usd_country_min").show();
            $("#chart_pc_unit_usd_country_min_chart").hide();
            },

          success: function(data) {
            $("#loading-chart_pc_unit_usd_country_min").hide();
            $("#chart_pc_unit_usd_country_min_chart").show();
            localStorage.setItem("exporter_priCom_data",(localStorage.getItem("exporter_priCom_data")-1));
              var chdata = []
              for(var i in data.data) {
                  chdata.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
              }
              //console.log(chdata);

              Highcharts.chart('chart_pc_unit_usd_country_min', {
                  chart: {
                      type: 'pie',
                      options3d: {
                          enabled: true,
                          alpha: 45
                      }
                  },
                  title: {
                      text: 'Min Unit Price In USD'
                  },
                  subtitle: {
                      text: 'Country Wise'
                  },
                  plotOptions: {
                      pie: {
                          innerSize: 100,
                          depth: 45
                      }
                  },
                  series: [{
                      name: 'Min Unit Price Value in USD',
                      data: chdata
                  }]
              });
          },
          error: function(data) {
              console.log(data);
          }
      });
      $.ajax({
          url: '{!! route('exporterbill.ga_pc_qua_country_min') !!}',
          method: "POST",
          data:postData,
          beforeSend: function() {
            $("#loading-chart_pc_quantity_country_min").show();
            $("#chart_pc_quantity_country_min_chart").hide();
            },
          success: function(data) {
            $("#loading-chart_pc_quantity_country_min").hide();
            $("#chart_pc_quantity_country_min_chart").show();
            localStorage.setItem("exporter_priCom_data",(localStorage.getItem("exporter_priCom_data")-1));
              var chdata = []
              for(var i in data.data) {
                  chdata.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
              }
              //console.log(chdata);

              Highcharts.chart('chart_pc_quantity_country_min', {
                  chart: {
                      type: 'pie',
                      options3d: {
                          enabled: true,
                          alpha: 45
                      }
                  },
                  title: {
                      text: 'Min Quantity'
                  },
                  subtitle: {
                      text: 'Country Wise'
                  },
                  plotOptions: {
                      pie: {
                          innerSize: 100,
                          depth: 45
                      }
                  },
                  series: [{
                      name: 'Quantity Value',
                      data: chdata
                  }]
              });
          },
          error: function(data) {
              console.log(data);
          }
      });
      $.ajax({
          url: '{!! route('exporterbill.ga_pc_usd_port_max') !!}',
          method: "POST",
          data:postData,
          beforeSend: function() {
            $("#loading-chart_pc_unit_usd_port_max").show();
            $("#chart_pc_unit_usd_port_max_chart").hide();

            },

            success: function(data) {
            $("#loading-chart_pc_unit_usd_port_max").hide();
            $("#chart_pc_unit_usd_port_max_chart").show();
            localStorage.setItem("exporter_priCom_data",(localStorage.getItem("exporter_priCom_data")-1));
              var chdata = []
              for(var i in data.data) {
                  chdata.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
              }
              //console.log(chdata);

              Highcharts.chart('chart_pc_unit_usd_port_max', {
                  chart: {
                      type: 'pie',
                      options3d: {
                          enabled: true,
                          alpha: 45
                      }
                  },
                  title: {
                      text: 'Max Unit Price In USD'
                  },
                  subtitle: {
                      text: 'Port Wise'
                  },
                  plotOptions: {
                      pie: {
                          innerSize: 100,
                          depth: 45
                      }
                  },
                  series: [{
                      name: 'Max Unit Price Value in USD',
                      data: chdata
                  }]
              });
          },
          error: function(data) {
              console.log(data);
          }
      });

      $.ajax({
          url: '{!! route('exporterbill.ga_pc_qua_port_max') !!}',
          method: "POST",
          data:postData,
          beforeSend: function() {
            $("#loading-chart_pc_quantity_port_max").show();
            $("#chart_pc_quantity_port_max_chart").hide();

            },

          success: function(data) {
            $("#loading-chart_pc_quantity_port_max").hide();
            $("#chart_pc_quantity_port_max_chart").show();
            localStorage.setItem("exporter_priCom_data",(localStorage.getItem("exporter_priCom_data")-1));
              var chdata = []
              for(var i in data.data) {
                  chdata.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
              }
              //console.log(chdata);

              Highcharts.chart('chart_pc_quantity_port_max', {
                  chart: {
                      type: 'pie',
                      options3d: {
                          enabled: true,
                          alpha: 45
                      }
                  },
                  title: {
                      text: 'Max Quantity'
                  },
                  subtitle: {
                      text: 'Port Wise'
                  },
                  plotOptions: {
                      pie: {
                          innerSize: 100,
                          depth: 45
                      }
                  },
                  series: [{
                      name: 'Quantity Value',
                      data: chdata
                  }]
              });
          },
          error: function(data) {
              console.log(data);
          }
      });
      $.ajax({
          url: '{!! route('exporterbill.ga_pc_usd_port_min') !!}',
          method: "POST",
          data:postData,
          beforeSend: function() {
            $("#loading-chart_pc_unit_usd_port_min").show();
            $("#chart_pc_unit_usd_port_min_chart").hide();

            },

            success: function(data) {
                $("#loading-chart_pc_unit_usd_port_min").hide();
                $("#chart_pc_unit_usd_port_min_chart").show();
                localStorage.setItem("exporter_priCom_data",(localStorage.getItem("exporter_priCom_data")-1));
              var chdata = []
              for(var i in data.data) {
                  chdata.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
              }
              //console.log(chdata);

              Highcharts.chart('chart_pc_unit_usd_port_min', {
                  chart: {
                      type: 'pie',
                      options3d: {
                          enabled: true,
                          alpha: 45
                      }
                  },
                  title: {
                      text: 'Min Unit Price In USD'
                  },
                  subtitle: {
                      text: 'Port Wise'
                  },
                  plotOptions: {
                      pie: {
                          innerSize: 100,
                          depth: 45
                      }
                  },
                  series: [{
                      name: 'Min Unit Price Value in USD',
                      data: chdata
                  }]
              });
          },
          error: function(data) {
              console.log(data);
          }
      });
      $.ajax({
          url: '{!! route('exporterbill.ga_pc_qua_port_min') !!}',
          method: "POST",
          data:postData,
          beforeSend: function() {
            $("#loading-chart_pc_quantity_port_min").show();
            $("#chart_pc_quantity_port_min_chart").hide();

                },

            success: function(data) {
                $("#loading-chart_pc_quantity_port_min").hide();
                $("#chart_pc_quantity_port_min_chart").show();
                localStorage.setItem("exporter_priCom_data",(localStorage.getItem("exporter_priCom_data")-1));
              var chdata = []
              for(var i in data.data) {
                  chdata.push([data.data[i].labeltitle, parseFloat(data.data[i].labelvalue)]);
              }
              //console.log(chdata);

              Highcharts.chart('chart_pc_quantity_port_min', {
                  chart: {
                      type: 'pie',
                      options3d: {
                          enabled: true,
                          alpha: 45
                      }
                  },
                  title: {
                      text: 'Min Quantity'
                  },
                  subtitle: {
                      text: 'Port Wise'
                  },
                  plotOptions: {
                      pie: {
                          innerSize: 100,
                          depth: 45
                      }
                  },
                  series: [{
                      name: 'Quantity Value',
                      data: chdata
                  }]
              });
          },
          error: function(data) {
              console.log(data);
          }
      });
    }

    $(document).ready(function() {
        
        
       
        sessionStorage.removeItem("TabName");
        localStorage.removeItem("search");
        sessionStorage.setItem("TabName","datatable_tab");
        sessionStorage.setItem("data_type","importer");
        localStorage.setItem("importer_dashboard_data",'3');
        localStorage.setItem("importer_ImAna_data",'3');
        localStorage.setItem("importer_ExpAna_data",'3');
        localStorage.setItem("importer_marSha_data",'4');
        localStorage.setItem("importer_price_data",'3');
        localStorage.setItem("importer_comp_data",'3');
        localStorage.setItem("importer_priCom_data",'8');


        localStorage.setItem("exporter_dashboard_data",'3');
        localStorage.setItem("exporter_ImAna_data",'3');
        localStorage.setItem("exporter_ExAna_data",'3');
        localStorage.setItem("exporter_marSha_data",'4');
        localStorage.setItem("exporter_price_data",'3');
        localStorage.setItem("exporter_comp_data",'3');
        localStorage.setItem("exporter_priCom_data",'8');
        data_type = sessionStorage.getItem("data_type");


        $.ajax({
                url: '{!! route('dataaccess.ajax_get_cal_months') !!}',
                method: "POST",
                data: {
                    '_token': '{{ csrf_token() }}',
                    'sel_rtype': 'Importer',
                    'sel_fyear': '{{$last_year}}',

                },
                beforeSend: function(){
                    // $('.ajax-loader').css("visibility", "visible");
                },
                success: function (response) {
                    if(response.status == 'success'){
                        $("#input-fmonth").find('option').not(':first').remove();
                        $.each(response.da_months, function (index, value) {
                            var option = "<option value='"+value+"'>"+value+"</option>";
                            $("#input-fmonth").append(option);
                        });
                    }
                    else if(response.status == 'failed'){
                        alert(response.message);
                        $("#input-fmonth").find('option').not(':first').remove();
                    }
                },
               
            });
        // console.log(data_type)
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
        });
        $( "#shipments-tab" ).click(function() {
            falseform();
            sessionStorage.setItem("TabName","datatable_tab");
          
            console.log(data_type);
            
            importer_datatable();
           
        });
        $( "#dashboard-tab" ).click(function() {
            trueform();
            sessionStorage.setItem("TabName","dashboard_tab");
            data_type = sessionStorage.getItem("data_type");
            console.log(data_type);


            if(data_type == 'exporter' ){
                is_load = localStorage.getItem("exporter_dashboard_data");
                if(is_load != '0'){
                    exporter_dashboard();
                }
            }else{
               is_load = localStorage.getItem("importer_dashboard_data");
                if(is_load != '0'){
                 importer_dashboard();
                }
            }

        });
        $( "#impanalysis-tab" ).click(function() {
            trueform();
            sessionStorage.setItem("TabName","analysis_tab");
            data_type = sessionStorage.getItem("data_type");

            console.log(data_type);
            
            if(data_type == 'exporter' ){
                is_load = localStorage.getItem("exporter_ImAna_data");
                if(is_load != '0'){
                    exporter_consinee_analysis();
                }
           }else{
                is_load = localStorage.getItem("importer_ImAna_data");
                if(is_load != '0'){
                    importer_analysis();
                }
           }
            
            
        });
        $( "#expanalysis-tab" ).click(function() {
            trueform();
            sessionStorage.setItem("TabName","sup_analysis_tab");
            data_type = sessionStorage.getItem("data_type");
            console.log(data_type);

            if(data_type == 'exporter' ){
               is_load = localStorage.getItem("exporter_ExAna_data");
                if(is_load != '0'){
                   exporter_analysis();
                }
            }else{
                is_load = localStorage.getItem("importer_ExpAna_data");
                if(is_load != '0'){
                    importer_sup_analysis();
                }
                
            }

        });
        $( "#marketshare-tab" ).click(function() {
            trueform();
            sessionStorage.setItem("TabName","marketshare_tab");
            data_type = sessionStorage.getItem("data_type");
            console.log(data_type);
            if(data_type == 'exporter'){
                is_load = localStorage.getItem("exporter_marSha_data");
                if(is_load != '0'){
                   exporter_marketshare();
                }
            }else{
                is_load = localStorage.getItem("importer_marSha_data");
                if(is_load != '0'){
                    importer_marketshare();
                }
            }

        });
        $( "#priceana-tab" ).click(function() {
            trueform();
            sessionStorage.setItem("TabName","priceana_tab");

            data_type = sessionStorage.getItem("data_type");
            console.log(data_type);
            if(data_type == 'exporter'){
                is_load = localStorage.getItem("exporter_price_data");
                if(is_load != '0'){
                    exporter_priceana();
                }
             
            }else{
                is_load = localStorage.getItem("importer_price_data");
                if(is_load != '0'){
                    importer_priceana();
                }
               
            }
            
        });
        $( "#comparisontt-tab" ).click(function() {
            trueform();
            sessionStorage.setItem("TabName","comparison_tab");

            data_type = sessionStorage.getItem("data_type");
            console.log(data_type);
            if(data_type == 'exporter'){
                is_load = localStorage.getItem("exporter_comp_data");
                if(is_load != '0'){
                    exporter_comparison();
                }

               
            }else{
                is_load = localStorage.getItem("importer_comp_data");
                if(is_load != '0'){
                    importer_comparison();
                }
              
            }
            
            // importer_comparison();
        });
        $( "#pricecompare-tab" ).click(function() {
            trueform();
            sessionStorage.setItem("TabName","pricecompare_tab");
            data_type = sessionStorage.getItem("data_type");
            console.log(data_type);
            if(data_type == 'exporter'){
                is_load = localStorage.getItem("exporter_priCom_data");
                if(is_load != '0'){
                    exporter_pricecompare();
                }
                
            }else{
                is_load = localStorage.getItem("importer_priCom_data");
                if(is_load != '0'){
                    importer_pricecompare();
                }

               
            }
        });
        $( "#gridsummaries-tab" ).click(function() {
            falseform();
            if( $('#gsum-8digit-table_length').length == 0 ){
                sessionStorage.setItem("TabName","gridsummaries_tab");
                data_type = sessionStorage.getItem("data_type");
                console.log(data_type);
                if(data_type == 'exporter'){
                    exporter_gridsummaries();
                }else{
                    importer_gridsummaries();
                }
            }
            

        });
    // Javascript method's body can be found in assets/js/demos.js
        if('{{ count($da_years) }}'>0) {
            if('{{ Auth::check() }}'){
                console.log('login ok');
            } else {
                window.location.href = 'login';
            }
            // $('.ajax-loader').css("visibility", "visible");
            // sidebarupdate ();
            importer_datatable();

            //importer_dashboard();
            //importer_analysis();
            //importer_sup_analysis();
            //importer_marketshare();
            //importer_priceana();
            //importer_comparison();
            //importer_gridsummaries();
            // importer_pricecompare();
            // $('.ajax-loader').css("visibility", "hidden");
        } else {
            alert('No Import data Year Found in rights ');
        }

        $('#input-rtype').change(function(){

           

            if('{{ Auth::check() }}'){
                console.log('login ok');
            } else {
                window.location.href = 'login';
            }
            // $('.ajax-loader').css("visibility", "visible");
            if($(this).val() == 'Importer'){

                sessionStorage.setItem("data_type","importer");
                console.log(sessionStorage.getItem("data_type"));

                
                $.ajax({
                    url: '{!! route('dataaccess.ajax_get_cal_years') !!}',
                    method: "POST",
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'sel_rtype': $(this).val(),
                    },
                    beforeSend: function(){
                        //$('.ajax-loader').css("visibility", "visible");
                    },
                    error: function (xhr, error, code)
                    {
                        console.log(xhr);
                        console.log(code);
                    },
                    statusCode: {
                        401: function() {
                            window.location.href = 'login'; //or what ever is your login URI
                        },
                        419: function(){
                            window.location.href = 'login'; //or what ever is your login URI
                        }
                    },
                    success: function (response) {
                        if(response.status == 'success'){
                            $("#input-fyear").find('option').not(':first').remove();
                            $("#input-fmonth").find('option').not(':first').remove();
                            $.each(response.da_years, function (index, value) {
                                var option = "<option value='"+value+"'>"+value+"</option>";
                                $("#input-fyear").append(option);
                            });
                            // sidebarupdate ();
                            // importer_datatable();
                            // importer_dashboard();
                            // importer_analysis ();
                            // importer_sup_analysis();
                            // importer_marketshare();
                            // importer_priceana ();
                            // importer_comparison();
                            // importer_gridsummaries();
                            // importer_pricecompare();

                            $('#nav_exp_hscode').hide();
                            $('#nav_imp_hscode').show();

                            $('#nav_exp_country').hide();
                            $('#nav_imp_country').show();

                            $('#nav_exp_port').hide();
                            $('#nav_imp_port').show();

                            $('#nav_exp_units').hide();
                            $('#nav_imp_units').show();
                        }
                        else if(response.status == 'failed'){
                            alert(response.message);
                            $("#input-fyear").find('option').not(':first').remove();
                            $("#input-fmonth").find('option').not(':first').remove();

                            $('#nav_exp_hscode').hide();
                            $('#nav_imp_hscode').hide();

                            $('#nav_exp_country').hide();
                            $('#nav_imp_country').hide();

                            $('#nav_exp_port').hide();
                            $('#nav_imp_port').hide();

                            $('#nav_exp_units').hide();
                            $('#nav_imp_units').hide();
                        }
                    },
                    /*complete: function(){
                        $('.ajax-loader').css("visibility", "hidden");
                    },*/


                });


            }
            else {
                sessionStorage.setItem("data_type","exporter");
                console.log(sessionStorage.getItem("data_type"));
                // $('#dvDataTable').html('');
                // $('#dvDataTable').html ('<table class="table" id="importer-table"><thead class=" text-primary"></thead></table>');

                // $('#dvDataTableGSum8Digit').html('');
                // $('#dvDataTableGSum8Digit').html ('<table id="gsum-8digit-table"  class="table table-striped table-bordered" cellspacing="0" width="100%"><thead class=" text-primary"></thead></table>');

                // $('#dvDataTableGSum2Digit').html('');
                // $('#dvDataTableGSum2Digit').html ('<table id="gsum-2digit-table"  class="table table-striped table-bordered" cellspacing="0" width="100%"><thead class=" text-primary"></thead></table>');

                // $('#dvDataTableGSum4Digit').html('');
                // $('#dvDataTableGSum4Digit').html ('<table id="gsum-4digit-table"  class="table table-striped table-bordered" cellspacing="0" width="100%"><thead class=" text-primary"></thead></table>');

                // $('#dvDataTableGSumPort').html('');
                // $('#dvDataTableGSumPort').html ('<table id="gsum-port-table"  class="table table-striped table-bordered" cellspacing="0" width="100%"><thead class=" text-primary"></thead></table>');

                // $('#dvDataTableGSumCountry').html('');
                // $('#dvDataTableGSumCountry').html ('<table id="gsum-country-table"  class="table table-striped table-bordered" cellspacing="0" width="100%"><thead class=" text-primary"></thead></table>');

                // $('#dvDataTableGSumUnit').html('');
                // $('#dvDataTableGSumUnit').html ('<table id="gsum-unit-table"  class="table table-striped table-bordered" cellspacing="0" width="100%"><thead class=" text-primary"></thead></table>');

                $.ajax({
                    url: '{!! route('dataaccess.ajax_get_cal_years') !!}',
                    method: "POST",
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'sel_rtype': $(this).val(),
                    },
                    beforeSend: function(){
                        //$('.ajax-loader').css("visibility", "visible");
                    },
                    success: function (response) {
                       
                        if(response.status == 'success'){
                            $("#input-fyear").find('option').not(':first').remove();
                            $("#input-fmonth").find('option').not(':first').remove();
                            $.each(response.da_years, function (index, value) {
                                var option = "<option value='"+value+"'>"+value+"</option>";
                                $("#input-fyear").append(option);
                            });
                            $('#nav_imp_hscode').hide();
                            $('#nav_exp_hscode').show();

                            $('#nav_imp_country').hide();
                            $('#nav_exp_country').show();

                            $('#nav_imp_port').hide();
                            $('#nav_exp_port').show();

                            $('#nav_imp_units').hide();
                            $('#nav_exp_units').show();

                        }
                        else if(response.status == 'failed'){
                            alert(response.message);
                            $("#input-fyear").find('option').not(':first').remove();
                            $("#input-fmonth").find('option').not(':first').remove();

                            $('#nav_imp_hscode').hide();
                            $('#nav_exp_hscode').hide();

                            $('#nav_imp_country').hide();
                            $('#nav_exp_country').hide();

                            $('#nav_imp_port').hide();
                            $('#nav_exp_port').hide();

                            $('#nav_imp_units').hide();
                            $('#nav_exp_units').hide();
                        }
                    },
                    /*complete: function(){
                        $('.ajax-loader').css("visibility", "hidden");
                    }*/
                });
            }
            // $('.ajax-loader').css("visibility", "hidden");
        });


        $('#input-fyear').change(function(){
            
            if('{{ Auth::check() }}'){
                console.log('login ok');
            } else {
                window.location.href = 'login';
            }
            // $('.ajax-loader').css("visibility", "visible");
            $.ajax({
                url: '{!! route('dataaccess.ajax_get_cal_months') !!}',
                method: "POST",
                data: {
                    '_token': '{{ csrf_token() }}',
                    'sel_rtype': $('#input-rtype').val(),
                    'sel_fyear': $(this).val(),

                },
                beforeSend: function(){
                    // $('.ajax-loader').css("visibility", "visible");
                },
                success: function (response) {
                    if(response.status == 'success'){
                        $("#input-fmonth").find('option').not(':first').remove();
                        $.each(response.da_months, function (index, value) {
                            var option = "<option value='"+value+"'>"+value+"</option>";
                            $("#input-fmonth").append(option);
                        });
                    }
                    else if(response.status == 'failed'){
                        alert(response.message);
                        $("#input-fmonth").find('option').not(':first').remove();
                    }
                },
               
            });
        });
        demo.initDashboardPageCharts();

        $("#frm_filter").submit(function(event){


            if('{{ Auth::check() }}'){
                console.log('login ok');
            } else {
                window.location.href = 'login';
            }
            // $('.ajax-loader').css("visibility", "visible");
            event.preventDefault(); //prevent default action
            var post_url = $(this).attr("action"); //get form action url
            var request_method = $(this).attr("method"); //get form GET/POST method
            var form_data = $(this).serializeArray(); //Encode form elements for submission


            localStorage.setItem("search", JSON.stringify(form_data));
           


            $('#dvDataTable').html('');
            $('#dvDataTable').html ('<table class="table" id="importer-table"><thead class=" text-primary"></thead></table>');

            $('#dvDataTableGSum8Digit').html('');
            $('#dvDataTableGSum8Digit').html ('<table id="gsum-8digit-table"  class="table table-striped table-bordered" cellspacing="0" width="100%"><thead class=" text-primary"></thead></table>');

            $('#dvDataTableGSum2Digit').html('');
            $('#dvDataTableGSum2Digit').html ('<table id="gsum-2digit-table"  class="table table-striped table-bordered" cellspacing="0" width="100%"><thead class=" text-primary"></thead></table>');

            $('#dvDataTableGSum4Digit').html('');
            $('#dvDataTableGSum4Digit').html ('<table id="gsum-4digit-table"  class="table table-striped table-bordered" cellspacing="0" width="100%"><thead class=" text-primary"></thead></table>');

            $('#dvDataTableGSumPort').html('');
            $('#dvDataTableGSumPort').html ('<table id="gsum-port-table"  class="table table-striped table-bordered" cellspacing="0" width="100%"><thead class=" text-primary"></thead></table>');

            $('#dvDataTableGSumCountry').html('');
            $('#dvDataTableGSumCountry').html ('<table id="gsum-country-table"  class="table table-striped table-bordered" cellspacing="0" width="100%"><thead class=" text-primary"></thead></table>');

            $('#dvDataTableGSumUnit').html('');
            $('#dvDataTableGSumUnit').html ('<table id="gsum-unit-table"  class="table table-striped table-bordered" cellspacing="0" width="100%"><thead class=" text-primary"></thead></table>');

            if ('Importer' == $('#input-rtype').val()) {

                Tab_Name = sessionStorage.getItem("TabName");
                sidebarupdate (form_data);
                
                if(Tab_Name == 'datatable_tab'){
                 importer_datatable(form_data);
                }
                if(Tab_Name == 'dashboard_tab'){
                	 importer_dashboard(form_data);
                }

                if(Tab_Name == 'analysis_tab'){
                    importer_analysis(form_data);
                }

                if(Tab_Name == 'sup_analysis_tab'){
                    importer_sup_analysis(form_data);
                }
                if(Tab_Name == 'marketshare_tab'){
                    importer_marketshare(form_data);
                }
                if(Tab_Name == 'priceana_tab'){
                    importer_priceana(form_data);
                }
                if(Tab_Name == 'comparison_tab'){
                    importer_comparison(form_data);
                }
                if(Tab_Name == 'gridsummaries_tab'){
                    importer_gridsummaries(form_data);
                }
                if(Tab_Name == 'pricecompare_tab'){
                    importer_pricecompare(form_data);
                }
               
            }
            if ('Exporter' == $('#input-rtype').val()) {
                  sidebarupdate (form_data);
                  Tab_Name = sessionStorage.getItem("TabName");
                if(Tab_Name == 'datatable_tab'){
                    
                    exporter_datatable(form_data);
                }
                if(Tab_Name == 'dashboard_tab'){
                    exporter_dashboard(form_data);
                }

                if(Tab_Name == 'analysis_tab'){
                   
                    exporter_consinee_analysis(form_data);
                }

                if(Tab_Name == 'sup_analysis_tab'){
                    
                    exporter_analysis(form_data);
                }
                if(Tab_Name == 'marketshare_tab'){
                    exporter_marketshare(form_data);
                }
                if(Tab_Name == 'priceana_tab'){
                    exporter_priceana(form_data);
                }
                if(Tab_Name == 'comparison_tab'){
                    exporter_comparison(form_data);
                }
                if(Tab_Name == 'gridsummaries_tab'){
                    exporter_gridsummaries(form_data);
                }
                if(Tab_Name == 'pricecompare_tab'){
                    exporter_pricecompare(form_data);
                }


               
            }
            // $('.ajax-loader').css("visibility", "hidden");
            return false;
        });
    });

   
  </script>


@endpush
