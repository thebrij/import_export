<?php

namespace App\Http\Controllers;


use App\Models\DataAccess;
use App\User;
use App\Models\Role;
use App\Models\ImporterBill;
use App\Models\ExporterBill;
use Carbon\Traits\Date;
use Illuminate\Http\Request;
use Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;
use Symfony\Component\VarDumper\Cloner\Data;

class DataAccessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $userlist = User::select('id', 'name', 'email', 'role_id')->get();
        /*$hs_code_imp = ImporterBill::select('hs_code')->distinct()->get();
        $hs_code_exp = ExporterBill::select('hs_code')->distinct()->get();
        $hs_code = [];
        foreach ($hs_code_imp as $row){
            $hs_code[] = $row->hs_code;
        }
        foreach ($hs_code_exp as $row){
            $hs_code[] = $row->hs_code;
        }
        sort($hs_code);
        //$hs_code=$hs_code_imp->merge($hs_code_exp);

        dd($hs_code);*/

        if(Auth::User()->role_id == '1') {
            $roles=Role::all();
        } else {
            $roles = Role::where('id', '!=', '1')->get();
        }
        $dataaccess = [];
        return view('dataaccess.create', compact('roles', 'dataaccess', 'userlist'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        //echo 'Hello From Store';
        //print_r($_REQUEST);
        if($request->has('action')) {
            if($request->input('action') == 'ajax_add_calendar') {
                if ($request->has('seluserid')) {
                    if($request->has('seldatatype')) {

                        if ($request->has('selyears')) {
                            $right_name = ($request->input('seldatatype') == 'importer')?'selimpyear':'selexpyear';
                            $deletedRows = DataAccess::where('user_id', $request->input('seluserid'))->where('right_name', $right_name)->delete();
                            foreach ($request->input('selyears') as $row) {
                                DataAccess::create([
                                    'user_id' => $request->input('seluserid'),
                                    'right_name' => $right_name,
                                    'right_option' => $row,
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);
                            }
                        }
                        if ($request->has('selmonths')) {
                            $right_name = ($request->input('seldatatype') == 'importer')?'selimpmonth':'selexpmonth';
                            $deletedRows = DataAccess::where('user_id', $request->input('seluserid'))->where('right_name', $right_name)->delete();
                            $fe_count = 0;
                            foreach ($request->input('selmonths') as $row) {
                                DataAccess::create([
                                    'user_id' => $request->input('seluserid'),
                                    'right_name' => $right_name,
                                    'right_option' => $row,
                                    'description' => $request->input('selmonthnames')[$fe_count],
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);
                                $fe_count = $fe_count + 1;
                            }
                        }
                        $response = array(
                            'status' => 'success',
                            'msg' => 'Calendar Rights Saved!',
                        );
                        return Response::json($response);
                    } else {
                        $response = array(
                            'status' => 'failed',
                            'msg' => 'Please Select Data Type First!',
                        );
                        return Response::json($response);
                    }
                } else {
                    $response = array(
                        'status' => 'failed',
                        'msg' => 'Please Select User First!',
                    );
                    return Response::json($response);
                }
            }
            if($request->input('action') == 'ajax_add_hs_code') {
                if ($request->has('seluserid')) {
                    if($request->has('seldatatype')) {
                        if ($request->has('selhscode')) {
                            if($request->input('seldatatype') == 'importer'){
                                $right_name = 'selimphscode';
                                $exithscode = DataAccess::select('right_option')->where('user_id',$request->input('seluserid'))->where('right_name', $right_name)->where('right_option',$request->input('selhscode'))->get();
                                if(count($exithscode)){
                                    $response = array(
                                        'status' => 'failed',
                                        'msg' => 'Hs Code Already Assigned!',
                                    );
                                    return Response::json($response);
                                }
                                $exithscode = ImporterBill::select('hs_code')->where('hs_code', 'LIKE', $request->input('selhscode').'%')->get();
                                if(!count($exithscode)){
                                    $response = array(
                                        'status' => 'failed',
                                        'msg' => 'Please Enter Valid Hs Code!',
                                    );
                                    return Response::json($response);
                                }
                                $add_da = DataAccess::create([
                                    'user_id' => $request->input('seluserid'),
                                    'right_name' => $right_name,
                                    'right_option' => $request->input('selhscode'),
                                    'description' => '',
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);
                                $response = array(
                                    'status' => 'success',
                                    'msg' => 'Hs Code Added Successfully! ',
                                );
                            }
                            else if($request->input('seldatatype') == 'exporter'){
                                $right_name = 'selexphscode';
                                $exithscode = DataAccess::select('right_option')->where('user_id',$request->input('seluserid'))->where('right_name', $right_name)->where('right_option',$request->input('selhscode'))->get();
                                if(count($exithscode)){
                                    $response = array(
                                        'status' => 'failed',
                                        'msg' => 'Hs Code Already Assigned!',
                                    );
                                    return Response::json($response);
                                }
                                $exithscode = ExporterBill::select('hs_code')->where('hs_code',$request->input('selhscode'))->get();
                                if(!count($exithscode)){
                                    $response = array(
                                        'status' => 'failed',
                                        'msg' => 'Please Enter Valid Hs Code!',
                                    );
                                    return Response::json($response);
                                }
                                $add_da = DataAccess::create([
                                    'user_id' => $request->input('seluserid'),
                                    'right_name' => $right_name,
                                    'right_option' => $request->input('selhscode'),
                                    'description' => '',
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);
                                $response = array(
                                    'status' => 'success',
                                    'msg' => 'Hs Code Added Successfully! ',
                                );
                            }

                            return Response::json($response);
                        }
                        else {
                            $response = array(
                                'status' => 'failed',
                                'msg' => 'Please Enter Hs Code to Add!',
                            );
                            return Response::json($response);
                        }
                    } else {
                        $response = array(
                            'status' => 'failed',
                            'msg' => 'Please Select Data Type First!',
                        );
                        return Response::json($response);
                    }
                } else {
                    $response = array(
                        'status' => 'failed',
                        'msg' => 'Please Select User First!',
                    );
                    return Response::json($response);
                }
            }
            if($request->input('action') == 'ajax_del_hs_code') {
                if ($request->has('seluserid')) {
                    if($request->has('seldatatype')) {
                        if ($request->has('delhscode')) {
                            $right_name = ($request->input('seldatatype') == 'importer')?'selimphscode':'selexphscode';
                            $exithscode = DataAccess::select('right_option')->where('user_id',$request->input('seluserid'))->where('right_name',$right_name)->where('right_option',$request->input('delhscode'));
                            //print_r(count($exithscode));
                            if($exithscode) {
                                $exithscode->delete();
                                $response = array(
                                    'status' => 'success',
                                    'msg' => 'Hs Code Deleted Successfully! ',
                                );
                                return Response::json($response);
                            }
                            else {
                                $response = array(
                                    'status' => 'failed',
                                    'msg' => 'Hs Code Not Found!',
                                );
                                return Response::json($response);
                            }
                        }
                        else {
                            $response = array(
                                'status' => 'failed',
                                'msg' => 'Hs Code is missing, Could not Delete!',
                            );
                            return Response::json($response);
                        }
                    } else {
                        $response = array(
                            'status' => 'failed',
                            'msg' => 'Please Select Data Type First!',
                        );
                        return Response::json($response);
                    }
                } else {
                    $response = array(
                        'status' => 'failed',
                        'msg' => 'Please Select User First!',
                    );
                    return Response::json($response);
                }
            }
            if($request->input('action') == 'ajax_add_chapter') {
                if ($request->has('seluserid')) {
                    if ($request->has('seldatatype')) {
                        if ($request->has('selchapter')) {
                            if ($request->input('seldatatype') == 'importer') {
                                $right_name = 'selimpchapter';
                                $exitchapter = DataAccess::select('right_option')->where('user_id',$request->input('seluserid'))->where('right_name',$right_name)->where('right_option',$request->input('selchapter'))->get();
                                //print_r(count($exitchapter));
                                if(count($exitchapter)){
                                    $response = array(
                                        'status' => 'failed',
                                        'msg' => 'Chapter Already Assigned!',
                                    );
                                    return Response::json($response);
                                }
                                $exitchapter = ImporterBill::select('chapter')->where('chapter', 'LIKE', $request->input('selchapter').'%')->get();
                                if(!count($exitchapter)){
                                    $response = array(
                                        'status' => 'failed',
                                        'msg' => 'Please Enter Valid Chapter!',
                                    );
                                    return Response::json($response);
                                }
                                $add_da = DataAccess::create([
                                    'user_id' => $request->input('seluserid'),
                                    'right_name' => $right_name,
                                    'right_option' => $request->input('selchapter'),
                                    'description' => '',
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);
                                $response = array(
                                    'status' => 'success',
                                    'msg' => 'Chapter Added Successfully! ',
                                );

                            }
                            else if ($request->input('seldatatype') == 'exporter') {
                                $right_name = 'selexpchapter';
                                $exitchapter = DataAccess::select('right_option')->where('user_id',$request->input('seluserid'))->where('right_name',$right_name)->where('right_option',$request->input('selchapter'))->get();
                                //print_r(count($exitchapter));
                                if(count($exitchapter)){
                                    $response = array(
                                        'status' => 'failed',
                                        'msg' => 'Chapter Already Assigned!',
                                    );
                                    return Response::json($response);
                                }
                                $exitchapter = ExporterBill::select('chapter')->where('chapter',$request->input('selchapter'))->get();
                                if(!count($exitchapter)){
                                    $response = array(
                                        'status' => 'failed',
                                        'msg' => 'Please Enter Valid Chapter!',
                                    );
                                    return Response::json($response);
                                }
                                $add_da = DataAccess::create([
                                    'user_id' => $request->input('seluserid'),
                                    'right_name' => $right_name,
                                    'right_option' => $request->input('selchapter'),
                                    'description' => '',
                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                ]);
                                $response = array(
                                    'status' => 'success',
                                    'msg' => 'Chapter Added Successfully! ',
                                );

                            }

                            return Response::json($response);

                        } else {
                            $response = array(
                                'status' => 'failed',
                                'msg' => 'Please Enter Chapter to Add!',
                            );
                            return Response::json($response);
                        }
                    } else {
                        $response = array(
                            'status' => 'failed',
                            'msg' => 'Please Select Data Type First!',
                        );
                        return Response::json($response);
                    }
                } else {
                    $response = array(
                        'status' => 'failed',
                        'msg' => 'Please Select User First!',
                    );
                    return Response::json($response);
                }
            }
            if($request->input('action') == 'ajax_del_chapter') {
                if ($request->has('seluserid')) {
                    if ($request->has('seldatatype')) {
                        if($request->has('delchapter')){
                            if($request->input('seldatatype') == 'importer'){
                                $right_name = 'selimpchapter';
                            }
                            else if($request->input('seldatatype') == 'exporter'){
                                $right_name = 'selexpchapter';
                            }
                            $exitchapter = DataAccess::select('right_option')->where('user_id',$request->input('seluserid'))->where('right_name',$right_name)->where('right_option',$request->input('delchapter'));
                            //print_r(count($exitchapter));
                            if($exitchapter) {
                                $exitchapter->delete();
                                $response = array(
                                    'status' => 'success',
                                    'msg' => 'Chapter Deleted Successfully! ',
                                );
                                return Response::json($response);
                            }
                            else {
                                $response = array(
                                    'status' => 'failed',
                                    'msg' => 'Chapter Not Found!',
                                );
                                return Response::json($response);
                            }
                        }
                        else {
                            $response = array(
                                'status' => 'failed',
                                'msg' => 'Chapter is missing, Could not Delete!',
                            );
                            return Response::json($response);
                        }
                    }
                    else {
                        $response = array(
                            'status' => 'failed',
                            'msg' => 'Data Type is missing, Could not Delete!',
                        );
                        return Response::json($response);
                    }
                }
                else {
                    $response = array(
                        'status' => 'failed',
                        'msg' => 'Please Select User First!',
                    );
                    return Response::json($response);
                }
            }
            if($request->input('action') == 'ajax_update_points') {
                if ($request->has('seluserid')) {
                    if ($request->has('selpoints')) {
                        $exitpoints = DataAccess::where('user_id',$request->input('seluserid'))->where('right_name','selpoints')->first();
                        //print_r($exitpoints);
                        if($exitpoints === null) {
                            $add_da = DataAccess::create([
                                'user_id' => $request->input('seluserid'),
                                'right_name' => 'selpoints',
                                'right_option' => $request->input('selpoints'),
                                'description' => '',
                                'created_by' => Auth::user()->id,
                                'updated_by' => Auth::user()->id,
                            ]);
                            $response = array(
                                'status' => 'success',
                                'msg' => 'Points Added Successfully! ',
                            );
                            return Response::json($response);
                        }
                        else {
                            $exitpoints->update(['right_option' => $request->input('selpoints')]);
                            $response = array(
                                'status' => 'success',
                                'msg' => 'Points Updated Successfully!',
                            );
                            return Response::json($response);
                        }


                    }
                    else {
                        $response = array(
                            'status' => 'failed',
                            'msg' => 'Please Enter Points to Add/Update!',
                        );
                        return Response::json($response);
                    }
                } else {
                    $response = array(
                        'status' => 'failed',
                        'msg' => 'Please Select User First!',
                    );
                    return Response::json($response);
                }
            }
            if($request->input('action') == 'ajax_update_expiry_date') {
                if ($request->has('seluserid')) {
                    if ($request->has('selexpirydate')) {
                        $exitusers = User::where('id',$request->input('seluserid'))->first();
                        //print_r($exitpoints);
                        $selDate = $request->input('selexpirydate');
                        //$newSelDate = Carbon::parse($request->input('selexpirydate'))->format('Y-m-d');
                        $selDate = \DateTime::createFromFormat('d/m/Y', $selDate);
                        $newSelDate = Carbon::parse(date_format($selDate,'Y-m-d'));
                        //$newSelDate = \DateTime::createFromFormat('d/m/Y', $selDate);

                        //dd($newSelDate);
                        //dd(date('d/m/Y', strtotime( $selDate ) ));
                        if($exitusers === null) {
                            $response = array(
                                'status' => 'failed',
                                'msg' => 'Error in Selected User! ',
                            );
                            return Response::json($response);
                        }
                        else {

                            $exitusers->update(['expiry_date' => $newSelDate]);
                            $response = array(
                                'status' => 'success',
                                'msg' => 'Login Expiry Date Updated Successfully!',
                            );
                            return Response::json($response);
                        }


                    }
                    else {
                        $response = array(
                            'status' => 'failed',
                            'msg' => 'Please Select a Date to Add/Update!',
                        );
                        return Response::json($response);
                    }
                }
                else {
                    $response = array(
                        'status' => 'failed',
                        'msg' => 'Please Select User First!',
                    );
                    return Response::json($response);
                }
            }

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function ajax_get_cal_years (Request $request){
        if($request->has('sel_rtype')){
            if(null !== $request->input('sel_rtype') and $request->input('sel_rtype') != ''){
                $da_years = [];
                if($request->input('sel_rtype') == 'Importer'){
                    if(Auth::user()->hasRole('Administrator') or Auth::user()->hasRole('Manager') or Auth::user()->hasRole('Supervisor')) {
                        $dbimp_cals = ImporterBill::distinct()->get([DB::raw('YEAR(bill_of_entry_date) as imp_year')]);
                        if ($dbimp_cals->count()) {
                            foreach ($dbimp_cals as $dbimp_cal) {
                                $da_years[] = $dbimp_cal->imp_year;
                            }
                            $da_years = array_unique($da_years);
                        }
                    }
                    else if(Auth::user()->hasRole('User')) {
                        $imp_cals = DataAccess::select('right_option')->where('user_id', Auth::user()->id)->where('right_name', 'selimpcal')->get();
                        if ($imp_cals->count()) {
                            foreach ($imp_cals as $imp_cal) {
                                $da_years[] = date('Y', strtotime($imp_cal->right_option));
                            }
                            $da_years = array_unique($da_years);
                        }
                    }
                }
                else if ($request->input('sel_rtype') == 'Exporter') {
                    if(Auth::user()->hasRole('Administrator') or Auth::user()->hasRole('Manager') or Auth::user()->hasRole('Supervisor')) {
                        $dbexp_cals = ExporterBill::distinct()->get([DB::raw('YEAR(shipping_bill_date) as exp_year')]);
                        if ($dbexp_cals->count()) {
                            $da_years = [];
                            foreach ($dbexp_cals as $dbexp_cal) {
                                $da_years[] = $dbexp_cal->exp_year;
                            }
                            $da_years = array_unique($da_years);
                        }
                    }
                    else if(Auth::user()->hasRole('User')) {
                        $exp_cals = DataAccess::select('right_option')->where('user_id', Auth::user()->id)->where('right_name', 'selexpcal')->get();
                        if ($exp_cals->count()) {
                            foreach ($exp_cals as $exp_cal) {
                                $da_years[] = date('Y', strtotime($exp_cal->right_option));
                            }
                            $da_years = array_unique($da_years);
                        }
                    }
                }
                if(count($da_years)) {
                    //print_r($da_months);
                    return Response::json([
                        'status' => 'success',
                        'message' => 'Years got Successfully',
                        'da_years' => $da_years
                    ]);
                }
                else {
                    return Response::json([
                        'status' => 'failed',
                        'message' => 'Year(s) not found in rights'
                    ]);
                }


            }
            else {
                return Response::json([
                    'status' => 'failed',
                    'message' => 'Record Type Missing in the Request'
                ]);
            }
        }
        else {
            return Response::json([
                'status' => 'failed',
                'message' => 'Record Type Missing in the Request'
            ]);
        }
    }
    public function ajax_get_cal_months (Request $request){
        if($request->has('sel_rtype')){
            if(null !== $request->input('sel_rtype') and $request->input('sel_rtype') != ''){
                if($request->has('sel_fyear')){
                    if(null !== $request->input('sel_fyear') and $request->input('sel_fyear') != ''){
                        $da_months = [];
                        if($request->input('sel_rtype') == 'Importer'){
                            if(Auth::user()->hasRole('Administrator') or Auth::user()->hasRole('Manager') or Auth::user()->hasRole('Supervisor')) {
                                $dbimp_cals = ImporterBill::where(DB::raw('YEAR(bill_of_entry_date)'),$request->input('sel_fyear'))->distinct()->orderBy(DB::raw('MONTH(bill_of_entry_date)'),'ASC')->get([DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\') as imp_month')]);
                                if ($dbimp_cals->count()) {
                                    foreach ($dbimp_cals as $dbimp_cal) {
                                        $da_months[] = $dbimp_cal->imp_month;
                                    }
                                }
                            }
                            else if(Auth::user()->hasRole('User')) {
                                $imp_cals = DataAccess::select('right_option')->where('user_id', Auth::user()->id)->where('right_name', 'selimpcal')->where('right_option', 'LIKE', $request->input('sel_fyear') . '%')->get();
                                if ($imp_cals->count()) {
                                    foreach ($imp_cals as $imp_cal) {
                                        $da_months[] = date('M', strtotime($imp_cal->right_option));
                                    }
                                }
                            }

                        }
                        else if ($request->input('sel_rtype') == 'Exporter') {
                            if(Auth::user()->hasRole('Administrator') or Auth::user()->hasRole('Manager') or Auth::user()->hasRole('Supervisor')) {
                                $dbimp_cals = ExporterBill::where(DB::raw('YEAR(shipping_bill_date)'),$request->input('sel_fyear'))->distinct()->orderBy(DB::raw('MONTH(shipping_bill_date)'),'ASC')->get([DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\') as imp_month')]);
                                if ($dbimp_cals->count()) {
                                    foreach ($dbimp_cals as $dbimp_cal) {
                                        $da_months[] = $dbimp_cal->imp_month;
                                    }
                                }
                            }
                            else if(Auth::user()->hasRole('User')) {
                                $exp_cals = DataAccess::select('right_option')->where('user_id', Auth::user()->id)->where('right_name', 'selexpcal')->where('right_option', 'LIKE', $request->input('sel_fyear') . '%')->get();
                                if ($exp_cals->count()) {
                                    foreach ($exp_cals as $exp_cal) {
                                        $da_months[] = date('M', strtotime($exp_cal->right_option));
                                    }
                                }
                            }
                        }
                        if(count($da_months)) {
                            //print_r($da_months);
                            return Response::json([
                                'status' => 'success',
                                'message' => 'Months got Successfully',
                                'da_months' => $da_months
                            ]);
                        }
                        else {
                            return Response::json([
                                'status' => 'failed',
                                'message' => 'Months not found in rights'
                            ]);
                        }

                    }
                    else {
                        return Response::json([
                            'status' => 'failed',
                            'message' => 'Year Missing in the Request'
                        ]);
                    }
                }
                else {
                    return Response::json([
                        'status' => 'failed',
                        'message' => 'Year Missing in the Request'
                    ]);
                }
            }
            else {
                return Response::json([
                    'status' => 'failed',
                    'message' => 'Record Type Missing in the Request'
                ]);
            }
        }
        else {
            return Response::json([
                'status' => 'failed',
                'message' => 'Record Type Missing in the Request'
            ]);
        }
    }

    public function ajax_get_user_da_selyear(Request $request){
        if($request->input('seldatatype') == 'importer'){
            $user_years    = User::find($request->input('seluser_id'))->data_access()->where('right_name', 'selimpyear')->get();
            $user_months   = User::find($request->input('seluser_id'))->data_access()->where('right_name', 'selimpmonth')->get();
            $user_hscodes  = User::find($request->input('seluser_id'))->data_access()->where('right_name', 'selimphscode')->get();
            $user_chapters = User::find($request->input('seluser_id'))->data_access()->where('right_name', 'selimpchapter')->get();
            $user_points   = User::find($request->input('seluser_id'))->data_access()->where('right_name', 'selpoints')->get();
            $user_expiry   = User::where('id', $request->input('seluser_id'))->first();
        }
        else if($request->input('seldatatype') == 'exporter') {
            $user_years = User::find($request->input('seluser_id'))->data_access()->where('right_name', 'selexpyear')->get();
            $user_months = User::find($request->input('seluser_id'))->data_access()->where('right_name', 'selexpmonth')->get();
            $user_hscodes = User::find($request->input('seluser_id'))->data_access()->where('right_name', 'selexphscode')->get();
            $user_chapters = User::find($request->input('seluser_id'))->data_access()->where('right_name', 'selexpchapter')->get();
            $user_points = User::find($request->input('seluser_id'))->data_access()->where('right_name', 'selpoints')->get();
            $user_expiry = User::where('id', $request->input('seluser_id'))->first();
        }
        //dd($user_expiry->expiry_date->format('d-m-Y'));
        return Response::json(['user_years' => $user_years,
            'user_months' => $user_months,
            'user_hscodes' => $user_hscodes,
            'user_chapters' => $user_chapters,
            'user_points' => $user_points,
            'user_expiry' => $user_expiry->expiry_date->format('d/m/Y'),
            ]);
    }
    public function ajax_get_uloads (Request $request){
        if($request->has('action')){
            if($request->input('action') == 'ajax_get_u_hscode_imp'){
                $rows = $request->input('ucal_imp_selected');
                $imp_hs_codes = ImporterBill::select(DB::raw('count(*) as count_rows, hs_code as value'))
                    ->when($rows, function ($query, $rows) {
                        $query->where(function ($query) use ($rows) {
                            $cnt = 0;
                            foreach ($rows as $row) {
                                if ($cnt) {
                                    $query->orWhere([
                                        [DB::raw('YEAR(bill_of_entry_date)'), '=', date('Y', strtotime($row))],
                                        [DB::raw('MONTH(bill_of_entry_date)'), '=', (int)date('m', strtotime($row))],
                                    ]);
                                } else {
                                    $query->where([
                                        [DB::raw('YEAR(bill_of_entry_date)'), '=', date('Y', strtotime($row))],
                                        [DB::raw('MONTH(bill_of_entry_date)'), '=', (int)date('m', strtotime($row))],
                                    ]);
                                }
                                $cnt = $cnt + 1;
                            }
                        });
                        return $query;
                    })
                    ->groupBy('hs_code')
                    ->get();
                $imp_chapters = ImporterBill::select(DB::raw('count(*) as count_rows, chapter as value'))
                    ->when($rows, function ($query, $rows) {
                        $query->where(function ($query) use ($rows) {
                            $cnt = 0;
                            foreach ($rows as $row) {
                                if ($cnt) {
                                    $query->orWhere([
                                        [DB::raw('YEAR(bill_of_entry_date)'), '=', date('Y', strtotime($row))],
                                        [DB::raw('MONTH(bill_of_entry_date)'), '=', (int)date('m', strtotime($row))],
                                    ]);
                                } else {
                                    $query->where([
                                        [DB::raw('YEAR(bill_of_entry_date)'), '=', date('Y', strtotime($row))],
                                        [DB::raw('MONTH(bill_of_entry_date)'), '=', (int)date('m', strtotime($row))],
                                    ]);
                                }
                                $cnt = $cnt + 1;
                            }
                        });
                        return $query;
                    })
                    ->groupBy('chapter')
                    ->get();
                $imp_indian_port = ImporterBill::select(DB::raw('count(*) as count_rows, indian_port as value'))
                    ->when($rows, function ($query, $rows) {
                        $query->where(function ($query) use ($rows) {
                            $cnt = 0;
                            foreach ($rows as $row) {
                                if ($cnt) {
                                    $query->orWhere([
                                        [DB::raw('YEAR(bill_of_entry_date)'), '=', date('Y', strtotime($row))],
                                        [DB::raw('MONTH(bill_of_entry_date)'), '=', (int)date('m', strtotime($row))],
                                    ]);
                                } else {
                                    $query->where([
                                        [DB::raw('YEAR(bill_of_entry_date)'), '=', date('Y', strtotime($row))],
                                        [DB::raw('MONTH(bill_of_entry_date)'), '=', (int)date('m', strtotime($row))],
                                    ]);
                                }
                                $cnt = $cnt + 1;
                            }
                        });
                        return $query;
                    })
                    ->groupBy('indian_port')
                    ->get();
                if($imp_hs_codes->count()) {
                    $response = array(
                        'status' => 'success',
                        'msg'    => 'Data Loading..',
                        'imp_hs_codes' => $imp_hs_codes,
                        'imp_chapters' => $imp_chapters,
                        'imp_indian_port' => $imp_indian_port,
                    );
                }
                else {
                    $response = array(
                        'status' => 'failed',
                        'msg' => 'Data not Found',
                    );
                }
                return Response::json($response);
            }
            if($request->input('action') == 'ajax_get_u_chapter_imp'){
                $imp_cal_rows     = $request->input('sel_u_imp_cal');
                $imp_hs_code_rows = $request->input('sel_u_imp_hscode');

                $imp_chapters = ImporterBill::select(DB::raw('count(*) as count_rows, chapter as value'))
                    ->when($imp_cal_rows, function ($query, $imp_cal_rows) {
                        $query->where(function ($query) use ($imp_cal_rows) {
                            $cnt = 0;
                            foreach ($imp_cal_rows as $row) {
                                if ($cnt) {
                                    $query->orWhere([
                                        [DB::raw('YEAR(bill_of_entry_date)'), '=', date('Y', strtotime($row))],
                                        [DB::raw('MONTH(bill_of_entry_date)'), '=', (int)date('m', strtotime($row))],
                                    ]);
                                } else {
                                    $query->where([
                                        [DB::raw('YEAR(bill_of_entry_date)'), '=', date('Y', strtotime($row))],
                                        [DB::raw('MONTH(bill_of_entry_date)'), '=', (int)date('m', strtotime($row))],
                                    ]);
                                }
                                $cnt = $cnt + 1;
                            }
                        });
                        return $query;
                    })
                    ->when($imp_hs_code_rows, function ($query, $imp_hs_code_rows) {
                        $query->where(function ($query) use ($imp_hs_code_rows) {
                            $cnt = 0;
                            foreach ($imp_hs_code_rows as $row) {
                                if ($cnt) {
                                    $query->orWhere([
                                        ['hs_code', '=',$row],
                                    ]);
                                } else {
                                    $query->where([
                                        ['hs_code', '=',$row],
                                    ]);
                                }
                                $cnt = $cnt + 1;
                            }
                        });

                        return $query;
                    })
                    ->groupBy('chapter')
                    ->get();
                $imp_indian_port = ImporterBill::select(DB::raw('count(*) as count_rows, indian_port as value'))
                    ->when($imp_cal_rows, function ($query, $imp_cal_rows) {
                        $query->where(function ($query) use ($imp_cal_rows) {
                            $cnt = 0;
                            foreach ($imp_cal_rows as $row) {
                                if ($cnt) {
                                    $query->orWhere([
                                        [DB::raw('YEAR(bill_of_entry_date)'), '=', date('Y', strtotime($row))],
                                        [DB::raw('MONTH(bill_of_entry_date)'), '=', (int)date('m', strtotime($row))],
                                    ]);
                                } else {
                                    $query->where([
                                        [DB::raw('YEAR(bill_of_entry_date)'), '=', date('Y', strtotime($row))],
                                        [DB::raw('MONTH(bill_of_entry_date)'), '=', (int)date('m', strtotime($row))],
                                    ]);
                                }
                                $cnt = $cnt + 1;
                            }
                        });
                        return $query;
                    })
                    ->when($imp_hs_code_rows, function ($query, $imp_hs_code_rows) {
                        $query->where(function ($query) use ($imp_hs_code_rows) {
                            $cnt = 0;
                            foreach ($imp_hs_code_rows as $row) {
                                if ($cnt) {
                                    $query->orWhere([
                                        ['hs_code', '=', $row],
                                    ]);
                                } else {
                                    $query->where([
                                        ['hs_code', '=', $row],
                                    ]);
                                }
                                $cnt = $cnt + 1;
                            }
                        });
                        return $query;
                    })
                    ->groupBy('indian_port')
                    ->get();
                if ($imp_chapters->count()) {
                    $response = array(
                        'status' => 'success',
                        'msg' => 'Data Loading..',
                        'imp_chapters' => $imp_chapters,
                        'imp_indian_port' => $imp_indian_port,
                    );
                }
                else {
                    $response = array(
                        'status' => 'failed',
                        'msg' => 'Data not Found',
                    );
                }
                return Response::json($response);
            }
            if($request->input('action') == 'ajax_get_u_port_imp'){
                $imp_cal_rows = $request->input('sel_u_imp_cal');
                $imp_hs_code_rows = $request->input('sel_u_imp_hscode');
                $imp_chapter_rows = $request->input('sel_u_imp_chapter');

                $imp_indian_port = ImporterBill::select(DB::raw('count(*) as count_rows, indian_port as value'))
                    ->when($imp_cal_rows, function ($query, $imp_cal_rows) {
                        $query->where(function ($query) use ($imp_cal_rows) {
                            $cnt = 0;
                            foreach ($imp_cal_rows as $row) {
                                if ($cnt) {
                                    $query->orWhere([
                                        [DB::raw('YEAR(bill_of_entry_date)'), '=', date('Y', strtotime($row))],
                                        [DB::raw('MONTH(bill_of_entry_date)'), '=', (int)date('m', strtotime($row))],
                                    ]);
                                } else {
                                    $query->where([
                                        [DB::raw('YEAR(bill_of_entry_date)'), '=', date('Y', strtotime($row))],
                                        [DB::raw('MONTH(bill_of_entry_date)'), '=', (int)date('m', strtotime($row))],
                                    ]);
                                }
                                $cnt = $cnt + 1;
                            }
                        });
                        return $query;
                    })
                    ->when($imp_hs_code_rows, function ($query, $imp_hs_code_rows) {
                        $query->where(function ($query) use ($imp_hs_code_rows) {
                            $cnt = 0;
                            foreach ($imp_hs_code_rows as $row) {
                                if ($cnt) {
                                    $query->orWhere([
                                        ['hs_code', '=', $row],
                                    ]);
                                } else {
                                    $query->where([
                                        ['hs_code', '=', $row],
                                    ]);
                                }
                                $cnt = $cnt + 1;
                            }
                        });
                        return $query;
                    })
                    ->when($imp_chapter_rows, function ($query, $imp_chapter_rows) {
                        $query->where(function ($query) use ($imp_chapter_rows) {
                            $cnt = 0;
                            foreach ($imp_chapter_rows as $row) {
                                if ($cnt) {
                                    $query->orWhere([
                                        ['chapter', '=', $row],
                                    ]);
                                } else {
                                    $query->where([
                                        ['chapter', '=', $row],
                                    ]);
                                }
                                $cnt = $cnt + 1;
                            }
                        });
                        return $query;
                    })
                    ->groupBy('indian_port')
                    ->get();
                if ($imp_indian_port->count()) {
                    $response = array(
                        'status' => 'success',
                        'msg' => 'Data Loading..',
                        'imp_indian_port' => $imp_indian_port,
                    );
                }
                else {
                    $response = array(
                        'status' => 'failed',
                        'msg' => 'data not Found',
                    );
                }
                return Response::json($response);
            }

            if($request->input('action') == 'ajax_get_u_hscode_exp'){
                    $rows = $request->input('ucal_exp_selected');
                    $exp_hs_codes = ExporterBill::select(DB::raw('count(*) as count_rows, hs_code as value'))
                        ->when($rows, function ($query, $rows) {
                            $query->where(function ($query) use ($rows) {
                                $cnt = 0;
                                foreach ($rows as $row) {
                                    if ($cnt) {
                                        $query->orWhere([
                                            [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
                                            [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
                                        ]);
                                    } else {
                                        $query->where([
                                            [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
                                            [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
                                        ]);
                                    }
                                    $cnt = $cnt + 1;
                                }
                            });
                            return $query;
                        })
                        ->groupBy('hs_code')
                        ->get();
                    $exp_chapters = ExporterBill::select(DB::raw('count(*) as count_rows, chapter as value'))
                        ->when($rows, function ($query, $rows) {
                            $query->where(function ($query) use ($rows) {
                                $cnt = 0;
                                foreach ($rows as $row) {
                                    if ($cnt) {
                                        $query->orWhere([
                                            [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
                                            [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
                                        ]);
                                    } else {
                                        $query->where([
                                            [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
                                            [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
                                        ]);
                                    }
                                    $cnt = $cnt + 1;
                                }
                            });
                            return $query;
                        })
                        ->groupBy('chapter')
                        ->get();
                    $exp_indian_port = ExporterBill::select(DB::raw('count(*) as count_rows, indian_port as value'))
                        ->when($rows, function ($query, $rows) {
                            $query->where(function ($query) use ($rows) {
                                $cnt = 0;
                                foreach ($rows as $row) {
                                    if ($cnt) {
                                        $query->orWhere([
                                            [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
                                            [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
                                        ]);
                                    } else {
                                        $query->where([
                                            [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
                                            [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
                                        ]);
                                    }
                                    $cnt = $cnt + 1;
                                }
                            });
                            return $query;
                        })
                        ->groupBy('indian_port')
                        ->get();
                    if($exp_hs_codes->count()) {
                        $response = array(
                            'status' => 'success',
                            'msg'    => 'Data Loading..',
                            'exp_hs_codes' => $exp_hs_codes,
                            'exp_chapters' => $exp_chapters,
                            'exp_indian_port' => $exp_indian_port,
                        );
                    }
                    else {
                        $response = array(
                            'status' => 'failed',
                            'msg' => 'Hs Code not Found',
                            'hs_codes' => json_encode($exp_hs_codes),
                        );
                    }
                    return Response::json($response);

            }
            if($request->input('action') == 'ajax_get_u_chapter_exp'){
                        $exp_cal_rows     = $request->input('sel_u_exp_cal');
                        $exp_hs_code_rows = $request->input('sel_u_exp_hscode');

                        $exp_chapters = ExporterBill::select(DB::raw('count(*) as count_rows, chapter as value'))
                            ->when($exp_cal_rows, function ($query, $exp_cal_rows) {
                                $query->where(function ($query) use ($exp_cal_rows) {
                                    $cnt = 0;
                                    foreach ($exp_cal_rows as $row) {
                                        if ($cnt) {
                                            $query->orWhere([
                                                [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
                                                [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
                                            ]);
                                        } else {
                                            $query->where([
                                                [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
                                                [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
                                            ]);
                                        }
                                        $cnt = $cnt + 1;
                                    }
                                });
                                return $query;
                            })
                            ->when($exp_hs_code_rows, function ($query, $exp_hs_code_rows) {
                                $query->where(function ($query) use ($exp_hs_code_rows) {
                                    $cnt = 0;
                                    foreach ($exp_hs_code_rows as $row) {
                                        if ($cnt) {
                                            $query->orWhere([
                                                ['hs_code', '=', $row],
                                            ]);
                                        } else {
                                            $query->where([
                                                ['hs_code', '=', $row],
                                            ]);
                                        }
                                        $cnt = $cnt + 1;
                                    }
                                });
                                return $query;
                            })
                            ->groupBy('chapter') 
                            ->get();
                        $exp_indian_port = ExporterBill::select(DB::raw('count(*) as count_rows, indian_port as value'))
                            ->when($exp_cal_rows, function ($query, $exp_cal_rows) {
                                $query->where(function ($query) use ($exp_cal_rows) {
                                    $cnt = 0;
                                    foreach ($exp_cal_rows as $row) {
                                        if ($cnt) {
                                            $query->orWhere([
                                                [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
                                                [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
                                            ]);
                                        } else {
                                            $query->where([
                                                [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
                                                [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
                                            ]);
                                        }
                                        $cnt = $cnt + 1;
                                    }
                                });
                                return $query;
                            })
                            ->when($exp_hs_code_rows, function ($query, $exp_hs_code_rows) {
                                $query->where(function ($query) use ($exp_hs_code_rows) {
                                    $cnt = 0;
                                    foreach ($exp_hs_code_rows as $row) {
                                        if ($cnt) {
                                            $query->orWhere([
                                                ['hs_code', '=', $row],
                                            ]);
                                        } else {
                                            $query->where([
                                                ['hs_code', '=', $row],
                                            ]);
                                        }
                                        $cnt = $cnt + 1;
                                    }
                                });
                                return $query;
                            })
                            ->groupBy('indian_port')
                            ->get();
                        if ($exp_chapters->count()) {
                            $response = array(
                                'status' => 'success',
                                'msg' => 'Data Loading..',
                                'exp_chapters' => $exp_chapters,
                                'exp_indian_port' => $exp_indian_port,
                            );
                        } else {
                            $response = array(
                                'status' => 'failed',
                                'msg' => 'Hs Code not Found',
                                'hs_codes' => $exp_chapters,
                            );
                        }
                        return Response::json($response);

            }
            if($request->input('action') == 'ajax_get_u_port_exp'){

                    $exp_cal_rows = $request->input('sel_u_exp_cal');
                    $exp_hs_code_rows = $request->input('sel_u_exp_hscode');
                    $exp_chapter_rows = $request->input('sel_u_exp_chapter');

                    $exp_indian_port = ExporterBill::select(DB::raw('count(*) as count_rows, indian_port as value'))
                        ->when($exp_cal_rows, function ($query, $exp_cal_rows) {
                            $query->where(function ($query) use ($exp_cal_rows) {
                                $cnt = 0;
                                foreach ($exp_cal_rows as $row) {
                                    if ($cnt) {
                                        $query->orWhere([
                                            [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
                                            [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
                                        ]);
                                    } else {
                                        $query->where([
                                            [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
                                            [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
                                        ]);
                                    }
                                    $cnt = $cnt + 1;
                                }
                            });
                            return $query;
                        })
                        ->when($exp_hs_code_rows, function ($query, $exp_hs_code_rows) {
                            $query->where(function ($query) use ($exp_hs_code_rows) {
                                $cnt = 0;
                                foreach ($exp_hs_code_rows as $row) {
                                    if ($cnt) {
                                        $query->orWhere([
                                            ['hs_code', '=', $row],
                                        ]);
                                    } else {
                                        $query->where([
                                            ['hs_code', '=', $row],
                                        ]);
                                    }
                                    $cnt = $cnt + 1;
                                }
                            });
                            return $query;
                        })
                        ->when($exp_chapter_rows, function ($query, $exp_chapter_rows) {
                            $query->where(function ($query) use ($exp_chapter_rows) {
                                $cnt = 0;
                                foreach ($exp_chapter_rows as $row) {
                                    if ($cnt) {
                                        $query->orWhere([
                                            ['chapter', '=', $row],
                                        ]);
                                    } else {
                                        $query->where([
                                            ['chapter', '=', $row],
                                        ]);
                                    }
                                    $cnt = $cnt + 1;
                                }
                            });
                            return $query;
                        })
                        ->groupBy('indian_port')
                        ->get();
                    if ($exp_indian_port->count()) {
                        $response = array(
                            'status' => 'success',
                            'msg' => 'Data Loading..',
                            'exp_indian_port' => $exp_indian_port,
                        );
                    }
                    else {
                        $response = array(
                            'status' => 'failed',
                            'msg' => 'data not Found',
                        );
                    }
                    return Response::json($response);

            }
        }
        else {
            $response = array(
                'status' => 'failed',
                'msg' => 'Action Missing, Try Again Latter!',
            );
            return Response::json($response);
        }
    }
}
