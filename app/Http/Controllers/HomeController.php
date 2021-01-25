<?php

namespace App\Http\Controllers;

use App\Http\Middleware\Role;
use App\User;
use App\Models\DataAccess;
use App\Models\Role as Roles;
use App\Models\ImporterBill;
use App\Models\ExporterBill;
use App\Models\ExportBillsFile;

//use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

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
    public function index()
    {
       
      
        
       $da_years = [];
        $da_months = [];
        $last_year = '';
        if(Auth::user()->hasRole('Administrator') or Auth::user()->hasRole('Manager') or Auth::user()->hasRole('Supervisor')) {
        
          $imp_cals = ImporterBill::distinct()->get([DB::raw('YEAR(bill_of_entry_date) as imp_year')])->pluck('imp_year')->toArray();
         
            if (count($imp_cals)) {
                $da_years = array_unique($imp_cals);
                $last_year = array_values(array_slice($da_years, -1))[0];
            }
        }
        else if(Auth::user()->hasRole('User')) {
            $imp_cals = DataAccess::select('right_option')->where('user_id', Auth::user()->id)->where('right_name', 'selimpcal')->get();
            if ($imp_cals->count()) {
                foreach ($imp_cals as $imp_cal) {
                    $da_years[] = date('Y', strtotime($imp_cal->right_option));
                }
                $da_years = array_unique($da_years);
                $last_year = array_values(array_slice($da_years, -1))[0];
            }
        }
  
       

        $da_exp_years = [];
        // if(Auth::user()->hasRole('Administrator') or Auth::user()->hasRole('Manager') or Auth::user()->hasRole('Supervisor')) {
        //     $exp_cals = ExporterBill::distinct()->get([DB::raw('YEAR(shipping_bill_date) as exp_year')]);
        //     if ($exp_cals->count()) {
        //         foreach ($exp_cals as $exp_cal) {
        //             $da_exp_years[] = $exp_cal->exp_year;
        //         }
        //         $da_exp_years = array_unique($da_years);
        //     }
        // }

       
        // else if(Auth::user()->hasRole('User')) {
        //     $exp_cals = DataAccess::select('right_option')->where('user_id', Auth::user()->id)->where('right_name', 'selexpcal')->get();
        //     if ($exp_cals->count()) {
        //         foreach ($exp_cals as $exp_cal) {
        //             $da_exp_years[] = date('Y', strtotime($exp_cal->right_option));
        //         }
        //         $da_exp_years = array_unique($da_exp_years);
        //     }
        // }
        // return $da_exp_years;
        $da_months = DataAccess::where('user_id', Auth::user()->id)->where('right_name','selmonth')->get();
        
        $da_chapters = [];
        $dac_chapters = DataAccess::where('user_id', Auth::user()->id)->where('right_name','selchapter')->get();
        foreach($dac_chapters as $chap){
            $da_chapters[] = $chap->right_option;
        }
            // return   $year = ImporterBill::select(DB::raw('YEAR(bill_of_entry_date) as last_year'))->orderBy(DB::raw('YEAR(bill_of_entry_date)'),'DESC')->first();
            
            
        // find hs code for side bar 
         $side_hscode =  ImporterBill::select(DB::raw('count(*) as order_count, hs_code'))->groupBy('hs_code')->orderBy('order_count','DESC')->get();
                  

       
        $side_hscode_ex =ExporterBill::select(DB::raw('count(*) as order_count, hs_code'))->groupBy('hs_code')->orderBy('order_count','DESC')->get();

        // $side_hscode =[];
        // $side_hscode_ex = [];
       
        $page = 'home';
        return view('home', compact('da_years', 'last_year','da_months', 'da_exp_years','page','side_hscode','side_hscode_ex'));
    }
    public function pagetwo()
    {
        # code...
        return view('pagetwo');
    }
    public function pageone()
    {
        return view('pageone');
        # code...
    }
}
