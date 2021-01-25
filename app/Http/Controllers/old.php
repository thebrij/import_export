<?php

namespace App\Http\Controllers;

//use Illuminate\Support\Facades\Auth;
use App\Models\ImportBillsFile;
use App\Models\ImporterBill;
use App\Models\DataAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Exports\ExportImporterBills;
use App\Imports\ImportImporterBills;

use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use PhpParser\Error;
use PhpParser\Node\Expr\Cast\Object_;
use Response;
use Rap2hpoutre\FastExcel\FastExcel;
use \Carbon\Carbon;

class ImporterBillController extends Controller
{
    public $da_cals;
    public $da_years;
    public $da_months;
    public $da_hscodes;
    public $da_chapters;
    public $da_ports;
    public $ferror;
    public $ferrormsg;

    public function __construct()
    {
        $this->da_cals = [];
        $this->da_hscodes = [];
        $this->da_chapters = [];
        $this->da_ports = [];
        $this->middleware(function ($request, $next) {
            if (isset(Auth::user()->role_id) and Auth::user()->role_id != '') {
                //echo 'login user';
                if (Auth::user()->hasRole('User')) {
                    //echo 'role user';

                    $da_get_cals = DataAccess::where('user_id', Auth::user()->id)->where('right_name', 'selimpcal')->get();
                    if (isset($da_get_cals) and !empty($da_get_cals)) {
                        foreach ($da_get_cals as $da_get_cal) {
                            array_push($this->da_cals, $da_get_cal->right_option);
                        }
                    }
                    $da_get_hscodes = DataAccess::where('user_id', Auth::user()->id)->where('right_name', 'selimphscode')->get();
                    if (isset($da_get_hscodes) and !empty($da_get_hscodes)) {
                        foreach ($da_get_hscodes as $da_get_hscode) {
                            array_push($this->da_hscodes, $da_get_hscode->right_option);
                        }
                    }
                    $da_get_chapters = DataAccess::where('user_id', Auth::user()->id)->where('right_name', 'selimpchapter')->get();
                    if (isset($da_get_chapters) and !empty($da_get_chapters)) {
                        foreach ($da_get_chapters as $da_get_chapter) {
                            array_push($this->da_chapters, $da_get_chapter->right_option);
                        }
                    }
                    $da_get_ports = DataAccess::where('user_id', Auth::user()->id)->where('right_name', 'selimpport')->get();
                    if (isset($da_get_ports) and !empty($da_get_ports)) {
                        foreach ($da_get_ports as $da_get_port) {
                            array_push($this->da_ports, $da_get_port->right_option);
                        }
                    }
                }
            }
            return $next($request);
        });

        //print_r($this->da_years);
    }

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function importBillsFileList()
    {
        $importBillFiles = ImportBillsFile::all();

        return view('importerbills.import_files_index', ['importFiles' => $importBillFiles]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $efilelist[] = 'Select Uploaded Excel';
        $filesInFolder = \File::files(storage_path('app/excel-files/importers'));
        foreach($filesInFolder as $path) {
            $file = pathinfo($path);
            $efilelist[] = $file['basename'] ;
        }

        $db_filenames = ImporterBill::distinct()->get(['file_name']);
        $file_names = [];
        if($db_filenames->count()){
            foreach($db_filenames as $db_filename){
                $file_names[] = $db_filename->file_name;
            }
        }
        return view('importerbills.create',compact('efilelist', 'file_names'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   
    public function store(Request $request)
    {
         


        $start = microtime(true);
        set_time_limit('7200');
        ini_set('post_max_size','1024M');
        ini_set('upload_max_filesize','1024M');
        ini_set('max_input_time', '7200');
        ini_set('max_execution_time', '7200');
        ini_set('memory_limit', '5G');
        // Validation rules somewhere...


        // just upload main file if requred 
        // but don't store it on database because we store sliced temp file details into the database

        // if (isset($request->efilelist) and $request->efilelist != '0'){
        //     $path = 'excel-files/importers/'.$request->efilelist;
        //     $file_name = $request->efilelist;
        // } else {
        //     echo 'IN ELSE';
        //     $validatedData = $request->validate([
        //         'file' => 'required',
        //     ]);
        //     $file_name = $request->file('file')->getClientOriginalName();
        //     $file_name = trim(str_replace(' ', '_', $file_name));
        //     $path = $request->file('file')->storeAs('excel-files/importers/', time(). '_' . $file_name);
        // }
        


        //slice file  and store details on database 
        $temp_file_name = $request->file('file')->getClientOriginalName();
        $outputFile = storage_path('app/excel-files/importers/pending-files/');
        
        $splitSize = 25; // 50k records in a one file
      
        $in = fopen($request->file->getRealPath(), 'r');

        $rows = 0;
        $fileCount = 1;
        $out = null;

        while (!feof($in)) {
            if (($rows % $splitSize) == 0) {
                if ($rows > 0) {
                    fclose($out);
                }
                $fileCount++;
                // for filenames like indiacountry-part-0001.csv, indiacountry-part-0002.csv etc
                $fileCounterDisplay = sprintf("%04d", $fileCount);

                $fileNameDB = "$fileCounterDisplay.csv";
                $fileName = "$outputFile$fileCounterDisplay.csv";
                $filepath   = 'excel-files/importers/pending-files/'.$fileNameDB;
                $out = fopen($fileName, 'w');
                ImportBillsFile::create([
                    'user_id' => Auth::getUser()->id,
                    'filepath' => preg_replace('/\s+/', '/', $filepath),
                    'filename' => $fileNameDB,
                ]);
            }

            $data = fgetcsv($in);

            if ($data)
                fputcsv($out, $data);

            $rows++;

            
        }
        fclose($out);
        


        $this->file_name = $temp_file_name;
        $this->ferror = false;
        if($this->ferror){
            echo $this->ferrormsg;
            return back()->with('error', $this->ferrormsg);
        } else {
            

            return back()->with('status', __('Excel file successfully Imported in the system'));
        }


        // old Code 


        // $start = microtime(true);
        // set_time_limit('7200');
        // ini_set('post_max_size','1024M');
        // ini_set('upload_max_filesize','1024M');
        // ini_set('max_input_time', '7200');
        // ini_set('max_execution_time', '7200');
        // ini_set('memory_limit', '5G');
        // // Validation rules somewhere...
        // if (isset($request->efilelist) and $request->efilelist != '0'){
        //     $path = 'excel-files/importers/'.$request->efilelist;
        //     $file_name = $request->efilelist;
        // } else {
        //     echo 'IN ELSE';
        //     $validatedData = $request->validate([
        //         'file' => 'required',
        //     ]);
        //     $file_name = $request->file('file')->getClientOriginalName();
        //     $file_name = trim(str_replace(' ', '_', $file_name));
        //     $path = $request->file('file')->storeAs('excel-files/importers', time(). '_' . $file_name);
        // }

        // $time_upload_secs = microtime(true) - $start;
        // echo 'upload time '.$time_upload_secs.' upload';
        // ImportBillsFile::create([
        //     'user_id' => Auth::getUser()->id,
        //     'filepath' => $path,
        //     'filename' => $file_name,
        // ]);
        // $this->file_name = $file_name;
        // $this->ferror = false;
        // if($this->ferror){
        //     echo $this->ferrormsg;
        //     return back()->with('error', $this->ferrormsg);
        // } else {
        //     return back()->with('status', __('Excel file successfully Imported in the system'));
        // }


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
            ImporterBill::where('file_name', $request->input('sel_file_to_delete'))->delete();
        }


        return back()->with('status', __('Selected file\'s Data Deleted Successfully' ));
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function export()
    {
        //return Excel::download(new ExportImporterBills, 'importerbills.xlsx');
        return response()->download(storage_path("app/excel-files/ImportExportSampleImporterData.xlsx"));
    }
    public function ajax_importer_export (Request $request){
        if($request->has('rtype')){
            if($request->input('rtype') == 'Importer'){
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

                //print_r($this->da_years);

                $importers = ImporterBill::selectRaw('id, bill_of_entry_no, bill_of_entry_date, importer_id, importer_name, importer_address
                                            , importer_city_state, pin_code, city, state, contact_no, email, supplier, supplier_address
                                            , foreign_port, origin_country, hs_code, chapter, product_discription
                                            , quantity, unit_quantity, unit_value_as_per_invoice, invoice_currency, total_value_in_fc
                                            , unit_rate_in_usd , exchange_rate, total_value_usd_exchange, unit_price_in_inr
                                            , assess_value_in_inr, duty, cha_number , cha_name, invoice_no, item_number, be_type
                                            , a_group, indian_port, cush
                                            , created_by, updated_by, created_at, updated_at')

                    ->when(Auth::user()->hasRole('User'), function ($query) {
                        $query->when($this->da_cals, function ($query) {
                            $rows = $this->da_cals;
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
                                    $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                                    $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                                }
                            });
                        });
                        return $query;
                    })
                    ->when($fyear, function ($query, $fyear) {
                        return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
                    })
                    ->when($fmonth, function ($query, $fmonth) {
                        return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
                    })
                    ->when($fproduct, function ($query, $fproduct) {
                        return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                                $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                            }
                        });
                    })
                    ->when($fcountry, function ($query, $fcountry) {
                        return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
                    })
                    ->when($fport, function ($query, $fport) {
                        return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
                    })
                    ->when($funit, function ($query, $funit) {
                        return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
                    })
                    ->when($fimpexpname, function ($query, $fimpexpname) {
                        return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
                    })
                    ->get()
                ;
                if($importers->count()) {
                    if (Auth::user()->hasRole('Administrator')  == '1' or  Auth::user()->hasRole('Manager')  == '1'
                        or  Auth::user()->hasRole('Supervisor') == '1') {
                        $response = array(
                            'Record Count' => $importers->count(),
                            'status' => 'success',
                            'msg' => 'Request Reveived Data ready to export!',
                        );
                        return (new FastExcel($importers))->download('ImportExportImporter.xlsx');
                    }
                    else if(Auth::user()->hasRole('User') == '1') {
                        $points = DataAccess::where('user_id', Auth::user()->id)->where('right_name', 'selpoints')->first();
                        if ($points->count()) {
                            if ($importers->count() < $points->right_option) {
                                $points->update(['right_option' => ($points->right_option - $importers->count())]);
                                $response = array(
                                    'Record Count' => $importers->count(),
                                    'status' => 'success',
                                    'msg' => 'Request Reveived Data ready to export!',
                                );
                                return (new FastExcel($importers))->download('ImportExportImporter.xlsx');
                            }
                            else {
                                //$errors = [];
                                $errors =  __('Insufficient Download Points');
                                /*$errors = collect($errors['points'])->map(function ($error) {
                                    return (object) $error;
                                });*/
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
            else if($request->input('rtype') == 'Exporter'){
                $response = array(
                    'status' => 'failed',
                    'msg' => 'Request Reveived on Wrong Controller!',
                );
                return back()->withErrors(['record_type' => 'Request Reveived on Wrong Controller!', ]);
            }
            else {
                $response = array(
                    'status' => 'failed',
                    'msg' => 'Record Type Value is Corrupted!',
                );
                return back()->withErrors(['record_type' => 'Record Type Value is Corrupted!', ]);
            }
        }
        else {
            $response = array(
                'status' => 'failed',
                'msg' => 'Plz choose Importer or Exporter from Filters Drop Down!',
            );
            return back()->withErrors(['record_type' => 'Plz choose Importer or Exporter from Filters Drop Down!', ]);
        }
        return back()->withErrors(['record_type' => 'Some Error Occured', ]);
    }


    public function get_ajax_side_bar (Request $request)
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

        $imp_d_hs_code = ImporterBill::select(DB::raw('DISTINCT hs_code AS d_hs_code, count(*) as order_count'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('hs_code')
            ->orderBy(DB::raw('count(*)'),'DESC')
            ->get();

        $keys = array("d_hs_code"=>1, "order_count"=>2);
        $imp_sb_hs_code = array_map(function($a) use($keys){
            return array_intersect_key($a,$keys);
        }, $imp_d_hs_code->toArray());
        $imp_sidebar['sb_hs_codes'] = $imp_sb_hs_code;

        $imp_d_country = ImporterBill::select(DB::raw('DISTINCT origin_country AS d_country,  count(*) as order_count'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('origin_country')
            ->orderBy(DB::raw('count(*)'),'DESC')
            ->get();
        $keys = array("d_country"=>1, "order_count"=>2);
        $imp_sb_country = array_map(function($a) use($keys){
            return array_intersect_key($a,$keys);
        }, $imp_d_country->toArray());
        $imp_sidebar['sb_countries'] = $imp_sb_country;

        $imp_d_port = ImporterBill::select(DB::raw('DISTINCT indian_port AS d_port,  count(*) as order_count'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('indian_port')
            ->orderBy(DB::raw('count(*)'),'DESC')
            ->get();
        $keys = array("d_port"=>1, "order_count"=>2);
        $imp_sb_port = array_map(function($a) use($keys){
            return array_intersect_key($a,$keys);
        }, $imp_d_port->toArray());
        $imp_sidebar['sb_ports'] = $imp_sb_port;

        $imp_d_unit = ImporterBill::select(DB::raw('DISTINCT unit_quantity AS d_unit,  count(*) as order_count'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('unit_quantity')
            ->orderBy(DB::raw('count(*)'),'DESC')
            ->get();
        $keys = array("d_unit"=>1, "order_count"=>2);
        $imp_sb_unit = array_map(function($a) use($keys){
            return array_intersect_key($a,$keys);
        }, $imp_d_unit->toArray());
        $imp_sidebar['sb_units'] = $imp_sb_unit;


        //dd($imp_sidebar);
        return $imp_sidebar;
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

        //print_r($this->da_years);

        $importers = ImporterBill::selectRaw('id, bill_of_entry_no, bill_of_entry_date, importer_id, importer_name, importer_address
                                            , importer_city_state, pin_code, city, state, contact_no, email, supplier, supplier_address
                                            , foreign_port, origin_country, hs_code, chapter, product_discription
                                            , quantity, unit_quantity, unit_value_as_per_invoice, invoice_currency, total_value_in_fc
                                            , unit_rate_in_usd , exchange_rate, total_value_usd_exchange, unit_price_in_inr
                                            , assess_value_in_inr, duty, cha_number , cha_name, invoice_no, item_number, be_type
                                            , a_group, indian_port, cush
                                            , created_by, updated_by, created_at, updated_at')
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })

            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })

        ;
        return datatables()->of($importers)->make(true);
    }

    public function get_ajax_top_usd (Request $request){
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

        $importers_top_usd = ImporterBill::select('importer_name',DB::raw('ROUND(SUM(total_value_usd_exchange), 2) AS top15_usd'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('importer_name')
            ->orderBy(DB::raw('ROUND(SUM(total_value_usd_exchange), 2)'),'DESC')
            ->paginate(15);

        return $importers_top_usd;
    }
    public function get_ajax_top_usd_port (Request $request){
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

        $topusdport = ImporterBill::select(DB::raw('indian_port AS labeltitle'),DB::raw('ROUND(SUM(total_value_usd_exchange), 2)  AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('indian_port')
            ->orderBy(DB::raw('ROUND(SUM(total_value_usd_exchange), 2)'),'DESC')
            ->paginate(15);
        return $topusdport;
    }
    public function get_ajax_top_usd_country (Request $request){
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

        $topusdcountry = ImporterBill::select('origin_country',DB::raw('ROUND(SUM(total_value_usd_exchange), 2) AS top15_usd'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('origin_country')
            ->orderBy(DB::raw('ROUND(SUM(total_value_usd_exchange), 2)'),'DESC')
            ->paginate(15);
        return $topusdcountry;
    }

    public function get_ajax_impana_usd_comp (Request $request){
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

        $importers_usd_comp = ImporterBill::select('importer_name',DB::raw('ROUND(SUM(total_value_usd_exchange), 2) AS top15_usd'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('importer_name')
            ->orderBy(DB::raw('ROUND(SUM(total_value_usd_exchange), 2)'),'DESC')
            ->paginate(15);
        return $importers_usd_comp;
    }
    public function get_ajax_impana_usd_cost (Request $request){
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

        $importerana_usd_port = ImporterBill::select('importer_name',DB::raw('ROUND(SUM(total_value_usd_exchange), 2) AS top15_usd'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('importer_name')
            ->orderBy(DB::raw('ROUND(SUM(total_value_usd_exchange), 2)'),'DESC')
            ->paginate(15);
        return $importerana_usd_port;
    }
    public function get_ajax_impana_usd_quantity (Request $request){
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

        $importerana_usd_quantity = ImporterBill::select('importer_name',DB::raw('ROUND(SUM(quantity), 2) AS top_quantity'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('importer_name')
            ->orderBy(DB::raw('ROUND(SUM(quantity), 2)'),'DESC')
            ->paginate(15);
        return $importerana_usd_quantity;
    }

    public function ga_imp_supana_usd_comp (Request $request){
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

        $importers_usd_comp = ImporterBill::select('supplier',DB::raw('ROUND(SUM(total_value_usd_exchange), 2) AS top15_usd'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('supplier')
            ->orderBy(DB::raw('ROUND(SUM(total_value_usd_exchange), 2)'),'DESC')
            ->paginate(15);
        return $importers_usd_comp;
    }
    public function ga_imp_supana_usd_cost (Request $request){
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

        $importerana_usd_port = ImporterBill::select('supplier',DB::raw('ROUND(SUM(total_value_usd_exchange), 2) AS top15_usd'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('supplier')
            ->orderBy(DB::raw('ROUND(SUM(total_value_usd_exchange), 2)'),'DESC')
            ->paginate(15);
        return $importerana_usd_port;
    }
    public function ga_imp_supana_usd_quantity (Request $request){
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

        $importerana_usd_quantity = ImporterBill::select('supplier',DB::raw('ROUND(SUM(quantity), 2) AS top_quantity'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('supplier')
            ->orderBy(DB::raw('ROUND(SUM(quantity), 2)'),'DESC')
            ->paginate(15);
        return $importerana_usd_quantity;
    }


    public function ga_marketshare_cost_usd_port (Request $request){
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

        $marketshare_cost_usd_port = ImporterBill::select(DB::raw('indian_port AS labeltitle'),DB::raw('ROUND(SUM(total_value_usd_exchange), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('indian_port')
            ->orderBy(DB::raw('ROUND(SUM(total_value_usd_exchange), 2)'),'DESC')
            ->paginate(15);
        return $marketshare_cost_usd_port;
    }
    public function ga_marketshare_cost_qua_port (Request $request){
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

        $marketshare_cost_qua_port = ImporterBill::select(DB::raw('indian_port AS labeltitle'),DB::raw('ROUND(SUM(quantity), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('indian_port')
            ->orderBy(DB::raw('ROUND(SUM(quantity), 2)'),'DESC')
            ->paginate(15);
        return $marketshare_cost_qua_port;
    }
    public function ga_marketshare_cost_qua_country (Request $request){
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

        $marketshare_cost_qua_country = ImporterBill::select(DB::raw('origin_country AS labeltitle'),DB::raw('ROUND(SUM(quantity), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('origin_country')
            ->orderBy(DB::raw('ROUND(SUM(quantity), 2)'),'DESC')
            ->paginate(15);
        return $marketshare_cost_qua_country;
    }
    public function ga_marketshare_cost_usd_country (Request $request){
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

        $marketshare_cost_usd_country = ImporterBill::select(DB::raw('origin_country AS labeltitle'),DB::raw('ROUND(SUM(total_value_usd_exchange), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('origin_country')
            ->orderBy(DB::raw('ROUND(SUM(total_value_usd_exchange), 2)'),'DESC')
            ->paginate(15);
        return $marketshare_cost_usd_country;
    }

    public function ga_priceana_usd_country (Request $request){
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

        $priceana_usd_country = ImporterBill::select(DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(bill_of_entry_date, 0), \' \', \'Sunday\'), \'%X%V %W\') AS week_start')
            , DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(bill_of_entry_date, 0), \' \', \'Saturday\'), \'%X%V %W\') AS week_end')
            , DB::raw('origin_country AS labeltitle')
            , DB::raw('ROUND(SUM(total_value_usd_exchange), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy(DB::raw('YEARWEEK(bill_of_entry_date, 0)'))
            ->orderBy(DB::raw('origin_country'))
            ->orderBy(DB::raw('YEARWEEK(bill_of_entry_date, 0)'),'DESC')
            ->paginate(100);
        return $priceana_usd_country;
    }
    public function ga_priceana_usd_port (Request $request){
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

        $priceana_usd_port = ImporterBill::select(DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(bill_of_entry_date, 0), \' \', \'Sunday\'), \'%X%V %W\') AS week_start')
            , DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(bill_of_entry_date, 0), \' \', \'Saturday\'), \'%X%V %W\') AS week_end')
            , DB::raw('indian_port AS labeltitle')
            , DB::raw('ROUND(SUM(total_value_usd_exchange), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy(DB::raw('YEARWEEK(bill_of_entry_date, 0)'))
            ->orderBy(DB::raw('indian_port'))
            ->orderBy(DB::raw('YEARWEEK(bill_of_entry_date, 0)'),'DESC')
            ->paginate(100);
        return $priceana_usd_port;
    }
    public function ga_priceana_usd_importer (Request $request){
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

        $priceana_usd_importer = ImporterBill::select(DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(bill_of_entry_date, 0), \' \', \'Sunday\'), \'%X%V %W\') AS week_start')
            , DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(bill_of_entry_date, 0), \' \', \'Saturday\'), \'%X%V %W\') AS week_end')
            , DB::raw('importer_name AS labeltitle')
            , DB::raw('ROUND(SUM(total_value_usd_exchange), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy(DB::raw('YEARWEEK(bill_of_entry_date, 0)'))
            ->orderBy(DB::raw('importer_name'))
            ->orderBy(DB::raw('YEARWEEK(bill_of_entry_date, 0)'),'DESC')
            ->paginate(100);
        return $priceana_usd_importer;
    }

    public function ga_comparison_usd_importer (Request $request){
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

        $comparison_usd_importer = ImporterBill::select(DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(bill_of_entry_date, 0), \' \', \'Sunday\'), \'%X%V %W\') AS week_start')
            , DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(bill_of_entry_date, 0), \' \', \'Saturday\'), \'%X%V %W\') AS week_end')
            , DB::raw('importer_name AS labeltitle')
            , DB::raw('ROUND(SUM(total_value_usd_exchange), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy(DB::raw('YEARWEEK(bill_of_entry_date, 0)'))
            ->orderBy(DB::raw('importer_name'))
            ->orderBy(DB::raw('YEARWEEK(bill_of_entry_date, 0)'),'DESC')
            ->paginate(100);
        return $comparison_usd_importer;
    }
    public function ga_comparison_usd_country (Request $request){
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

        $comparison_usd_country = ImporterBill::select(DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(bill_of_entry_date, 0), \' \', \'Sunday\'), \'%X%V %W\') AS week_start')
            , DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(bill_of_entry_date, 0), \' \', \'Saturday\'), \'%X%V %W\') AS week_end')
            , DB::raw('origin_country AS labeltitle')
            , DB::raw('ROUND(SUM(total_value_usd_exchange), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy(DB::raw('YEARWEEK(bill_of_entry_date, 0)'))
            ->orderBy(DB::raw('origin_country'))
            ->orderBy(DB::raw('YEARWEEK(bill_of_entry_date, 0)'),'DESC')
            ->paginate(100);
        return $comparison_usd_country;
    }
    public function ga_comparison_usd_ports (Request $request){
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

        $comparison_usd_ports = ImporterBill::select(DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(bill_of_entry_date, 0), \' \', \'Sunday\'), \'%X%V %W\') AS week_start')
            , DB::raw('STR_TO_DATE(CONCAT(YEARWEEK(bill_of_entry_date, 0), \' \', \'Saturday\'), \'%X%V %W\') AS week_end')
            , DB::raw('indian_port AS labeltitle')
            , DB::raw('ROUND(SUM(total_value_usd_exchange), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy(DB::raw('YEARWEEK(bill_of_entry_date, 0)'))
            ->orderBy(DB::raw('indian_port'))
            ->orderBy(DB::raw('YEARWEEK(bill_of_entry_date, 0)'),'DESC')
            ->paginate(100);
        return $comparison_usd_ports;
    }

    public function ga_gsum_8digit(Request $request)
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

        $gsum_8digit = ImporterBill::select(DB::raw('LEFT(hs_code, 8) AS hs_code')
            , DB::raw('ROUND(AVG(unit_price_in_inr),2) AS avg_unit_price')
            , DB::raw('ROUND(SUM(quantity),2) AS quantity_sum')
            , DB::raw('ROUND(SUM(total_value_usd_exchange), 2) AS value_sum_usd')
            , DB::raw('COUNT(*) AS record_count')
            , DB::raw('ROUND(SUM(duty), 2) AS duty_sum'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy(DB::raw('LEFT(hs_code, 8)'))

        ;
        return datatables()->of($gsum_8digit)->make(true);
    }
    public function ga_gsum_2digit(Request $request)
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

        $gsum_8digit = ImporterBill::select(DB::raw('LEFT(hs_code, 2) AS hs_code')
            , DB::raw('ROUND(AVG(unit_price_in_inr),2) AS avg_unit_price')
            , DB::raw('ROUND(SUM(quantity),2) AS quantity_sum')
            , DB::raw('ROUND(SUM(total_value_usd_exchange), 2) AS value_sum_usd')
            , DB::raw('COUNT(*) AS record_count')
            , DB::raw('ROUND(SUM(duty), 2) AS duty_sum'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy(DB::raw('LEFT(hs_code, 2)'))

        ;
        return datatables()->of($gsum_8digit)->make(true);
    }
    public function ga_gsum_4digit(Request $request)
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

        $gsum_8digit = ImporterBill::select(DB::raw('LEFT(hs_code, 4) AS hs_code')
            , DB::raw('ROUND(AVG(unit_price_in_inr),2) AS avg_unit_price')
            , DB::raw('ROUND(SUM(quantity),2) AS quantity_sum')
            , DB::raw('ROUND(SUM(total_value_usd_exchange), 2) AS value_sum_usd')
            , DB::raw('COUNT(*) AS record_count')
            , DB::raw('ROUND(SUM(duty), 2) AS duty_sum'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy(DB::raw('LEFT(hs_code, 4)'))

        ;
        return datatables()->of($gsum_8digit)->make(true);
    }
    public function ga_gsum_port(Request $request)
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

        $gsum_8digit = ImporterBill::select(DB::raw('indian_port AS label_title')
            , DB::raw('ROUND(AVG(unit_price_in_inr),2) AS avg_unit_price')
            , DB::raw('ROUND(SUM(quantity),2) AS quantity_sum')
            , DB::raw('ROUND(SUM(total_value_usd_exchange), 2) AS value_sum_usd')
            , DB::raw('COUNT(*) AS record_count')
            , DB::raw('ROUND(SUM(duty), 2) AS duty_sum'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy(DB::raw('indian_port'))

        ;
        return datatables()->of($gsum_8digit)->make(true);
    }
    public function ga_gsum_country(Request $request)
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

        $gsum_8digit = ImporterBill::select(DB::raw('origin_country AS label_title')
            , DB::raw('ROUND(AVG(unit_price_in_inr),2) AS avg_unit_price')
            , DB::raw('ROUND(SUM(quantity),2) AS quantity_sum')
            , DB::raw('ROUND(SUM(total_value_usd_exchange), 2) AS value_sum_usd')
            , DB::raw('COUNT(*) AS record_count')
            , DB::raw('ROUND(SUM(duty), 2) AS duty_sum'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy(DB::raw('origin_country'))

        ;
        return datatables()->of($gsum_8digit)->make(true);
    }
    public function ga_gsum_unit(Request $request)
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

        $gsum_8digit = ImporterBill::select(DB::raw('unit_quantity AS label_title')
            , DB::raw('ROUND(AVG(unit_price_in_inr),2) AS avg_unit_price')
            , DB::raw('ROUND(SUM(quantity),2) AS quantity_sum')
            , DB::raw('ROUND(SUM(total_value_usd_exchange), 2) AS value_sum_usd')
            , DB::raw('COUNT(*) AS record_count')
            , DB::raw('ROUND(SUM(duty), 2) AS duty_sum'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy(DB::raw('unit_quantity'))

        ;
        return datatables()->of($gsum_8digit)->make(true);
    }

    public function ga_pc_usd_country_max (Request $request){
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

        $pc_usd_country_max = ImporterBill::select('origin_country AS labeltitle',DB::raw('ROUND(MAX(unit_rate_in_usd), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('origin_country')
            ->orderBy(DB::raw('ROUND(MAX(unit_rate_in_usd), 2)'),'DESC')
            ->paginate(15);
        return $pc_usd_country_max;
    }
    public function ga_pc_qua_country_max (Request $request){
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

        $pc_qua_country_max = ImporterBill::select(DB::raw('CONCAT(origin_country, \'::\', unit_quantity) AS labeltitle'),DB::raw('ROUND(MAX(quantity), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('unit_quantity')
            ->orderBy(DB::raw('ROUND(MAX(quantity), 2)'),'DESC')
            ->paginate(15);
        return $pc_qua_country_max;
    }
    public function ga_pc_usd_country_min (Request $request){
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

        $pc_usd_country_min = ImporterBill::select('origin_country AS labeltitle',DB::raw('ROUND(MIN(unit_rate_in_usd), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('origin_country')
            ->orderBy(DB::raw('ROUND(MIN(unit_rate_in_usd), 2)'),'DESC')
            ->paginate(15);
        return $pc_usd_country_min;
    }
    public function ga_pc_qua_country_min (Request $request){
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

        $pc_qua_country_min = ImporterBill::select(DB::raw('CONCAT(origin_country, \'::\', unit_quantity) AS labeltitle'),DB::raw('ROUND(MIN(quantity), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('unit_quantity')
            ->orderBy(DB::raw('ROUND(MIN(quantity), 2)'),'DESC')
            ->paginate(15);
        return $pc_qua_country_min;
    }
    public function ga_pc_usd_port_max (Request $request){
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

        $pc_usd_country_max = ImporterBill::select('indian_port AS labeltitle',DB::raw('ROUND(MAX(unit_rate_in_usd), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('indian_port')
            ->orderBy(DB::raw('ROUND(MAX(unit_rate_in_usd), 2)'),'DESC')
            ->paginate(15);
        return $pc_usd_country_max;
    }
    public function ga_pc_qua_port_max (Request $request){
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

        $pc_qua_country_max = ImporterBill::select(DB::raw('CONCAT(indian_port, \'::\', unit_quantity) AS labeltitle'),DB::raw('ROUND(MAX(quantity), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('unit_quantity')
            ->orderBy(DB::raw('ROUND(MAX(quantity), 2)'),'DESC')
            ->paginate(15);
        return $pc_qua_country_max;
    }
    public function ga_pc_usd_port_min (Request $request){
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

        $pc_usd_port_min = ImporterBill::select('indian_port AS labeltitle',DB::raw('ROUND(MIN(unit_rate_in_usd), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('indian_port')
            ->orderBy(DB::raw('ROUND(MIN(unit_rate_in_usd), 2)'),'DESC')
            ->paginate(15);
        return $pc_usd_port_min;
    }
    public function ga_pc_qua_port_min (Request $request){
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

        $pc_qua_port_min = ImporterBill::select(DB::raw('CONCAT(indian_port, \'::\', unit_quantity) AS labeltitle'),DB::raw('ROUND(MIN(quantity), 2) AS labelvalue'))
            ->when(Auth::user()->hasRole('User'), function ($query) {
                $query->when($this->da_cals, function ($query) {
                    $rows = $this->da_cals;
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
                            $query->orwhere('chapter', 'LIKE', $rows[$i] . '%');
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
                            $query->orwhere('indian_port', 'LIKE', $rows[$i] . '%');
                        }
                    });
                });
                return $query;
            })
            ->when($fyear, function ($query, $fyear) {
                return $query->where(DB::raw('YEAR(bill_of_entry_date)'), '=', $fyear);
            })
            ->when($fmonth, function ($query, $fmonth) {
                return $query->where(DB::raw('DATE_FORMAT(bill_of_entry_date, \'%b\')'), '=', $fmonth);
            })
            ->when($fproduct, function ($query, $fproduct) {
                return $query->where('product_discription', 'LIKE', '%'.$fproduct.'%');
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
                        $query->orwhere('chapter', 'LIKE', $fchapter[$i] . '%');
                    }
                });
            })
            ->when($fcountry, function ($query, $fcountry) {
                return $query->where('origin_country', 'LIKE', '%'.$fcountry.'%');
            })
            ->when($fport, function ($query, $fport) {
                return $query->where('indian_port', 'LIKE', '%'.$fport.'%');
            })
            ->when($funit, function ($query, $funit) {
                return $query->where('unit_quantity', 'LIKE', '%'.$funit.'%');
            })
            ->when($fimpexpname, function ($query, $fimpexpname) {
                return $query->where('importer_name', 'LIKE', '%'.$fimpexpname.'%');
            })
            ->groupBy('unit_quantity')
            ->orderBy(DB::raw('ROUND(MIN(quantity), 2)'),'DESC')
            ->paginate(15);
        return $pc_qua_port_min;
    }

    public function get_ajax_points_bal (){
        $points = DataAccess::where('user_id', Auth::user()->id)->where('right_name','selpoints')->first();
        return isset($points->right_option)?$points->right_option:0;
    }
    public function put_ajax_points(Request $request){
        //print_r($request->drows);
        $upoints = 0;
        $userpoints = DataAccess::where('user_id', Auth::user()->id)->where('right_name','selpoints')->first();
        if($userpoints){
            $upoints = isset($userpoints->right_option)?$userpoints->right_option:0;
        }
        if($request->input('drows') <= $upoints){
            $userpoints->update(['right_option' => ($upoints - $request->input('drows'))]);
            $response = array(
                'status' => 'success',
                'msg' => 'Points Updated Successfully!',
            );
            return Response::json($response);
        }
        $response = array(
            'status' => 'failed',
            'msg' => 'Insufficient Points!',
        );
        return Response::json($response);

    }

}
