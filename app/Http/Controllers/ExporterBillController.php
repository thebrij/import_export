<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\ExporterBill;
use App\Models\DataAccess;
use App\Models\ExpChartData;
use App\Models\ExportBillsFile;

use Illuminate\Http\Request;
use App\Exports\ExportExporterBills;
use App\Imports\ImportExporterBills;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use \Carbon\Carbon;

class ExporterBillController extends Controller
{
    public $da_cals;
    public $da_years;
    public $da_months;
    public $da_hscodes;
    public $da_chapters;
    public $da_ports;
    public $file_name;
    public $ferror;
    public $ferrormsg;

    public function __construct()
    {
        $this->da_cals = [];
        $this->da_years = [];
        $this->da_months = [];
        $this->da_hscodes = [];
        $this->da_chapters = [];
        $this->da_ports = [];
        $this->file_name = '';
        $this->middleware(function ($request, $next) {
            if (isset(Auth::user()->role_id) and Auth::user()->role_id != '') {
                if (Auth::user()->hasRole('User')) {

                    $da_get_cals = DataAccess::where('user_id', Auth::user()->id)->where('right_name', 'selexpcal')->get();
                    if (isset($da_get_cals) and !empty($da_get_cals)) {
                        foreach ($da_get_cals as $da_get_cal) {
                            array_push($this->da_cals, $da_get_cal->right_option);
                        }
                    }
                    $da_get_hscodes = DataAccess::where('user_id', Auth::user()->id)->where('right_name', 'selexphscode')->get();
                    if (isset($da_get_hscodes) and !empty($da_get_hscodes)) {
                        foreach ($da_get_hscodes as $da_get_hscode) {
                            array_push($this->da_hscodes, $da_get_hscode->right_option);
                        }
                    }
                    $da_get_chapters = DataAccess::where('user_id', Auth::user()->id)->where('right_name', 'selexpchapter')->get();
                    if (isset($da_get_chapters) and !empty($da_get_chapters)) {
                        foreach ($da_get_chapters as $da_get_chapter) {
                            array_push($this->da_chapters, $da_get_chapter->right_option);
                        }
                    }
                    $da_get_ports = DataAccess::where('user_id', Auth::user()->id)->where('right_name', 'selexpport')->get();
                    if (isset($da_get_ports) and !empty($da_get_ports)) {
                        foreach ($da_get_ports as $da_get_port) {
                            array_push($this->da_ports, $da_get_port->right_option);
                        }
                    }
                }
            }
            return $next($request);
        });
    }

    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return "ok";
    }

    
    public function expoterBillsFileList()
    {

        $exportBillFiles = ExportBillsFile::all();

        return view('exporterbills.export_files_index', ['exportFiles' => $exportBillFiles]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        
        $efilelist[] = 'Select Uploaded Excel';
        $filesInFolder = \File::files(storage_path('app/excel-files/exporters'));
        foreach($filesInFolder as $path) {
            $file = pathinfo($path);
            $efilelist[] = $file['basename'] ;
        }

        $file_names = ExportBillsFile::where('status',2)->get(['filename']);
        // $file_names = [];
        // if($db_filenames->count()){
        //     foreach($db_filenames as $db_filename){
        //         $file_names[] = $db_filename->file_name;
        //     }
        // }


         $chart_update = ExpChartData::select('updated_at')->first();
        return view('exporterbills.create', compact('efilelist', 'file_names','chart_update'));
    }

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $ext =  strtolower($request->file('file')->getClientOriginalExtension());
        if ($ext != 'csv') {
           
            return "file must be a csv file.";
        }
      
        //print_r($_POST);
        $start = microtime(true);
        set_time_limit('7200');
        ini_set('post_max_size','1024M');
        ini_set('upload_max_filesize','1024M');
        ini_set('max_input_time', '7200');
        ini_set('max_execution_time', '7200');
        ini_set('memory_limit', '5G');

        // Validation rules somewhere...
        if (isset($request->efilelist) and $request->efilelist != '0'){
            $path = 'excel-files/exporters/'.$request->efilelist;
            $file_name = $request->efilelist;
        } else {
            $validatedData = $request->validate([
                'file' => 'required',
            ]);

            $file = $request->file('file')->getClientOriginalName();
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $file_name = $filename.'_'.time().'.'.$extension;

            $path = $request->file('file')->storeAs('excel-files/exporters', $file_name);
        }

        
        $time_upload_secs = microtime(true) - $start;
        // echo 'upload time '.$time_upload_secs.' upload';
        ExportBillsFile::create([
            'user_id' => Auth::getUser()->id,
            'filepath' => $path,
            'filename' => $file_name,
        ]);

        $this->file_name = $file_name;
        if($this->ferror){
            // echo $this->ferrormsg;
            return "$this->ferrormsg";
            // return back()->with('error', $this->ferrormsg);
        } else {
            $time = round($time_upload_secs, 3);
            return 'file uploaded successfully. ( upload time '.$time.' )';
            //  return response()->json(['success'=>'CSV file successfully Imported in the system']);
            // return back()->with('status', __('Excel file data successfully Imported in the system'));
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

    public function del_file_data (Request $request){
        $validatedData = $request->validate([
            'sel_file_to_delete' => 'required',
        ]);
        if($request->has('sel_file_to_delete')){
            ExporterBill::where('file_name', $request->input('sel_file_to_delete'))->delete();
        }
        $filename = ExportBillsFile::where('filename',$request->sel_file_to_delete)->first();
        if($filename){
            $filename->delete();

        }

        return back()->with('status', __('Selected file\'s Data Deleted Successfully' ));
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function export()
    {
        //return Excel::download(new ExportExporterBills, 'exporterbills.xlsx');
        return response()->download(storage_path("app/excel-files/ImportExportSampleExporterData.csv"));
    }
    public function ajax_exporter_export (Request $request){
        if($request->has('rtype')){
            if($request->input('rtype') == 'Exporter'){
                $clauses = [];
               $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();

        $fyear       = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
                $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
                $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
                $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
                $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
                $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
                $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
                $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
                $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;

                $exporters = ExporterBill::select(['id', 'shipping_bill_no', 'shipping_bill_date', 'iec', 'exporter', 'exporter_address_n_city'
                    , 'city', 'pin', 'state', 'contact_no', 'e_mail_id', 'consinee', 'consinee_address', 'port_code'
                    , 'foreign_port', 'foreign_country', 'hs_code', 'chapter', 'product_descripition', 'quantity'
                    , 'unit_quantity', 'item_rate_in_fc', 'currency', 'total_value_in_usd'
                    , 'unit_rate_in_usd_exchange', 'exchange_rate_usd', 'total_value_in_usd_exchange'
                    , 'unit_value_in_inr', 'fob_in_inr', 'invoice_serial_number', 'invoice_number', 'item_number'
                    , 'drawback', DB::raw('MONTH(month) month'), 'year', 'mode', 'indian_port', 'cush'
                    , 'created_by', 'updated_by', 'created_at', 'updated_at'])
                    ->when(Auth::user()->hasRole('User'), function ($query) {
                        $query->when($this->da_cals, function ($query) {
                            $rows = $this->da_cals;
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
                        });
                        return $query;
                    })
                    ->when(Auth::user()->hasRole('User'), function ($query) {
                        $query->when($this->da_hscodes, function ($query) {
                            $rows = $this->da_hscodes;
                            return $query->where(function ($query) use ($rows) {
                                for ($i = 0; $i < count($rows); $i++) {
                                    $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
                                }
                            });
                        });
                        return $query;
                    })
                    ->when(Auth::user()->hasRole('User'), function ($query) {
                        $query->when($this->da_chapters, function ($query) {
                            $rows = $this->da_chapters;
                            return $query->where(function ($query) use ($rows) {
                                for ($i = 0; $i < count($rows); $i++) {
                                    $query->orwhere('chapter', '=', $rows[$i]);
                                }
                            });
                        });
                        return $query;
                    })
                    ->when(Auth::user()->hasRole('User'), function ($query) {
                        $query->when($this->da_ports, function ($query) {
                            $rows = $this->da_ports;
                            return $query->where(function ($query) use ($rows) {
                                for ($i = 0; $i < count($rows); $i++) {
                                    $query->orwhere('indian_port', '=', $rows[$i]);
                                }
                            });
                        });
                        return $query;
                    })
                    ->when($fyear, function ($query, $fyear) {
                        return $query->where('year', '=', $fyear);
                    })
                    ->when($fmonth, function ($query, $fmonth) {
                        return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
                    })
                    ->when($fproduct, function ($query, $fproduct) {
                        return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
                    })
                    ->when($fhscode, function ($query, $fhscode) {
                        $fhscode = explode(',', $fhscode);
                        $fhscode = array_map('trim',$fhscode);
                        return $query->where(function ($query) use ($fhscode) {
                            for ($i = 0; $i < count($fhscode); $i++) {
                                $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
                            }
                        });
                    })
                    ->when($fchapter, function ($query, $fchapter) {
                        $fchapter = explode(',', $fchapter);
                        $fchapter = array_map('trim',$fchapter);
                        return $query->where(function ($query) use ($fchapter) {
                            for ($i = 0; $i < count($fchapter); $i++) {
                                $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
                            }
                        });
                    })
                    ->when($fcountry, function ($query, $fcountry) {
                        return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
                    })
                    ->when($fport, function ($query, $fport) {
                        return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
                    })
                    ->when($funit, function ($query, $funit) {
                        return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
                    })
                    ->when($fimpexpname, function ($query, $fimpexpname) {
                        return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
                    })
                    ->get()
                ;
                if($exporters->count()) {
                    if (Auth::user()->hasRole('Administrator')  == '1' or  Auth::user()->hasRole('Manager')  == '1'
                        or  Auth::user()->hasRole('Supervisor') == '1') {
                        return (new FastExcel($exporters))->download('ImportExportExporter.xlsx');
                    }
                    else if(Auth::user()->hasRole('User') == '1') {
                        $points = DataAccess::where('user_id', Auth::user()->id)->where('right_name', 'selpoints')->first();
                        if ($points->count()) {
                            if ($exporters->count() < $points->right_option) {
                                $points->update(['right_option' => ($points->right_option - $exporters->count())]);
                                return (new FastExcel($exporters))->download('ImportExportExporter.xlsx');
                            }
                            else {
                                return back()->withErrors(['points' => 'Insufficient Download Points', ]);
                            }
                        }
                        else {
                            return back()->with('status', __('Insufficient Download Points'));
                        }
                    }
                    else {
                        return back()->with('status', __('User Have not Sufficient Privileges'));
                    }
                }
                else {
                    return back()->with('status', __('No Data Found to Download'));
                }
            }
            else if($request->input('rtype') == 'Importer'){
                return back()->withErrors(['record_type' => 'Request Received on Wrong Controller!', ]);
            }
            else {
                return back()->withErrors(['record_type' => 'Record Type Value is Corrupted!', ]);
            }
        }
        else {
            return back()->withErrors(['record_type' => 'Plz choose Importer or Exporter from Filters Drop Down!', ]);
        }
        return back()->withErrors(['record_type' => 'Some Error Occured', ]);
    }

    public function get_ajax_side_bar (Request $request)
    {
        $clauses = [];
       $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();

        $fyear       = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;

        $exp_d_hs_code = ExporterBill::select(DB::raw('DISTINCT hs_code AS d_hs_code, count(*) as order_count'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_hscodes, function ($query) {
                    $rows = $this->da_hscodes;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_chapters, function ($query) {
                    $rows = $this->da_chapters;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('chapter', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_ports, function ($query) {
                    $rows = $this->da_ports;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('indian_port', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where('year', '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
            })
            ->when($fhscode, function ($query, $fhscode) {
                $fhscode = explode(',', $fhscode);
                $fhscode = array_map('trim',$fhscode);
                return $query->where(function ($query) use ($fhscode) {
                    for ($i = 0; $i < count($fhscode); $i++) {
                        $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
                    }
                });
            })
            ->when($fchapter, function ($query, $fchapter) {
                $fchapter = explode(',', $fchapter);
                $fchapter = array_map('trim',$fchapter);
                return $query->where(function ($query) use ($fchapter) {
                    for ($i = 0; $i < count($fchapter); $i++) {
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('hs_code')
            ->orderBy(DB::raw('count(*)'),'DESC')
            ->get();

        $keys = array("d_hs_code"=>1, "order_count"=>2);
        $exp_sb_hs_code = array_map(function($a) use($keys){
            return array_intersect_key($a,$keys);
        }, $exp_d_hs_code->toArray());
        //dd($this->da_chapters);
        $exp_sidebar['sb_hs_codes'] = $exp_sb_hs_code;

        $exp_d_country = ExporterBill::select(DB::raw('DISTINCT foreign_country AS d_country, count(*) as order_count'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_hscodes, function ($query) {
                    $rows = $this->da_hscodes;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_chapters, function ($query) {
                    $rows = $this->da_chapters;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('chapter', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_ports, function ($query) {
                    $rows = $this->da_ports;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('indian_port', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where('year', '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
            })
            ->when($fhscode, function ($query, $fhscode) {
                $fhscode = explode(',', $fhscode);
                $fhscode = array_map('trim',$fhscode);
                return $query->where(function ($query) use ($fhscode) {
                    for ($i = 0; $i < count($fhscode); $i++) {
                        $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
                    }
                });
            })
            ->when($fchapter, function ($query, $fchapter) {
                $fchapter = explode(',', $fchapter);
                $fchapter = array_map('trim',$fchapter);
                return $query->where(function ($query) use ($fchapter) {
                    for ($i = 0; $i < count($fchapter); $i++) {
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('foreign_country')
            ->orderBy(DB::raw('count(*)'),'DESC')
            ->get();
        $keys = array("d_country"=>1, "order_count"=>2);
        $exp_sb_country = array_map(function($a) use($keys){
            return array_intersect_key($a,$keys);
        }, $exp_d_country->toArray());
        $exp_sidebar['sb_countries'] = $exp_sb_country;

        $exp_d_port = ExporterBill::select(DB::raw('DISTINCT indian_port AS d_port, count(*) as order_count'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_hscodes, function ($query) {
                    $rows = $this->da_hscodes;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_chapters, function ($query) {
                    $rows = $this->da_chapters;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('chapter', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_ports, function ($query) {
                    $rows = $this->da_ports;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('indian_port', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where('year', '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
            })
            ->when($fhscode, function ($query, $fhscode) {
                $fhscode = explode(',', $fhscode);
                $fhscode = array_map('trim',$fhscode);
                return $query->where(function ($query) use ($fhscode) {
                    for ($i = 0; $i < count($fhscode); $i++) {
                        $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
                    }
                });
            })
            ->when($fchapter, function ($query, $fchapter) {
                $fchapter = explode(',', $fchapter);
                $fchapter = array_map('trim',$fchapter);
                return $query->where(function ($query) use ($fchapter) {
                    for ($i = 0; $i < count($fchapter); $i++) {
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('indian_port')
            ->orderBy(DB::raw('count(*)'),'DESC')
            ->get();
        $keys = array("d_port"=>1, "order_count"=>2);
        $exp_sb_port = array_map(function($a) use($keys){
            return array_intersect_key($a,$keys);
        }, $exp_d_port->toArray());
        $exp_sidebar['sb_ports'] = $exp_sb_port;

        $exp_d_unit = ExporterBill::select(DB::raw('DISTINCT unit_quantity AS d_unit, count(*) as order_count'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_hscodes, function ($query) {
                    $rows = $this->da_hscodes;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_chapters, function ($query) {
                    $rows = $this->da_chapters;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('chapter', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_ports, function ($query) {
                    $rows = $this->da_ports;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('indian_port', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where('year', '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
            })
            ->when($fhscode, function ($query, $fhscode) {
                $fhscode = explode(',', $fhscode);
                $fhscode = array_map('trim',$fhscode);
                return $query->where(function ($query) use ($fhscode) {
                    for ($i = 0; $i < count($fhscode); $i++) {
                        $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
                    }
                });
            })
            ->when($fchapter, function ($query, $fchapter) {
                $fchapter = explode(',', $fchapter);
                $fchapter = array_map('trim',$fchapter);
                return $query->where(function ($query) use ($fchapter) {
                    for ($i = 0; $i < count($fchapter); $i++) {
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('unit_quantity')
            ->orderBy(DB::raw('count(*)'),'DESC')
            ->get();
        $keys = array("d_unit"=>1, "order_count"=>2);
        $exp_sb_unit = array_map(function($a) use($keys){
            return array_intersect_key($a,$keys);
        }, $exp_d_unit->toArray());
        $exp_sidebar['sb_units'] = $exp_sb_unit;


        //dd($exp_sidebar);
        return $exp_sidebar;
    }
    public function get_ajax(Request $request)
    {
        
        $clauses = [];
        $fyear       = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:null;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;

   
        $exporters = ExporterBill::select(['id', 'shipping_bill_no', 'shipping_bill_date', 'iec', 'exporter', 'exporter_address_n_city'
                                         , 'city', 'pin', 'state', 'contact_no', 'e_mail_id', 'consinee', 'consinee_address', 'port_code'
                                         , 'foreign_port', 'foreign_country', 'hs_code', 'chapter', 'product_descripition', 'quantity'
                                         , 'unit_quantity', 'item_rate_in_fc', 'currency', 'total_value_in_usd'
                                         , 'unit_rate_in_usd_exchange', 'exchange_rate_usd', 'total_value_in_usd_exchange'
                                         , 'unit_value_in_inr', 'fob_in_inr', 'invoice_serial_number', 'invoice_number', 'item_number'
                                         , 'drawback', DB::raw('MONTH(month) month'), 'year', 'mode', 'indian_port', 'cush'
                                         , 'created_by', 'updated_by', 'created_at', 'updated_at'])
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where('year', '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_hscodes, function ($query) {
                    $rows = $this->da_hscodes;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_chapters, function ($query) {
                    $rows = $this->da_chapters;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('chapter', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_ports, function ($query) {
                    $rows = $this->da_ports;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('indian_port', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
           
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
            })
            ->when($fhscode, function ($query, $fhscode) {
                $fhscode = explode(',', $fhscode);
                $fhscode = array_map('trim',$fhscode);
                return $query->where(function ($query) use ($fhscode) {
                    for ($i = 0; $i < count($fhscode); $i++) {
                        $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
                    }
                });
            })
            ->when($fchapter, function ($query, $fchapter) {
                $fchapter = explode(',', $fchapter);
                $fchapter = array_map('trim',$fchapter);
                return $query->where(function ($query) use ($fchapter) {
                    for ($i = 0; $i < count($fchapter); $i++) {
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
            })
        ;
        //$importers = ImporterBill::selectRaw('id, shipping_bill_no, shipping_bill_date, iec, exporter, exporter_address_n_city, city, pin, state, contact_no, e_mail_id, consinee, consinee_address, port_code, foreign_port, foreign_country, hs_code, chapter, product_descripition, quantity, unit_quantity, item_rate_in_fc, currency, total_value_in_usd, unit_rate_in_usd_exchange, exchange_rate_usd, total_value_in_usd_exchange, unit_value_in_inr, fob_in_inr, invoice_serial_number, invoice_number, item_number, drawback, month, year, mode, indian_port, cush, created_by, updated_by, created_at, updated_at');
        return datatables()->of($exporters)->make(true);
    }
    public function get_ajax_top_usd (Request $request){
        $clauses = [];


        $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();

        $fyear       = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;

        $data = [];
        $result = ExpChartData::select('top15_usd','exporter')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',1)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;
    



        // $exporters_top_usd = ExporterBill::select('exporter',DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS top15_usd'))
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_cals, function ($query) {
        //             $rows = $this->da_cals;
        //             $query->where(function ($query) use ($rows) {
        //                 $cnt = 0;
        //                 foreach ($rows as $row) {
        //                     if ($cnt) {
        //                         $query->orWhere([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     } else {
        //                         $query->where([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     }
        //                     $cnt = $cnt + 1;
        //                 }
        //             });
        //             return $query;
        //         });
        //         return $query;
        //     })
        //     ->when($fyear, function ($query, $fyear) {
        //         return $query->where('year', '=', $fyear);
        //     })
        //     ->when($fmonth, function ($query, $fmonth) {
        //         return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_hscodes, function ($query) {
        //             $rows = $this->da_hscodes;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_chapters, function ($query) {
        //             $rows = $this->da_chapters;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('chapter', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_ports, function ($query) {
        //             $rows = $this->da_ports;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('indian_port', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
            
        //     ->when($fproduct, function ($query, $fproduct) {
        //         return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
        //     })
        //     ->when($fhscode, function ($query, $fhscode) {
        //         $fhscode = explode(',', $fhscode);
        //         $fhscode = array_map('trim',$fhscode);
        //         return $query->where(function ($query) use ($fhscode) {
        //             for ($i = 0; $i < count($fhscode); $i++) {
        //                 $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
        //             }
        //         });
        //     })
        //     ->when($fchapter, function ($query, $fchapter) {
        //         $fchapter = explode(',', $fchapter);
        //         $fchapter = array_map('trim',$fchapter);
        //         return $query->where(function ($query) use ($fchapter) {
        //             for ($i = 0; $i < count($fchapter); $i++) {
        //                 $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
        //             }
        //         });
        //     })
        //     ->when($fcountry, function ($query, $fcountry) {
        //         return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
        //     })
        //     ->when($fport, function ($query, $fport) {
        //         return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
        //     })
        //     ->when($funit, function ($query, $funit) {
        //         return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
        //     })
        //     ->when($fimpexpname, function ($query, $fimpexpname) {
        //         return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
        //     })
        //     ->groupBy('exporter')
        //     ->orderBy(DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2)'),'DESC')
        //     ->paginate(15);
        // return $exporters_top_usd;
    }
    public function get_ajax_top_usd_port (Request $request){
        $clauses = [];

        $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();

        $fyear       = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;
        $data = [];
        $result = ExpChartData::select('labeltitle','labelvalue')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',2)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;


        $topusdport = ExporterBill::select(DB::raw('indian_port AS labeltitle'),DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where('year', '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_hscodes, function ($query) {
                    $rows = $this->da_hscodes;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_chapters, function ($query) {
                    $rows = $this->da_chapters;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('chapter', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_ports, function ($query) {
                    $rows = $this->da_ports;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('indian_port', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
           
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
            })
            ->when($fhscode, function ($query, $fhscode) {
                $fhscode = explode(',', $fhscode);
                $fhscode = array_map('trim',$fhscode);
                return $query->where(function ($query) use ($fhscode) {
                    for ($i = 0; $i < count($fhscode); $i++) {
                        $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
                    }
                });
            })
            ->when($fchapter, function ($query, $fchapter) {
                $fchapter = explode(',', $fchapter);
                $fchapter = array_map('trim',$fchapter);
                return $query->where(function ($query) use ($fchapter) {
                    for ($i = 0; $i < count($fchapter); $i++) {
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('indian_port')
            ->orderBy(DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2)'),'DESC')
            ->paginate(15);
        return $topusdport;
    }
    public function get_ajax_top_usd_country (Request $request){
        $clauses = [];
        $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();

        $fyear       = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;
        
        $data = [];
        $result = ExpChartData::select('foreign_country','top15_usd')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',3)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;


        
        // $topusdcountry = ExporterBill::select('foreign_country',DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS top15_usd'))
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_cals, function ($query) {
        //             $rows = $this->da_cals;
        //             $query->where(function ($query) use ($rows) {
        //                 $cnt = 0;
        //                 foreach ($rows as $row) {
        //                     if ($cnt) {
        //                         $query->orWhere([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     } else {
        //                         $query->where([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     }
        //                     $cnt = $cnt + 1;
        //                 }
        //             });
        //             return $query;
        //         });
        //         return $query;
        //     })
        //     ->when($fyear, function ($query, $fyear) {
        //         return $query->where('year', '=', $fyear);
        //     })
        //     ->when($fmonth, function ($query, $fmonth) {
        //         return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_hscodes, function ($query) {
        //             $rows = $this->da_hscodes;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_chapters, function ($query) {
        //             $rows = $this->da_chapters;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('chapter', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_ports, function ($query) {
        //             $rows = $this->da_ports;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('indian_port', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
           
        //     ->when($fproduct, function ($query, $fproduct) {
        //         return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
        //     })
        //     ->when($fhscode, function ($query, $fhscode) {
        //         $fhscode = explode(',', $fhscode);
        //         $fhscode = array_map('trim',$fhscode);
        //         return $query->where(function ($query) use ($fhscode) {
        //             for ($i = 0; $i < count($fhscode); $i++) {
        //                 $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
        //             }
        //         });
        //     })
        //     ->when($fchapter, function ($query, $fchapter) {
        //         $fchapter = explode(',', $fchapter);
        //         $fchapter = array_map('trim',$fchapter);
        //         return $query->where(function ($query) use ($fchapter) {
        //             for ($i = 0; $i < count($fchapter); $i++) {
        //                 $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
        //             }
        //         });
        //     })
        //     ->when($fcountry, function ($query, $fcountry) {
        //         return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
        //     })
        //     ->when($fport, function ($query, $fport) {
        //         return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
        //     })
        //     ->when($funit, function ($query, $funit) {
        //         return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
        //     })
        //     ->when($fimpexpname, function ($query, $fimpexpname) {
        //         return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
        //     })
        //     ->groupBy('foreign_country')
        //     ->orderBy(DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2)'),'DESC')
        //     ->paginate(15);
        return $topusdcountry;
    }

    public function get_ajax_expana_usd_sup (Request $request){
        $clauses = [];
        $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();

        $fyear       = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;



        // $data = [];
        // $result = ExpChartData::select('exporter','top15_usd')->where(function ($query) use ($fyear) {
        //     if ($fyear) {
        //         $query->where('year', '=', $fyear);
        //     }
        //     })->where('api_id',22)->get();
            
        //     foreach($result as $item){
        //         $data['data'] = $result;
        //     }
        // return $data;


        $expanausdsup = ExporterBill::select('exporter',DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS top15_usd'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_hscodes, function ($query) {
                    $rows = $this->da_hscodes;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_chapters, function ($query) {
                    $rows = $this->da_chapters;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('chapter', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_ports, function ($query) {
                    $rows = $this->da_ports;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('indian_port', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where('year', '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
            })
            ->when($fhscode, function ($query, $fhscode) {
                $fhscode = explode(',', $fhscode);
                $fhscode = array_map('trim',$fhscode);
                return $query->where(function ($query) use ($fhscode) {
                    for ($i = 0; $i < count($fhscode); $i++) {
                        $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
                    }
                });
            })
            ->when($fchapter, function ($query, $fchapter) {
                $fchapter = explode(',', $fchapter);
                $fchapter = array_map('trim',$fchapter);
                return $query->where(function ($query) use ($fchapter) {
                    for ($i = 0; $i < count($fchapter); $i++) {
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('exporter')
            ->orderBy(DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2)'),'DESC')
            ->paginate(15)
        ;
        return $expanausdsup;
    }
    public function get_ajax_expana_usd_cost (Request $request){
        $clauses = [];
        $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();

        $fyear       = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;


        $data = [];
        $result = ExpChartData::select('exporter','top15_usd')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',23)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;


        // $expanausdcost = ExporterBill::select('exporter',DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS top15_usd'))
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_cals, function ($query) {
        //             $rows = $this->da_cals;
        //             $query->where(function ($query) use ($rows) {
        //                 $cnt = 0;
        //                 foreach ($rows as $row) {
        //                     if ($cnt) {
        //                         $query->orWhere([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     } else {
        //                         $query->where([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     }
        //                     $cnt = $cnt + 1;
        //                 }
        //             });
        //             return $query;
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_hscodes, function ($query) {
        //             $rows = $this->da_hscodes;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_chapters, function ($query) {
        //             $rows = $this->da_chapters;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('chapter', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_ports, function ($query) {
        //             $rows = $this->da_ports;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('indian_port', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when($fyear, function ($query, $fyear) {
        //         return $query->where('year', '=', $fyear);
        //     })
        //     ->when($fmonth, function ($query, $fmonth) {
        //         return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
        //     })
        //     ->when($fproduct, function ($query, $fproduct) {
        //         return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
        //     })
        //     ->when($fhscode, function ($query, $fhscode) {
        //         $fhscode = explode(',', $fhscode);
        //         $fhscode = array_map('trim',$fhscode);
        //         return $query->where(function ($query) use ($fhscode) {
        //             for ($i = 0; $i < count($fhscode); $i++) {
        //                 $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
        //             }
        //         });
        //     })
        //     ->when($fchapter, function ($query, $fchapter) {
        //         $fchapter = explode(',', $fchapter);
        //         $fchapter = array_map('trim',$fchapter);
        //         return $query->where(function ($query) use ($fchapter) {
        //             for ($i = 0; $i < count($fchapter); $i++) {
        //                 $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
        //             }
        //         });
        //     })
        //     ->when($fcountry, function ($query, $fcountry) {
        //         return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
        //     })
        //     ->when($fport, function ($query, $fport) {
        //         return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
        //     })
        //     ->when($funit, function ($query, $funit) {
        //         return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
        //     })
        //     ->when($fimpexpname, function ($query, $fimpexpname) {
        //         return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
        //     })
        //     ->groupBy('exporter')
        //     ->orderBy(DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2)'),'DESC')
        //     ->paginate(15);
        // return $expanausdcost;
    }
    public function get_ajax_expana_usd_quantity (Request $request){
        $clauses = [];
       $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();

        $fyear       = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;


        $data = [];
        $result = ExpChartData::select('exporter','top_quantity')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',24)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;


        // $expanausdquantity = ExporterBill::select('exporter',DB::raw('ROUND(SUM(quantity), 2) AS top_quantity'))
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_cals, function ($query) {
        //             $rows = $this->da_cals;
        //             $query->where(function ($query) use ($rows) {
        //                 $cnt = 0;
        //                 foreach ($rows as $row) {
        //                     if ($cnt) {
        //                         $query->orWhere([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     } else {
        //                         $query->where([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     }
        //                     $cnt = $cnt + 1;
        //                 }
        //             });
        //             return $query;
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_hscodes, function ($query) {
        //             $rows = $this->da_hscodes;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_chapters, function ($query) {
        //             $rows = $this->da_chapters;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('chapter', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_ports, function ($query) {
        //             $rows = $this->da_ports;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('indian_port', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when($fyear, function ($query, $fyear) {
        //         return $query->where('year', '=', $fyear);
        //     })
        //     ->when($fmonth, function ($query, $fmonth) {
        //         return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
        //     })
        //     ->when($fproduct, function ($query, $fproduct) {
        //         return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
        //     })
        //     ->when($fhscode, function ($query, $fhscode) {
        //         $fhscode = explode(',', $fhscode);
        //         $fhscode = array_map('trim',$fhscode);
        //         return $query->where(function ($query) use ($fhscode) {
        //             for ($i = 0; $i < count($fhscode); $i++) {
        //                 $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
        //             }
        //         });
        //     })
        //     ->when($fchapter, function ($query, $fchapter) {
        //         $fchapter = explode(',', $fchapter);
        //         $fchapter = array_map('trim',$fchapter);
        //         return $query->where(function ($query) use ($fchapter) {
        //             for ($i = 0; $i < count($fchapter); $i++) {
        //                 $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
        //             }
        //         });
        //     })
        //     ->when($fcountry, function ($query, $fcountry) {
        //         return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
        //     })
        //     ->when($fport, function ($query, $fport) {
        //         return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
        //     })
        //     ->when($funit, function ($query, $funit) {
        //         return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
        //     })
        //     ->when($fimpexpname, function ($query, $fimpexpname) {
        //         return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
        //     })
        //     ->groupBy('exporter')
        //     ->orderBy(DB::raw('ROUND(SUM(quantity), 2)'),'DESC')
        //     ->paginate(15);
        // return $expanausdquantity;
    }

    public function ga_exp_conana_usd_sup (Request $request){
        $clauses = [];
       $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();

        $fyear       = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;


        $data = [];
        $result = ExpChartData::select('consinee','top15_usd')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',25)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;


        // $expanausdsup = ExporterBill::select('consinee',DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS top15_usd'))
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_cals, function ($query) {
        //             $rows = $this->da_cals;
        //             $query->where(function ($query) use ($rows) {
        //                 $cnt = 0;
        //                 foreach ($rows as $row) {
        //                     if ($cnt) {
        //                         $query->orWhere([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     } else {
        //                         $query->where([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     }
        //                     $cnt = $cnt + 1;
        //                 }
        //             });
        //             return $query;
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_hscodes, function ($query) {
        //             $rows = $this->da_hscodes;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_chapters, function ($query) {
        //             $rows = $this->da_chapters;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('chapter', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_ports, function ($query) {
        //             $rows = $this->da_ports;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('indian_port', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when($fyear, function ($query, $fyear) {
        //         return $query->where('year', '=', $fyear);
        //     })
        //     ->when($fmonth, function ($query, $fmonth) {
        //         return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
        //     })
        //     ->when($fproduct, function ($query, $fproduct) {
        //         return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
        //     })
        //     ->when($fhscode, function ($query, $fhscode) {
        //         $fhscode = explode(',', $fhscode);
        //         $fhscode = array_map('trim',$fhscode);
        //         return $query->where(function ($query) use ($fhscode) {
        //             for ($i = 0; $i < count($fhscode); $i++) {
        //                 $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
        //             }
        //         });
        //     })
        //     ->when($fchapter, function ($query, $fchapter) {
        //         $fchapter = explode(',', $fchapter);
        //         $fchapter = array_map('trim',$fchapter);
        //         return $query->where(function ($query) use ($fchapter) {
        //             for ($i = 0; $i < count($fchapter); $i++) {
        //                 $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
        //             }
        //         });
        //     })
        //     ->when($fcountry, function ($query, $fcountry) {
        //         return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
        //     })
        //     ->when($fport, function ($query, $fport) {
        //         return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
        //     })
        //     ->when($funit, function ($query, $funit) {
        //         return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
        //     })
        //     ->when($fimpexpname, function ($query, $fimpexpname) {
        //         return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
        //     })
        //     ->groupBy('consinee')
        //     ->orderBy(DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2)'),'DESC')
        //     ->paginate(15)
        // ;
        // return $expanausdsup;
    }
    public function ga_exp_conana_usd_cost (Request $request){
        $clauses = [];
       $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();

        $fyear       = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;


        $data = [];
        $result = ExpChartData::select('consinee','top15_usd')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',26)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;


        // $expanausdcost = ExporterBill::select('consinee',DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS top15_usd'))
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_cals, function ($query) {
        //             $rows = $this->da_cals;
        //             $query->where(function ($query) use ($rows) {
        //                 $cnt = 0;
        //                 foreach ($rows as $row) {
        //                     if ($cnt) {
        //                         $query->orWhere([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     } else {
        //                         $query->where([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     }
        //                     $cnt = $cnt + 1;
        //                 }
        //             });
        //             return $query;
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_hscodes, function ($query) {
        //             $rows = $this->da_hscodes;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_chapters, function ($query) {
        //             $rows = $this->da_chapters;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('chapter', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_ports, function ($query) {
        //             $rows = $this->da_ports;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('indian_port', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when($fyear, function ($query, $fyear) {
        //         return $query->where('year', '=', $fyear);
        //     })
        //     ->when($fmonth, function ($query, $fmonth) {
        //         return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
        //     })
        //     ->when($fproduct, function ($query, $fproduct) {
        //         return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
        //     })
        //     ->when($fhscode, function ($query, $fhscode) {
        //         $fhscode = explode(',', $fhscode);
        //         $fhscode = array_map('trim',$fhscode);
        //         return $query->where(function ($query) use ($fhscode) {
        //             for ($i = 0; $i < count($fhscode); $i++) {
        //                 $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
        //             }
        //         });
        //     })
        //     ->when($fchapter, function ($query, $fchapter) {
        //         $fchapter = explode(',', $fchapter);
        //         $fchapter = array_map('trim',$fchapter);
        //         return $query->where(function ($query) use ($fchapter) {
        //             for ($i = 0; $i < count($fchapter); $i++) {
        //                 $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
        //             }
        //         });
        //     })
        //     ->when($fcountry, function ($query, $fcountry) {
        //         return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
        //     })
        //     ->when($fport, function ($query, $fport) {
        //         return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
        //     })
        //     ->when($funit, function ($query, $funit) {
        //         return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
        //     })
        //     ->when($fimpexpname, function ($query, $fimpexpname) {
        //         return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
        //     })
        //     ->groupBy('consinee')
        //     ->orderBy(DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2)'),'DESC')
        //     ->paginate(15);
        // return $expanausdcost;
    }
    public function ga_exp_conana_usd_quantity (Request $request){
        $clauses = [];
       $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();

        $fyear       = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;

        $data = [];
        $result = ExpChartData::select('consinee','top_quantity')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',27)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;


        // $expanausdquantity = ExporterBill::select('consinee',DB::raw('ROUND(SUM(quantity), 2) AS top_quantity'))
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_cals, function ($query) {
        //             $rows = $this->da_cals;
        //             $query->where(function ($query) use ($rows) {
        //                 $cnt = 0;
        //                 foreach ($rows as $row) {
        //                     if ($cnt) {
        //                         $query->orWhere([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     } else {
        //                         $query->where([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     }
        //                     $cnt = $cnt + 1;
        //                 }
        //             });
        //             return $query;
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_hscodes, function ($query) {
        //             $rows = $this->da_hscodes;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_chapters, function ($query) {
        //             $rows = $this->da_chapters;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('chapter', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_ports, function ($query) {
        //             $rows = $this->da_ports;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('indian_port', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when($fyear, function ($query, $fyear) {
        //         return $query->where('year', '=', $fyear);
        //     })
        //     ->when($fmonth, function ($query, $fmonth) {
        //         return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
        //     })
        //     ->when($fproduct, function ($query, $fproduct) {
        //         return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
        //     })
        //     ->when($fhscode, function ($query, $fhscode) {
        //         $fhscode = explode(',', $fhscode);
        //         $fhscode = array_map('trim',$fhscode);
        //         return $query->where(function ($query) use ($fhscode) {
        //             for ($i = 0; $i < count($fhscode); $i++) {
        //                 $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
        //             }
        //         });
        //     })
        //     ->when($fchapter, function ($query, $fchapter) {
        //         $fchapter = explode(',', $fchapter);
        //         $fchapter = array_map('trim',$fchapter);
        //         return $query->where(function ($query) use ($fchapter) {
        //             for ($i = 0; $i < count($fchapter); $i++) {
        //                 $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
        //             }
        //         });
        //     })
        //     ->when($fcountry, function ($query, $fcountry) {
        //         return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
        //     })
        //     ->when($fport, function ($query, $fport) {
        //         return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
        //     })
        //     ->when($funit, function ($query, $funit) {
        //         return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
        //     })
        //     ->when($fimpexpname, function ($query, $fimpexpname) {
        //         return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
        //     })
        //     ->groupBy('consinee')
        //     ->orderBy(DB::raw('ROUND(SUM(quantity), 2)'),'DESC')
        //     ->paginate(15);
        // return $expanausdquantity;
    }

    public function ga_marketshare_cost_usd_port (Request $request){
        $clauses = [];

        $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();
        $fyear       = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;

        $result = ExpChartData::select('labeltitle','labelvalue')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',4)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;


        // $marketshare_cost_usd_country2 = ExporterBill::select(DB::raw('indian_port AS labeltitle'),DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS labelvalue'))
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_cals, function ($query) {
        //             $rows = $this->da_cals;
        //             $query->where(function ($query) use ($rows) {
        //                 $cnt = 0;
        //                 foreach ($rows as $row) {
        //                     if ($cnt) {
        //                         $query->orWhere([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     } else {
        //                         $query->where([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     }
        //                     $cnt = $cnt + 1;
        //                 }
        //             });
        //             return $query;
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_hscodes, function ($query) {
        //             $rows = $this->da_hscodes;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_chapters, function ($query) {
        //             $rows = $this->da_chapters;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('chapter', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_ports, function ($query) {
        //             $rows = $this->da_ports;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('indian_port', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when($fyear, function ($query, $fyear) {
        //         return $query->where('year', '=', $fyear);
        //     })
        //     ->when($fmonth, function ($query, $fmonth) {
        //         return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
        //     })
        //     ->when($fproduct, function ($query, $fproduct) {
        //         return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
        //     })
        //     ->when($fhscode, function ($query, $fhscode) {
        //         $fhscode = explode(',', $fhscode);
        //         $fhscode = array_map('trim',$fhscode);
        //         return $query->where(function ($query) use ($fhscode) {
        //             for ($i = 0; $i < count($fhscode); $i++) {
        //                 $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
        //             }
        //         });
        //     })
        //     ->when($fchapter, function ($query, $fchapter) {
        //         $fchapter = explode(',', $fchapter);
        //         $fchapter = array_map('trim',$fchapter);
        //         return $query->where(function ($query) use ($fchapter) {
        //             for ($i = 0; $i < count($fchapter); $i++) {
        //                 $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
        //             }
        //         });
        //     })
        //     ->when($fcountry, function ($query, $fcountry) {
        //         return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
        //     })
        //     ->when($fport, function ($query, $fport) {
        //         return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
        //     })
        //     ->when($funit, function ($query, $funit) {
        //         return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
        //     })
        //     ->when($fimpexpname, function ($query, $fimpexpname) {
        //         return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
        //     })
        //     ->groupBy('indian_port')
        //     ->orderBy(DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2)'),'DESC')
        //     ->paginate(15);
        // return $marketshare_cost_usd_country2;
    }
    public function ga_marketshare_cost_qua_port (Request $request){
        $clauses = [];
        $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();
        $fyear       = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;

        $result = ExpChartData::select('labeltitle','labelvalue')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',5)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;


        // $marketshare_cost_qua_port2 = ExporterBill::select(DB::raw('indian_port AS labeltitle'),DB::raw('ROUND(SUM(quantity), 2) AS labelvalue'))
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_cals, function ($query) {
        //             $rows = $this->da_cals;
        //             $query->where(function ($query) use ($rows) {
        //                 $cnt = 0;
        //                 foreach ($rows as $row) {
        //                     if ($cnt) {
        //                         $query->orWhere([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     } else {
        //                         $query->where([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     }
        //                     $cnt = $cnt + 1;
        //                 }
        //             });
        //             return $query;
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_hscodes, function ($query) {
        //             $rows = $this->da_hscodes;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_chapters, function ($query) {
        //             $rows = $this->da_chapters;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('chapter', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_ports, function ($query) {
        //             $rows = $this->da_ports;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('indian_port', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when($fyear, function ($query, $fyear) {
        //         return $query->where('year', '=', $fyear);
        //     })
        //     ->when($fmonth, function ($query, $fmonth) {
        //         return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
        //     })
        //     ->when($fproduct, function ($query, $fproduct) {
        //         return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
        //     })
        //     ->when($fhscode, function ($query, $fhscode) {
        //         $fhscode = explode(',', $fhscode);
        //         $fhscode = array_map('trim',$fhscode);
        //         return $query->where(function ($query) use ($fhscode) {
        //             for ($i = 0; $i < count($fhscode); $i++) {
        //                 $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
        //             }
        //         });
        //     })
        //     ->when($fchapter, function ($query, $fchapter) {
        //         $fchapter = explode(',', $fchapter);
        //         $fchapter = array_map('trim',$fchapter);
        //         return $query->where(function ($query) use ($fchapter) {
        //             for ($i = 0; $i < count($fchapter); $i++) {
        //                 $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
        //             }
        //         });
        //     })
        //     ->when($fcountry, function ($query, $fcountry) {
        //         return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
        //     })
        //     ->when($fport, function ($query, $fport) {
        //         return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
        //     })
        //     ->when($funit, function ($query, $funit) {
        //         return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
        //     })
        //     ->when($fimpexpname, function ($query, $fimpexpname) {
        //         return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
        //     })
        //     ->groupBy('indian_port')
        //     ->orderBy(DB::raw('ROUND(SUM(quantity), 2)'),'DESC')
        //     ->paginate(15);
        // return $marketshare_cost_qua_port2;
    }
    public function ga_marketshare_cost_qua_country (Request $request){
        $clauses = [];
        $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();
        $fyear    = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;


        $result = ExpChartData::select('labeltitle','labelvalue')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',6)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;


        // $marketshare_cost_qua_country2 = ExporterBill::select(DB::raw('foreign_country AS labeltitle'),DB::raw('ROUND(SUM(quantity), 2) AS labelvalue'))
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_cals, function ($query) {
        //             $rows = $this->da_cals;
        //             $query->where(function ($query) use ($rows) {
        //                 $cnt = 0;
        //                 foreach ($rows as $row) {
        //                     if ($cnt) {
        //                         $query->orWhere([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     } else {
        //                         $query->where([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     }
        //                     $cnt = $cnt + 1;
        //                 }
        //             });
        //             return $query;
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_hscodes, function ($query) {
        //             $rows = $this->da_hscodes;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_chapters, function ($query) {
        //             $rows = $this->da_chapters;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('chapter', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_ports, function ($query) {
        //             $rows = $this->da_ports;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('indian_port', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when($fyear, function ($query, $fyear) {
        //         return $query->where('year', '=', $fyear);
        //     })
        //     ->when($fmonth, function ($query, $fmonth) {
        //         return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
        //     })
        //     ->when($fproduct, function ($query, $fproduct) {
        //         return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
        //     })
        //     ->when($fhscode, function ($query, $fhscode) {
        //         $fhscode = explode(',', $fhscode);
        //         $fhscode = array_map('trim',$fhscode);
        //         return $query->where(function ($query) use ($fhscode) {
        //             for ($i = 0; $i < count($fhscode); $i++) {
        //                 $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
        //             }
        //         });
        //     })
        //     ->when($fchapter, function ($query, $fchapter) {
        //         $fchapter = explode(',', $fchapter);
        //         $fchapter = array_map('trim',$fchapter);
        //         return $query->where(function ($query) use ($fchapter) {
        //             for ($i = 0; $i < count($fchapter); $i++) {
        //                 $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
        //             }
        //         });
        //     })
        //     ->when($fcountry, function ($query, $fcountry) {
        //         return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
        //     })
        //     ->when($fport, function ($query, $fport) {
        //         return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
        //     })
        //     ->when($funit, function ($query, $funit) {
        //         return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
        //     })
        //     ->when($fimpexpname, function ($query, $fimpexpname) {
        //         return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
        //     })
        //     ->groupBy('foreign_country')
        //     ->orderBy(DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2)'),'DESC')
        //     ->paginate(15);
        // return $marketshare_cost_qua_country2;
    }
    public function ga_marketshare_cost_usd_country (Request $request){
        $clauses = [];

        $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();
        $fyear       = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;

        $result = ExpChartData::select('labeltitle','labelvalue')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',7)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;


        // $marketshare_cost_usd_country2 = ExporterBill::select(DB::raw('foreign_country AS labeltitle'),DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS labelvalue'))
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_cals, function ($query) {
        //             $rows = $this->da_cals;
        //             $query->where(function ($query) use ($rows) {
        //                 $cnt = 0;
        //                 foreach ($rows as $row) {
        //                     if ($cnt) {
        //                         $query->orWhere([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     } else {
        //                         $query->where([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     }
        //                     $cnt = $cnt + 1;
        //                 }
        //             });
        //             return $query;
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_hscodes, function ($query) {
        //             $rows = $this->da_hscodes;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_chapters, function ($query) {
        //             $rows = $this->da_chapters;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('chapter', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_ports, function ($query) {
        //             $rows = $this->da_ports;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('indian_port', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when($fyear, function ($query, $fyear) {
        //         return $query->where('year', '=', $fyear);
        //     })
        //     ->when($fmonth, function ($query, $fmonth) {
        //         return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
        //     })
        //     ->when($fproduct, function ($query, $fproduct) {
        //         return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
        //     })
        //     ->when($fhscode, function ($query, $fhscode) {
        //         $fhscode = explode(',', $fhscode);
        //         $fhscode = array_map('trim',$fhscode);
        //         return $query->where(function ($query) use ($fhscode) {
        //             for ($i = 0; $i < count($fhscode); $i++) {
        //                 $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
        //             }
        //         });
        //     })
        //     ->when($fchapter, function ($query, $fchapter) {
        //         $fchapter = explode(',', $fchapter);
        //         $fchapter = array_map('trim',$fchapter);
        //         return $query->where(function ($query) use ($fchapter) {
        //             for ($i = 0; $i < count($fchapter); $i++) {
        //                 $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
        //             }
        //         });
        //     })
        //     ->when($fcountry, function ($query, $fcountry) {
        //         return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
        //     })
        //     ->when($fport, function ($query, $fport) {
        //         return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
        //     })
        //     ->when($funit, function ($query, $funit) {
        //         return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
        //     })
        //     ->when($fimpexpname, function ($query, $fimpexpname) {
        //         return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
        //     })
        //     ->groupBy('exporter')
        //     ->orderBy(DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2)'),'DESC')
        //     ->paginate(15);
        // return $marketshare_cost_usd_country2;
    }

    public function ga_priceana_usd_country (Request $request){
        $clauses = [];
        $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();

        $fyear       = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;

        $result = ExpChartData::select('week_start','week_end','labeltitle','labelvalue')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',8)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;


        // $priceana_usd_country = ExporterBill::select(DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(shipping_bill_date, 0), \' \', \'Sunday\'), \'%X%V %W\') AS week_start')
        //     , DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(shipping_bill_date, 0), \' \', \'Saturday\'), \'%X%V %W\') AS week_end')
        //     , DB::raw('foreign_country AS labeltitle')
        //     , DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS labelvalue'))
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_cals, function ($query) {
        //             $rows = $this->da_cals;
        //             $query->where(function ($query) use ($rows) {
        //                 $cnt = 0;
        //                 foreach ($rows as $row) {
        //                     if ($cnt) {
        //                         $query->orWhere([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     } else {
        //                         $query->where([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     }
        //                     $cnt = $cnt + 1;
        //                 }
        //             });
        //             return $query;
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_hscodes, function ($query) {
        //             $rows = $this->da_hscodes;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_chapters, function ($query) {
        //             $rows = $this->da_chapters;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('chapter', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_ports, function ($query) {
        //             $rows = $this->da_ports;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('indian_port', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when($fyear, function ($query, $fyear) {
        //         return $query->where('year', '=', $fyear);
        //     })
        //     ->when($fmonth, function ($query, $fmonth) {
        //         return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
        //     })
        //     ->when($fproduct, function ($query, $fproduct) {
        //         return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
        //     })
        //     ->when($fhscode, function ($query, $fhscode) {
        //         $fhscode = explode(',', $fhscode);
        //         $fhscode = array_map('trim',$fhscode);
        //         return $query->where(function ($query) use ($fhscode) {
        //             for ($i = 0; $i < count($fhscode); $i++) {
        //                 $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
        //             }
        //         });
        //     })
        //     ->when($fchapter, function ($query, $fchapter) {
        //         $fchapter = explode(',', $fchapter);
        //         $fchapter = array_map('trim',$fchapter);
        //         return $query->where(function ($query) use ($fchapter) {
        //             for ($i = 0; $i < count($fchapter); $i++) {
        //                 $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
        //             }
        //         });
        //     })
        //     ->when($fcountry, function ($query, $fcountry) {
        //         return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
        //     })
        //     ->when($fport, function ($query, $fport) {
        //         return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
        //     })
        //     ->when($funit, function ($query, $funit) {
        //         return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
        //     })
        //     ->when($fimpexpname, function ($query, $fimpexpname) {
        //         return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
        //     })
        //     ->groupBy(DB::raw('YEARWEEK(shipping_bill_date, 0)'))
        //     ->orderBy(DB::raw('foreign_country'))
        //     ->orderBy(DB::raw('YEARWEEK(shipping_bill_date, 0)'),'DESC')
        //     ->paginate(100);
        // return $priceana_usd_country;
    }
    public function ga_priceana_usd_port (Request $request){
        $clauses = [];
        $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();
        $fyear    = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;

        $result = ExpChartData::select('week_start','week_end','labeltitle','labelvalue')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',9)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;

        // $priceana_usd_port = ExporterBill::select(DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(shipping_bill_date, 0), \' \', \'Sunday\'), \'%X%V %W\') AS week_start')
        //     , DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(shipping_bill_date, 0), \' \', \'Saturday\'), \'%X%V %W\') AS week_end')
        //     , DB::raw('indian_port AS labeltitle')
        //     , DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS labelvalue'))
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_cals, function ($query) {
        //             $rows = $this->da_cals;
        //             $query->where(function ($query) use ($rows) {
        //                 $cnt = 0;
        //                 foreach ($rows as $row) {
        //                     if ($cnt) {
        //                         $query->orWhere([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     } else {
        //                         $query->where([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     }
        //                     $cnt = $cnt + 1;
        //                 }
        //             });
        //             return $query;
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_hscodes, function ($query) {
        //             $rows = $this->da_hscodes;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_chapters, function ($query) {
        //             $rows = $this->da_chapters;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('chapter', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_ports, function ($query) {
        //             $rows = $this->da_ports;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('indian_port', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when($fyear, function ($query, $fyear) {
        //         return $query->where('year', '=', $fyear);
        //     })
        //     ->when($fmonth, function ($query, $fmonth) {
        //         return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
        //     })
        //     ->when($fproduct, function ($query, $fproduct) {
        //         return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
        //     })
        //     ->when($fhscode, function ($query, $fhscode) {
        //         $fhscode = explode(',', $fhscode);
        //         $fhscode = array_map('trim',$fhscode);
        //         return $query->where(function ($query) use ($fhscode) {
        //             for ($i = 0; $i < count($fhscode); $i++) {
        //                 $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
        //             }
        //         });
        //     })
        //     ->when($fchapter, function ($query, $fchapter) {
        //         $fchapter = explode(',', $fchapter);
        //         $fchapter = array_map('trim',$fchapter);
        //         return $query->where(function ($query) use ($fchapter) {
        //             for ($i = 0; $i < count($fchapter); $i++) {
        //                 $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
        //             }
        //         });
        //     })
        //     ->when($fcountry, function ($query, $fcountry) {
        //         return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
        //     })
        //     ->when($fport, function ($query, $fport) {
        //         return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
        //     })
        //     ->when($funit, function ($query, $funit) {
        //         return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
        //     })
        //     ->when($fimpexpname, function ($query, $fimpexpname) {
        //         return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
        //     })
        //     ->groupBy(DB::raw('YEARWEEK(shipping_bill_date, 0)'))
        //     ->orderBy(DB::raw('indian_port'))
        //     ->orderBy(DB::raw('YEARWEEK(shipping_bill_date, 0)'),'DESC')
        //     ->paginate(100);
        // return $priceana_usd_port;
    }
    public function ga_priceana_usd_exporter (Request $request){
        $clauses = [];

        $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();
        $fyear    = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;

        $data = [];
        $result = ExpChartData::select('week_start','week_end','labeltitle','labelvalue')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',10)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;


        // $priceana_usd_exporter = ExporterBill::select(DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(shipping_bill_date, 0), \' \', \'Sunday\'), \'%X%V %W\') AS week_start')
        //     , DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(shipping_bill_date, 0), \' \', \'Saturday\'), \'%X%V %W\') AS week_end')
        //     , DB::raw('exporter AS labeltitle')
        //     , DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS labelvalue'))
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_cals, function ($query) {
        //             $rows = $this->da_cals;
        //             $query->where(function ($query) use ($rows) {
        //                 $cnt = 0;
        //                 foreach ($rows as $row) {
        //                     if ($cnt) {
        //                         $query->orWhere([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     } else {
        //                         $query->where([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     }
        //                     $cnt = $cnt + 1;
        //                 }
        //             });
        //             return $query;
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_hscodes, function ($query) {
        //             $rows = $this->da_hscodes;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_chapters, function ($query) {
        //             $rows = $this->da_chapters;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('chapter', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_ports, function ($query) {
        //             $rows = $this->da_ports;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('indian_port', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when($fyear, function ($query, $fyear) {
        //         return $query->where('year', '=', $fyear);
        //     })
        //     ->when($fmonth, function ($query, $fmonth) {
        //         return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
        //     })
        //     ->when($fproduct, function ($query, $fproduct) {
        //         return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
        //     })
        //     ->when($fhscode, function ($query, $fhscode) {
        //         $fhscode = explode(',', $fhscode);
        //         $fhscode = array_map('trim',$fhscode);
        //         return $query->where(function ($query) use ($fhscode) {
        //             for ($i = 0; $i < count($fhscode); $i++) {
        //                 $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
        //             }
        //         });
        //     })
        //     ->when($fchapter, function ($query, $fchapter) {
        //         $fchapter = explode(',', $fchapter);
        //         $fchapter = array_map('trim',$fchapter);
        //         return $query->where(function ($query) use ($fchapter) {
        //             for ($i = 0; $i < count($fchapter); $i++) {
        //                 $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
        //             }
        //         });
        //     })
        //     ->when($fcountry, function ($query, $fcountry) {
        //         return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
        //     })
        //     ->when($fport, function ($query, $fport) {
        //         return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
        //     })
        //     ->when($funit, function ($query, $funit) {
        //         return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
        //     })
        //     ->when($fimpexpname, function ($query, $fimpexpname) {
        //         return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
        //     })
        //     ->groupBy(DB::raw('YEARWEEK(shipping_bill_date, 0)'))
        //     ->orderBy(DB::raw('exporter'))
        //     ->orderBy(DB::raw('YEARWEEK(shipping_bill_date, 0)'),'DESC')
        //     ->paginate(100);
        // return $priceana_usd_exporter;
    }

    public function ga_comparison_usd_exporter (Request $request){
        $clauses = [];

        $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();
        $fyear    = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;
        $data = [];
        $result = ExpChartData::select('week_start','week_end','labeltitle','labelvalue')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',11)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;


        // $priceana_usd_exporter = ExporterBill::select(DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(shipping_bill_date, 0), \' \', \'Sunday\'), \'%X%V %W\') AS week_start')
        //     , DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(shipping_bill_date, 0), \' \', \'Saturday\'), \'%X%V %W\') AS week_end')
        //     , DB::raw('exporter AS labeltitle')
        //     , DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS labelvalue'))
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_cals, function ($query) {
        //             $rows = $this->da_cals;
        //             $query->where(function ($query) use ($rows) {
        //                 $cnt = 0;
        //                 foreach ($rows as $row) {
        //                     if ($cnt) {
        //                         $query->orWhere([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     } else {
        //                         $query->where([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     }
        //                     $cnt = $cnt + 1;
        //                 }
        //             });
        //             return $query;
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_hscodes, function ($query) {
        //             $rows = $this->da_hscodes;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_chapters, function ($query) {
        //             $rows = $this->da_chapters;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('chapter', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_ports, function ($query) {
        //             $rows = $this->da_ports;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('indian_port', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when($fyear, function ($query, $fyear) {
        //         return $query->where('year', '=', $fyear);
        //     })
        //     ->when($fmonth, function ($query, $fmonth) {
        //         return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
        //     })
        //     ->when($fproduct, function ($query, $fproduct) {
        //         return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
        //     })
        //     ->when($fhscode, function ($query, $fhscode) {
        //         $fhscode = explode(',', $fhscode);
        //         $fhscode = array_map('trim',$fhscode);
        //         return $query->where(function ($query) use ($fhscode) {
        //             for ($i = 0; $i < count($fhscode); $i++) {
        //                 $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
        //             }
        //         });
        //     })
        //     ->when($fchapter, function ($query, $fchapter) {
        //         $fchapter = explode(',', $fchapter);
        //         $fchapter = array_map('trim',$fchapter);
        //         return $query->where(function ($query) use ($fchapter) {
        //             for ($i = 0; $i < count($fchapter); $i++) {
        //                 $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
        //             }
        //         });
        //     })
        //     ->when($fcountry, function ($query, $fcountry) {
        //         return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
        //     })
        //     ->when($fport, function ($query, $fport) {
        //         return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
        //     })
        //     ->when($funit, function ($query, $funit) {
        //         return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
        //     })
        //     ->when($fimpexpname, function ($query, $fimpexpname) {
        //         return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
        //     })
        //     ->groupBy(DB::raw('YEARWEEK(shipping_bill_date, 0)'))
        //     ->orderBy(DB::raw('exporter'))
        //     ->orderBy(DB::raw('YEARWEEK(shipping_bill_date, 0)'),'DESC')
        //     ->paginate(100);
        // return $priceana_usd_exporter;
    }
    public function ga_comparison_usd_country (Request $request){
        $clauses = [];
        $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();
        $fyear       = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;

        $data = [];
        $result = ExpChartData::select('week_start','week_end','labeltitle','labelvalue')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',12)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;

        // $priceana_usd_country = ExporterBill::select(DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(shipping_bill_date, 0), \' \', \'Sunday\'), \'%X%V %W\') AS week_start')
        //     , DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(shipping_bill_date, 0), \' \', \'Saturday\'), \'%X%V %W\') AS week_end')
        //     , DB::raw('foreign_country AS labeltitle')
        //     , DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS labelvalue'))
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_cals, function ($query) {
        //             $rows = $this->da_cals;
        //             $query->where(function ($query) use ($rows) {
        //                 $cnt = 0;
        //                 foreach ($rows as $row) {
        //                     if ($cnt) {
        //                         $query->orWhere([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     } else {
        //                         $query->where([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     }
        //                     $cnt = $cnt + 1;
        //                 }
        //             });
        //             return $query;
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_hscodes, function ($query) {
        //             $rows = $this->da_hscodes;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_chapters, function ($query) {
        //             $rows = $this->da_chapters;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('chapter', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_ports, function ($query) {
        //             $rows = $this->da_ports;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('indian_port', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when($fyear, function ($query, $fyear) {
        //         return $query->where('year', '=', $fyear);
        //     })
        //     ->when($fmonth, function ($query, $fmonth) {
        //         return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
        //     })
        //     ->when($fproduct, function ($query, $fproduct) {
        //         return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
        //     })
        //     ->when($fhscode, function ($query, $fhscode) {
        //         $fhscode = explode(',', $fhscode);
        //         $fhscode = array_map('trim',$fhscode);
        //         return $query->where(function ($query) use ($fhscode) {
        //             for ($i = 0; $i < count($fhscode); $i++) {
        //                 $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
        //             }
        //         });
        //     })
        //     ->when($fchapter, function ($query, $fchapter) {
        //         $fchapter = explode(',', $fchapter);
        //         $fchapter = array_map('trim',$fchapter);
        //         return $query->where(function ($query) use ($fchapter) {
        //             for ($i = 0; $i < count($fchapter); $i++) {
        //                 $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
        //             }
        //         });
        //     })
        //     ->when($fcountry, function ($query, $fcountry) {
        //         return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
        //     })
        //     ->when($fport, function ($query, $fport) {
        //         return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
        //     })
        //     ->when($funit, function ($query, $funit) {
        //         return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
        //     })
        //     ->when($fimpexpname, function ($query, $fimpexpname) {
        //         return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
        //     })
        //     ->groupBy(DB::raw('YEARWEEK(shipping_bill_date, 0)'))
        //     ->orderBy(DB::raw('foreign_country'))
        //     ->orderBy(DB::raw('YEARWEEK(shipping_bill_date, 0)'),'DESC')
        //     ->paginate(100);
        // return $priceana_usd_country;
    }
    public function ga_comparison_usd_port (Request $request){
        $clauses = [];
        $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();
        $fyear    = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;

        $data = [];
        $result = ExpChartData::select('week_start','week_end','labeltitle','labelvalue')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',13)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;


        // $priceana_usd_port = ExporterBill::select(DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(shipping_bill_date, 0), \' \', \'Sunday\'), \'%X%V %W\') AS week_start')
        //     , DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(shipping_bill_date, 0), \' \', \'Saturday\'), \'%X%V %W\') AS week_end')
        //     , DB::raw('indian_port AS labeltitle')
        //     , DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS labelvalue'))
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_cals, function ($query) {
        //             $rows = $this->da_cals;
        //             $query->where(function ($query) use ($rows) {
        //                 $cnt = 0;
        //                 foreach ($rows as $row) {
        //                     if ($cnt) {
        //                         $query->orWhere([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     } else {
        //                         $query->where([
        //                             [DB::raw('YEAR(shipping_bill_date)'), '=', date('Y', strtotime($row))],
        //                             [DB::raw('MONTH(shipping_bill_date)'), '=', (int)date('m', strtotime($row))],
        //                         ]);
        //                     }
        //                     $cnt = $cnt + 1;
        //                 }
        //             });
        //             return $query;
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_hscodes, function ($query) {
        //             $rows = $this->da_hscodes;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_chapters, function ($query) {
        //             $rows = $this->da_chapters;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('chapter', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when(Auth::user()->hasRole('User'), function ($query) {
        //         $query->when($this->da_ports, function ($query) {
        //             $rows = $this->da_ports;
        //             return $query->where(function ($query) use ($rows) {
        //                 for ($i = 0; $i < count($rows); $i++) {
        //                     $query->orwhere('indian_port', '=', $rows[$i]);
        //                 }
        //             });
        //         });
        //         return $query;
        //     })
        //     ->when($fyear, function ($query, $fyear) {
        //         return $query->where('year', '=', $fyear);
        //     })
        //     ->when($fmonth, function ($query, $fmonth) {
        //         return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
        //     })
        //     ->when($fproduct, function ($query, $fproduct) {
        //         return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
        //     })
        //     ->when($fhscode, function ($query, $fhscode) {
        //         $fhscode = explode(',', $fhscode);
        //         $fhscode = array_map('trim',$fhscode);
        //         return $query->where(function ($query) use ($fhscode) {
        //             for ($i = 0; $i < count($fhscode); $i++) {
        //                 $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
        //             }
        //         });
        //     })
        //     ->when($fchapter, function ($query, $fchapter) {
        //         $fchapter = explode(',', $fchapter);
        //         $fchapter = array_map('trim',$fchapter);
        //         return $query->where(function ($query) use ($fchapter) {
        //             for ($i = 0; $i < count($fchapter); $i++) {
        //                 $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
        //             }
        //         });
        //     })
        //     ->when($fcountry, function ($query, $fcountry) {
        //         return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
        //     })
        //     ->when($fport, function ($query, $fport) {
        //         return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
        //     })
        //     ->when($funit, function ($query, $funit) {
        //         return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
        //     })
        //     ->when($fimpexpname, function ($query, $fimpexpname) {
        //         return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
        //     })
        //     ->groupBy(DB::raw('YEARWEEK(shipping_bill_date, 0)'))
        //     ->orderBy(DB::raw('indian_port'))
        //     ->orderBy(DB::raw('YEARWEEK(shipping_bill_date, 0)'),'DESC')
        //     ->paginate(100);
        // return $priceana_usd_port;
    }

    public function ga_gsum_8digit (Request $request){
        $clauses = [];
        $fyear       = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:null;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;

       
        
        $gsum_8digit = ExporterBill::select(DB::raw('LEFT(hs_code, 8) AS hs_code')
            , DB::raw('ROUND(AVG(unit_value_in_inr),2) AS avg_unit_price')
            , DB::raw('ROUND(SUM(quantity),2) AS quantity_sum')
            , DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS value_sum_usd')
            , DB::raw('COUNT(*) AS record_count'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_hscodes, function ($query) {
                    $rows = $this->da_hscodes;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_chapters, function ($query) {
                    $rows = $this->da_chapters;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('chapter', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_ports, function ($query) {
                    $rows = $this->da_ports;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('indian_port', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where('year', '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
            })
            ->when($fhscode, function ($query, $fhscode) {
                $fhscode = explode(',', $fhscode);
                $fhscode = array_map('trim',$fhscode);
                return $query->where(function ($query) use ($fhscode) {
                    for ($i = 0; $i < count($fhscode); $i++) {
                        $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
                    }
                });
            })
            ->when($fchapter, function ($query, $fchapter) {
                $fchapter = explode(',', $fchapter);
                $fchapter = array_map('trim',$fchapter);
                return $query->where(function ($query) use ($fchapter) {
                    for ($i = 0; $i < count($fchapter); $i++) {
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy(DB::raw('LEFT(hs_code, 8)'))
        ;
        return datatables()->of($gsum_8digit)->make(true);
    }
    public function ga_gsum_2digit (Request $request){
        $clauses = [];
        $fyear       = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:null;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;

        $gsum_2digit = ExporterBill::select(DB::raw('LEFT(hs_code, 2) AS hs_code')
            , DB::raw('ROUND(AVG(unit_value_in_inr),2) AS avg_unit_price')
            , DB::raw('ROUND(SUM(quantity),2) AS quantity_sum')
            , DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS value_sum_usd')
            , DB::raw('COUNT(*) AS record_count'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_hscodes, function ($query) {
                    $rows = $this->da_hscodes;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_chapters, function ($query) {
                    $rows = $this->da_chapters;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('chapter', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_ports, function ($query) {
                    $rows = $this->da_ports;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('indian_port', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where('year', '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
            })
            ->when($fhscode, function ($query, $fhscode) {
                $fhscode = explode(',', $fhscode);
                $fhscode = array_map('trim',$fhscode);
                return $query->where(function ($query) use ($fhscode) {
                    for ($i = 0; $i < count($fhscode); $i++) {
                        $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
                    }
                });
            })
            ->when($fchapter, function ($query, $fchapter) {
                $fchapter = explode(',', $fchapter);
                $fchapter = array_map('trim',$fchapter);
                return $query->where(function ($query) use ($fchapter) {
                    for ($i = 0; $i < count($fchapter); $i++) {
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy(DB::raw('LEFT(hs_code, 2)'))

        ;
        return datatables()->of($gsum_2digit)->make(true);
    }
    public function ga_gsum_4digit (Request $request){
        $clauses = [];
        $fyear       = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:null;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;

        $gsum_4digit = ExporterBill::select(DB::raw('LEFT(hs_code, 4) AS hs_code')
            , DB::raw('ROUND(AVG(unit_value_in_inr),2) AS avg_unit_price')
            , DB::raw('ROUND(SUM(quantity),2) AS quantity_sum')
            , DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS value_sum_usd')
            , DB::raw('COUNT(*) AS record_count'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_hscodes, function ($query) {
                    $rows = $this->da_hscodes;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_chapters, function ($query) {
                    $rows = $this->da_chapters;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('chapter', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_ports, function ($query) {
                    $rows = $this->da_ports;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('indian_port', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where('year', '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
            })
            ->when($fhscode, function ($query, $fhscode) {
                $fhscode = explode(',', $fhscode);
                $fhscode = array_map('trim',$fhscode);
                return $query->where(function ($query) use ($fhscode) {
                    for ($i = 0; $i < count($fhscode); $i++) {
                        $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
                    }
                });
            })
            ->when($fchapter, function ($query, $fchapter) {
                $fchapter = explode(',', $fchapter);
                $fchapter = array_map('trim',$fchapter);
                return $query->where(function ($query) use ($fchapter) {
                    for ($i = 0; $i < count($fchapter); $i++) {
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy(DB::raw('LEFT(hs_code, 4)'))

        ;
        return datatables()->of($gsum_4digit)->make(true);
    }
    public function ga_gsum_port (Request $request){
        $clauses = [];
        $fyear       = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:null;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;

        $gsum_8digit = ExporterBill::select(DB::raw('indian_port AS label_title')
            , DB::raw('ROUND(AVG(unit_value_in_inr),2) AS avg_unit_price')
            , DB::raw('ROUND(SUM(quantity),2) AS quantity_sum')
            , DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS value_sum_usd')
            , DB::raw('COUNT(*) AS record_count'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_hscodes, function ($query) {
                    $rows = $this->da_hscodes;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_chapters, function ($query) {
                    $rows = $this->da_chapters;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('chapter', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_ports, function ($query) {
                    $rows = $this->da_ports;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('indian_port', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where('year', '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
            })
            ->when($fhscode, function ($query, $fhscode) {
                $fhscode = explode(',', $fhscode);
                $fhscode = array_map('trim',$fhscode);
                return $query->where(function ($query) use ($fhscode) {
                    for ($i = 0; $i < count($fhscode); $i++) {
                        $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
                    }
                });
            })
            ->when($fchapter, function ($query, $fchapter) {
                $fchapter = explode(',', $fchapter);
                $fchapter = array_map('trim',$fchapter);
                return $query->where(function ($query) use ($fchapter) {
                    for ($i = 0; $i < count($fchapter); $i++) {
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy(DB::raw('indian_port'))
        ;
        return datatables()->of($gsum_8digit)->make(true);
    }
    public function ga_gsum_country (Request $request){
        $clauses = [];
        $fyear       = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:null;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;

        $gsum_2digit = ExporterBill::select(DB::raw('foreign_country AS label_title')
            , DB::raw('ROUND(AVG(unit_value_in_inr),2) AS avg_unit_price')
            , DB::raw('ROUND(SUM(quantity),2) AS quantity_sum')
            , DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS value_sum_usd')
            , DB::raw('COUNT(*) AS record_count'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_hscodes, function ($query) {
                    $rows = $this->da_hscodes;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_chapters, function ($query) {
                    $rows = $this->da_chapters;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('chapter', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_ports, function ($query) {
                    $rows = $this->da_ports;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('indian_port', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where('year', '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
            })
            ->when($fhscode, function ($query, $fhscode) {
                $fhscode = explode(',', $fhscode);
                $fhscode = array_map('trim',$fhscode);
                return $query->where(function ($query) use ($fhscode) {
                    for ($i = 0; $i < count($fhscode); $i++) {
                        $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
                    }
                });
            })
            ->when($fchapter, function ($query, $fchapter) {
                $fchapter = explode(',', $fchapter);
                $fchapter = array_map('trim',$fchapter);
                return $query->where(function ($query) use ($fchapter) {
                    for ($i = 0; $i < count($fchapter); $i++) {
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy(DB::raw('foreign_country'))

        ;
        return datatables()->of($gsum_2digit)->make(true);
    }
    public function ga_gsum_unit (Request $request){
        $clauses = [];
        $fyear       = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:null;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;

        $gsum_4digit = ExporterBill::select(DB::raw('unit_quantity AS label_title')
            , DB::raw('ROUND(AVG(unit_value_in_inr),2) AS avg_unit_price')
            , DB::raw('ROUND(SUM(quantity),2) AS quantity_sum')
            , DB::raw('ROUND(SUM(total_value_in_usd_exchange), 2) AS value_sum_usd')
            , DB::raw('COUNT(*) AS record_count'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_hscodes, function ($query) {
                    $rows = $this->da_hscodes;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_chapters, function ($query) {
                    $rows = $this->da_chapters;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('chapter', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_ports, function ($query) {
                    $rows = $this->da_ports;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('indian_port', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where('year', '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
            })
            ->when($fhscode, function ($query, $fhscode) {
                $fhscode = explode(',', $fhscode);
                $fhscode = array_map('trim',$fhscode);
                return $query->where(function ($query) use ($fhscode) {
                    for ($i = 0; $i < count($fhscode); $i++) {
                        $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
                    }
                });
            })
            ->when($fchapter, function ($query, $fchapter) {
                $fchapter = explode(',', $fchapter);
                $fchapter = array_map('trim',$fchapter);
                return $query->where(function ($query) use ($fchapter) {
                    for ($i = 0; $i < count($fchapter); $i++) {
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy(DB::raw('unit_quantity'))

        ;
        return datatables()->of($gsum_4digit)->make(true);
    }

    public function ga_pc_usd_country_max (Request $request){
        $clauses = [];
        $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();
        $fyear    = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;


        $result = ExpChartData::select('labeltitle','labelvalue')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',14)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;

        $pc_usd_country_max = ExporterBill::select(DB::raw('foreign_country AS labeltitle'), DB::raw('ROUND(MAX(unit_rate_in_usd_exchange), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_hscodes, function ($query) {
                    $rows = $this->da_hscodes;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_chapters, function ($query) {
                    $rows = $this->da_chapters;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('chapter', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_ports, function ($query) {
                    $rows = $this->da_ports;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('indian_port', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where('year', '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
            })
            ->when($fhscode, function ($query, $fhscode) {
                $fhscode = explode(',', $fhscode);
                $fhscode = array_map('trim',$fhscode);
                return $query->where(function ($query) use ($fhscode) {
                    for ($i = 0; $i < count($fhscode); $i++) {
                        $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
                    }
                });
            })
            ->when($fchapter, function ($query, $fchapter) {
                $fchapter = explode(',', $fchapter);
                $fchapter = array_map('trim',$fchapter);
                return $query->where(function ($query) use ($fchapter) {
                    for ($i = 0; $i < count($fchapter); $i++) {
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy(DB::raw('foreign_country'))
            ->orderBy(DB::raw('ROUND(MAX(unit_rate_in_usd_exchange), 2)'), 'DESC')
            ->paginate(15)
        ;
        return $pc_usd_country_max;
    }
    public function ga_pc_qua_country_max (Request $request){
        $clauses = [];
        $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();
        $fyear    = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;

        $result = ExpChartData::select('labeltitle','labelvalue')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',15)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;

        $pc_qua_country_max = ExporterBill::select(DB::raw('CONCAT(foreign_country, \'::\', unit_quantity) AS labeltitle'),DB::raw('ROUND(MAX(quantity), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_hscodes, function ($query) {
                    $rows = $this->da_hscodes;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_chapters, function ($query) {
                    $rows = $this->da_chapters;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('chapter', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_ports, function ($query) {
                    $rows = $this->da_ports;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('indian_port', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where('year', '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
            })
            ->when($fhscode, function ($query, $fhscode) {
                $fhscode = explode(',', $fhscode);
                $fhscode = array_map('trim',$fhscode);
                return $query->where(function ($query) use ($fhscode) {
                    for ($i = 0; $i < count($fhscode); $i++) {
                        $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
                    }
                });
            })
            ->when($fchapter, function ($query, $fchapter) {
                $fchapter = explode(',', $fchapter);
                $fchapter = array_map('trim',$fchapter);
                return $query->where(function ($query) use ($fchapter) {
                    for ($i = 0; $i < count($fchapter); $i++) {
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('unit_quantity')
            ->orderBy(DB::raw('ROUND(MAX(quantity), 2)'),'DESC')
            ->paginate(15);
        return $pc_qua_country_max;
    }
    public function ga_pc_usd_country_min (Request $request){
        $clauses = [];
        $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();
        $fyear    = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;

        $result = ExpChartData::select('labeltitle','labelvalue')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',16)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;


        $pc_usd_country_max = ExporterBill::select(DB::raw('foreign_country AS labeltitle'), DB::raw('ROUND(MIN(unit_rate_in_usd_exchange), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_hscodes, function ($query) {
                    $rows = $this->da_hscodes;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_chapters, function ($query) {
                    $rows = $this->da_chapters;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('chapter', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_ports, function ($query) {
                    $rows = $this->da_ports;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('indian_port', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where('year', '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
            })
            ->when($fhscode, function ($query, $fhscode) {
                $fhscode = explode(',', $fhscode);
                $fhscode = array_map('trim',$fhscode);
                return $query->where(function ($query) use ($fhscode) {
                    for ($i = 0; $i < count($fhscode); $i++) {
                        $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
                    }
                });
            })
            ->when($fchapter, function ($query, $fchapter) {
                $fchapter = explode(',', $fchapter);
                $fchapter = array_map('trim',$fchapter);
                return $query->where(function ($query) use ($fchapter) {
                    for ($i = 0; $i < count($fchapter); $i++) {
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy(DB::raw('foreign_country'))
            ->orderBy(DB::raw('ROUND(MIN(unit_rate_in_usd_exchange), 2)'),'DESC')
            ->paginate(15)
        ;
        return $pc_usd_country_max;
    }
    public function ga_pc_qua_country_min (Request $request){
        $clauses = [];
        $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();
        $fyear    = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;

        $result = ExpChartData::select('labeltitle','labelvalue')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',17)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;


        $pc_qua_country_min = ExporterBill::select(DB::raw('CONCAT(foreign_country, \'::\', unit_quantity) AS labeltitle'),DB::raw('ROUND(MIN(quantity), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_hscodes, function ($query) {
                    $rows = $this->da_hscodes;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_chapters, function ($query) {
                    $rows = $this->da_chapters;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('chapter', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_ports, function ($query) {
                    $rows = $this->da_ports;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('indian_port', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where('year', '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
            })
            ->when($fhscode, function ($query, $fhscode) {
                $fhscode = explode(',', $fhscode);
                $fhscode = array_map('trim',$fhscode);
                return $query->where(function ($query) use ($fhscode) {
                    for ($i = 0; $i < count($fhscode); $i++) {
                        $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
                    }
                });
            })
            ->when($fchapter, function ($query, $fchapter) {
                $fchapter = explode(',', $fchapter);
                $fchapter = array_map('trim',$fchapter);
                return $query->where(function ($query) use ($fchapter) {
                    for ($i = 0; $i < count($fchapter); $i++) {
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('unit_quantity')
            ->orderBy(DB::raw('ROUND(MIN(quantity), 2)'),'DESC')
            ->paginate(15);
        return $pc_qua_country_min;
    }
    public function ga_pc_usd_port_max (Request $request){
        $clauses = [];
        $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();
        $fyear    = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;


        $result = ExpChartData::select('labeltitle','labelvalue')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',18)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;
        
        $pc_usd_country_max = ExporterBill::select(DB::raw('indian_port AS labeltitle'), DB::raw('ROUND(MAX(unit_rate_in_usd_exchange), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_hscodes, function ($query) {
                    $rows = $this->da_hscodes;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_chapters, function ($query) {
                    $rows = $this->da_chapters;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('chapter', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_ports, function ($query) {
                    $rows = $this->da_ports;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('indian_port', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where('year', '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
            })
            ->when($fhscode, function ($query, $fhscode) {
                $fhscode = explode(',', $fhscode);
                $fhscode = array_map('trim',$fhscode);
                return $query->where(function ($query) use ($fhscode) {
                    for ($i = 0; $i < count($fhscode); $i++) {
                        $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
                    }
                });
            })
            ->when($fchapter, function ($query, $fchapter) {
                $fchapter = explode(',', $fchapter);
                $fchapter = array_map('trim',$fchapter);
                return $query->where(function ($query) use ($fchapter) {
                    for ($i = 0; $i < count($fchapter); $i++) {
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy(DB::raw('indian_port'))
            ->orderBy(DB::raw('ROUND(MAX(unit_rate_in_usd_exchange), 2)'), 'DESC')
            ->paginate(15)
        ;
        return $pc_usd_country_max;
    }
    public function ga_pc_qua_port_max (Request $request){
        $clauses = [];
        $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();
        $fyear    = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;

        $result = ExpChartData::select('labeltitle','labelvalue')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',19)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;


        $pc_qua_country_max = ExporterBill::select(DB::raw('CONCAT(indian_port, \'::\', unit_quantity) AS labeltitle'),DB::raw('ROUND(MAX(quantity), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_hscodes, function ($query) {
                    $rows = $this->da_hscodes;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_chapters, function ($query) {
                    $rows = $this->da_chapters;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('chapter', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_ports, function ($query) {
                    $rows = $this->da_ports;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('indian_port', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where('year', '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
            })
            ->when($fhscode, function ($query, $fhscode) {
                $fhscode = explode(',', $fhscode);
                $fhscode = array_map('trim',$fhscode);
                return $query->where(function ($query) use ($fhscode) {
                    for ($i = 0; $i < count($fhscode); $i++) {
                        $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
                    }
                });
            })
            ->when($fchapter, function ($query, $fchapter) {
                $fchapter = explode(',', $fchapter);
                $fchapter = array_map('trim',$fchapter);
                return $query->where(function ($query) use ($fchapter) {
                    for ($i = 0; $i < count($fchapter); $i++) {
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('unit_quantity')
            ->orderBy(DB::raw('ROUND(MAX(quantity), 2)'),'DESC')
            ->paginate(15);
        return $pc_qua_country_max;
    }
    public function ga_pc_usd_port_min (Request $request){
        $clauses = [];
        $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();
        $fyear    = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;

        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;

        $result = ExpChartData::select('labeltitle','labelvalue')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',20)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;


        $pc_usd_country_max = ExporterBill::select(DB::raw('indian_port AS labeltitle'), DB::raw('ROUND(MIN(unit_rate_in_usd_exchange), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_hscodes, function ($query) {
                    $rows = $this->da_hscodes;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_chapters, function ($query) {
                    $rows = $this->da_chapters;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('chapter', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_ports, function ($query) {
                    $rows = $this->da_ports;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('indian_port', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where('year', '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
            })
            ->when($fhscode, function ($query, $fhscode) {
                $fhscode = explode(',', $fhscode);
                $fhscode = array_map('trim',$fhscode);
                return $query->where(function ($query) use ($fhscode) {
                    for ($i = 0; $i < count($fhscode); $i++) {
                        $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
                    }
                });
            })
            ->when($fchapter, function ($query, $fchapter) {
                $fchapter = explode(',', $fchapter);
                $fchapter = array_map('trim',$fchapter);
                return $query->where(function ($query) use ($fchapter) {
                    for ($i = 0; $i < count($fchapter); $i++) {
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy(DB::raw('indian_port'))
            ->orderBy(DB::raw('ROUND(MIN(unit_rate_in_usd_exchange), 2)'),'DESC')
            ->paginate(15)
        ;
        return $pc_usd_country_max;
    }
    public function ga_pc_qua_port_min (Request $request){
        $clauses = [];
        $last_year = ExporterBill::distinct()->orderBy('year','DESC')->pluck('year')->first();
        $fyear    = (isset($request['fyear'])    and $request['fyear']!=''   )?$request['fyear']:$last_year;
        $fmonth      = (isset($request['fmonth'])   and $request['fmonth']!=''  )?$request['fmonth']:null;
        $fproduct    = (isset($request['fproduct']) and $request['fproduct']!='')?$request['fproduct']:null;
        $fhscode     = (isset($request['fhscode'])  and $request['fhscode']!='' )?$request['fhscode']:null;
        $fchapter    = (isset($request['fchapter']) and $request['fchapter']!='')?$request['fchapter']:null;
        $fcountry    = (isset($request['fcountry']) and $request['fcountry']!='')?$request['fcountry']:null;
        $fport       = (isset($request['fport'])    and $request['fport']!=''   )?$request['fport']:null;
        $funit       = (isset($request['funit'])    and $request['funit']!=''   )?$request['funit']:null;
        $fimpexpname = (isset($request['fimpexpname']) and $request['fimpexpname']!='')?$request['fimpexpname']:null;

        $result = ExpChartData::select('labeltitle','labelvalue')->where(function ($query) use ($fyear) {
            if ($fyear) {
                $query->where('year', '=', $fyear);
            }
            })->where('api_id',21)->get();
            
            foreach($result as $item){
                $data['data'] = $result;
            }
        return $data;


        $pc_qua_country_min = ExporterBill::select(DB::raw('CONCAT(indian_port, \'::\', unit_quantity) AS labeltitle'),DB::raw('ROUND(MIN(quantity), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_hscodes, function ($query) {
                    $rows = $this->da_hscodes;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('hs_code', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_chapters, function ($query) {
                    $rows = $this->da_chapters;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('chapter', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_ports, function ($query) {
                    $rows = $this->da_ports;
                    return $query->where(function ($query) use ($rows) {
                        for ($i = 0; $i < count($rows); $i++) {
                            $query->orwhere('indian_port', '=', $rows[$i]);
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where('year', '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(shipping_bill_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_descripition', 'LIKE', '%'.$fproduct.'%');
            })
            ->when($fhscode, function ($query, $fhscode) {
                $fhscode = explode(',', $fhscode);
                $fhscode = array_map('trim',$fhscode);
                return $query->where(function ($query) use ($fhscode) {
                    for ($i = 0; $i < count($fhscode); $i++) {
                        $query->orwhere('hs_code', 'LIKE', $fhscode[$i] . '%');
                    }
                });
            })
            ->when($fchapter, function ($query, $fchapter) {
                $fchapter = explode(',', $fchapter);
                $fchapter = array_map('trim',$fchapter);
                return $query->where(function ($query) use ($fchapter) {
                    for ($i = 0; $i < count($fchapter); $i++) {
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i].'%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('foreign_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('exporter', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('unit_quantity')
            ->orderBy(DB::raw('ROUND(MIN(quantity), 2)'),'DESC')
            ->paginate(15);
        return $pc_qua_country_min;
    }


}
