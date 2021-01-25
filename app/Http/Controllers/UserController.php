<?php

namespace App\Http\Controllers;


use App\User;
use App\Models\Role;
use App\Models\ImporterBill;
use App\Models\ExporterBill;
use App\Models\DataAccess;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;
use Rap2hpoutre\FastExcel\FastExcel;
use Carbon\Carbon;
class UserController extends Controller
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
    }

    /**
     * Display a listing of the users
     *
     * @param  \App\User  $model
     * @return \Illuminate\View\View
     */
    public function index(User $model)
    {
        $roles=Role::all();
        if(Auth::User()->role_id >= 11 and Auth::User()->role_id <= 13) {
            $users = $model->paginate(15);
        } else {
            $users = $model->where('role_id', '!=', '11')->orWhereNull('role_id')->paginate(15);
        }
        return view('users.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new user
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if(Auth::User()->role_id == '1') {
            $roles=Role::all();
        } else {
            $roles = Role::where('id', '!=', '1')->get();
        }
        $caloptions = '';
        $arr_months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $caltotal = ImporterBill::select(
            DB::raw('YEAR(bill_of_entry_date) as year'),
            DB::raw('MONTH(bill_of_entry_date) as month'),
            DB::raw('count(*) as num_rows')
        )->groupBy('year', 'month')
            ->get();
        $caltotal_exp = ExporterBill::select(
            DB::raw('YEAR(shipping_bill_date) as year'),
            DB::raw('MONTH(shipping_bill_date) as month'),
            DB::raw('count(*) as num_rows')
        )->groupBy('year', 'month')
            ->get();
        //dd($caloptions);
        return view('users.create', compact('roles', 'arr_months', 'caltotal', 'caltotal_exp'));
    }

    /**
     * Store a newly created user in storage
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\User  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request, User $model)
    {
        $new_user = $model->create($request->merge(['name' => str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($request->get('name'))))), 'password' => Hash::make($request->get('password'))])->all());
        if($new_user->count()){
            $sel_u_imp_cal = $request->input('seluimpcal');

            if ($request->has('seluimpcal')) {
                $right_name = 'selimpcal';
                $deletedRows = DataAccess::where('user_id', $new_user->id)->where('right_name', $right_name)->delete();
                foreach ($request->input('seluimpcal') as $row) {
                    DataAccess::create([
                        'user_id' => $new_user->id,
                        'right_name' => $right_name,
                        'right_option' => $row,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
            }
            else {
                $right_name = 'selimpcal';
                $deletedRows = DataAccess::where('user_id', $new_user->id)->where('right_name', $right_name)->delete();
            }
            if ($request->has('seluimphscode')) {
                $right_name = 'selimphscode';
                $deletedRows = DataAccess::where('user_id', $new_user->id)->where('right_name', $right_name)->delete();
                foreach ($request->input('seluimphscode') as $row) {
                    DataAccess::create([
                        'user_id' => $new_user->id,
                        'right_name' => $right_name,
                        'right_option' => $row,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
            }
            else {
                $right_name = 'selimphscode';
                $deletedRows = DataAccess::where('user_id', $new_user->id)->where('right_name', $right_name)->delete();
            }
            if ($request->has('seluimpchapter')) {
                $right_name = 'selimpchapter';
                $deletedRows = DataAccess::where('user_id', $new_user->id)->where('right_name', $right_name)->delete();
                foreach ($request->input('seluimpchapter') as $row) {
                    DataAccess::create([
                        'user_id' => $new_user->id,
                        'right_name' => $right_name,
                        'right_option' => $row,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
            }
            else {
                $right_name = 'selimpchapter';
                $deletedRows = DataAccess::where('user_id', $new_user->id)->where('right_name', $right_name)->delete();
            }
            if ($request->has('seluimpindianport')) {
                $right_name = 'selimpport';
                $deletedRows = DataAccess::where('user_id', $new_user->id)->where('right_name', $right_name)->delete();
                foreach ($request->input('seluimpindianport') as $row) {
                    DataAccess::create([
                        'user_id' => $new_user->id,
                        'right_name' => $right_name,
                        'right_option' => $row,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
            }
            else {
                $right_name = 'selimpport';
                $deletedRows = DataAccess::where('user_id', $new_user->id)->where('right_name', $right_name)->delete();
            }

            if ($request->has('seluexpcal')) {
                $right_name = 'selexpcal';
                $deletedRows = DataAccess::where('user_id', $new_user->id)->where('right_name', $right_name)->delete();
                foreach ($request->input('seluexpcal') as $row) {
                    DataAccess::create([
                        'user_id' => $new_user->id,
                        'right_name' => $right_name,
                        'right_option' => $row,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
            }
            else {
                $right_name = 'selexpcal';
                $deletedRows = DataAccess::where('user_id', $new_user->id)->where('right_name', $right_name)->delete();
            }
            if ($request->has('seluexphscode')) {
                $right_name = 'selexphscode';
                $deletedRows = DataAccess::where('user_id', $new_user->id)->where('right_name', $right_name)->delete();
                foreach ($request->input('seluexphscode') as $row) {
                    DataAccess::create([
                        'user_id' => $new_user->id,
                        'right_name' => $right_name,
                        'right_option' => $row,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
            }
            else {
                $right_name = 'selexphscode';
                $deletedRows = DataAccess::where('user_id', $new_user->id)->where('right_name', $right_name)->delete();
            }
            if ($request->has('seluexpchapter')) {
                $right_name = 'selexpchapter';
                $deletedRows = DataAccess::where('user_id', $new_user->id)->where('right_name', $right_name)->delete();
                foreach ($request->input('seluexpchapter') as $row) {
                    DataAccess::create([
                        'user_id' => $new_user->id,
                        'right_name' => $right_name,
                        'right_option' => $row,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
            }
            else {
                $right_name = 'selexpchapter';
                $deletedRows = DataAccess::where('user_id', $new_user->id)->where('right_name', $right_name)->delete();
            }
            if ($request->has('seluexpindianport')) {
                $right_name = 'selexpport';
                $deletedRows = DataAccess::where('user_id', $new_user->id)->where('right_name', $right_name)->delete();
                foreach ($request->input('seluexpindianport') as $row) {
                    DataAccess::create([
                        'user_id' => $new_user->id,
                        'right_name' => $right_name,
                        'right_option' => $row,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
            }
            else {
                $right_name = 'selexpport';
                $deletedRows = DataAccess::where('user_id', $new_user->id)->where('right_name', $right_name)->delete();
            }

            if ($request->has('selupoints')) {
                if(null !== $request->input('selupoints') and $request->input('selupoints') != '') {
                    $right_name = 'selpoints';
                    $deletedRows = DataAccess::where('user_id', $new_user->id)->where('right_name', $right_name)->delete();

                    DataAccess::create([
                        'user_id' => $new_user->id,
                        'right_name' => $right_name,
                        'right_option' => $request->input('selupoints'),
                        'description' => '',
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
                else {
                    $right_name = 'selpoints';
                    $deletedRows = DataAccess::where('user_id', $new_user->id)->where('right_name', $right_name)->delete();
                }
            }
            else {
                $right_name = 'selpoints';
                $deletedRows = DataAccess::where('user_id', $new_user->id)->where('right_name', $right_name)->delete();
            }
            if ($request->has('seluexpirydate')) {
                if(null !== $request->input('seluexpirydate') and $request->input('seluexpirydate') != '') {
                    $selDate = $request->input('seluexpirydate');
                    $selDate = \DateTime::createFromFormat('d/m/Y', $selDate);
                    $newSelDate = Carbon::parse(date_format($selDate, 'Y-m-d'));

                    $new_user->update(['expiry_date' => $newSelDate]);
                }
                else {
                    $new_user->update(['expiry_date' => null]);
                }
            }
            else {
                $new_user->update(['expiry_date' => null]);
            }
        }
        return redirect()->route('user.index')->withStatus(__('User successfully created.'));
    }


    /**
     * Show the form for editing the specified user
     *
     * @param  \App\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        
           
        if(Auth::User()->role_id == '1') {
            $roles=Role::all();
        } else {
            $roles = Role::where('id', '!=', '1')->get();
        }

        $caloptions = '';
        $arr_months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $caltotal = ImporterBill::select(
                        DB::raw('YEAR(bill_of_entry_date) as year'),
                        DB::raw('MONTH(bill_of_entry_date) as month'),
                        DB::raw('count(*) as num_rows')
                    )->groupBy('year', 'month')
                        ->get();
        $user_imp_cal = array_column($user->data_access->where('right_name','selimpcal')->where('user_id',$user->id)->toArray(),'right_option');
        //print_r($user_imp_cal);

        $rows = $user_imp_cal;
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
            ->get()->toArray();
        $user_imp_hs_codes = array_column($user->data_access->where('right_name','selimphscode')->where('user_id',$user->id)->toArray(),'right_option');

        $imp_cal_rows     = $user_imp_cal;
        $imp_hs_code_rows = $user_imp_hs_codes;

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
            ->get()->toArray();
        $user_imp_chapters = array_column($user->data_access->where('right_name','selimpchapter')->where('user_id',$user->id)->toArray(),'right_option');


        $imp_chapter_rows = $user_imp_chapters;

        $imp_ports = ImporterBill::select(DB::raw('count(*) as count_rows, indian_port as value'))
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
            ->get()->toArray();
        $user_imp_ports = array_column($user->data_access->where('right_name','selimpport')->where('user_id',$user->id)->toArray(),'right_option');


        $caltotal_exp = ExporterBill::select(
            DB::raw('YEAR(shipping_bill_date) as year'),
            DB::raw('MONTH(shipping_bill_date) as month'),
            DB::raw('count(*) as num_rows')
        )->groupBy('year', 'month')
            ->get();
        $user_exp_cal = array_column($user->data_access->where('right_name','selexpcal')->where('user_id',$user->id)->toArray(),'right_option');

        $rows = $user_exp_cal;
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
            ->get()->toArray();
        $user_exp_hs_codes = array_column($user->data_access->where('right_name','selexphscode')->where('user_id',$user->id)->toArray(),'right_option');

        $exp_cal_rows     = $user_exp_cal;
        $exp_hs_code_rows = $user_exp_hs_codes;
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
            ->get()->toArray();
        $user_exp_chapters = array_column($user->data_access->where('right_name','selexpchapter')->where('user_id',$user->id)->toArray(),'right_option');

        $exp_chapter_rows = $user_exp_chapters;
        $exp_ports = ExporterBill::select(DB::raw('count(*) as count_rows, indian_port as value'))
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
            ->get()->toArray();
        $user_exp_ports = array_column($user->data_access->where('right_name','selexpport')->where('user_id',$user->id)->toArray(),'right_option');

        // dd($caloptions);
        // return $caltotal;
        return view('users.edit', compact('user', 'roles', 'arr_months', 'caltotal', 'user_imp_cal', 'imp_hs_codes', 'user_imp_hs_codes', 'imp_chapters', 'user_imp_chapters', 'imp_ports', 'user_imp_ports', 'caltotal_exp', 'user_exp_cal', 'exp_hs_codes', 'user_exp_hs_codes', 'exp_chapters', 'user_exp_chapters', 'exp_ports', 'user_exp_ports'));
    }

    /**
     * Update the specified user in storage
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserRequest $request, User  $user)
    {
        $hasPassword = $request->get('password');
        $user->update(
            $request->merge([
                'name' => str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($request->get('name'))))),
                'password' => Hash::make($request->get('password'))
                ])->except([$hasPassword ? '' : 'password'])
            );
        if($user->count()){
            $sel_u_imp_cal = $request->input('seluimpcal');

            if ($request->has('seluimpcal')) {
                $right_name = 'selimpcal';
                $deletedRows = DataAccess::where('user_id', $user->id)->where('right_name', $right_name)->delete();
                foreach ($request->input('seluimpcal') as $row) {
                    DataAccess::create([
                        'user_id' => $user->id,
                        'right_name' => $right_name,
                        'right_option' => $row,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
            }
            else {
                $right_name = 'selimpcal';
                $deletedRows = DataAccess::where('user_id', $user->id)->where('right_name', $right_name)->delete();
            }
            if ($request->has('seluimphscode')) {
                $right_name = 'selimphscode';
                $deletedRows = DataAccess::where('user_id', $user->id)->where('right_name', $right_name)->delete();
                foreach ($request->input('seluimphscode') as $row) {
                    DataAccess::create([
                        'user_id' => $user->id,
                        'right_name' => $right_name,
                        'right_option' => $row,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
            }
            else {
                $right_name = 'selimphscode';
                $deletedRows = DataAccess::where('user_id', $user->id)->where('right_name', $right_name)->delete();
            }
            if ($request->has('seluimpchapter')) {
                $right_name = 'selimpchapter';
                $deletedRows = DataAccess::where('user_id', $user->id)->where('right_name', $right_name)->delete();
                foreach ($request->input('seluimpchapter') as $row) {
                    DataAccess::create([
                        'user_id' => $user->id,
                        'right_name' => $right_name,
                        'right_option' => $row,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
            }
            else {
                $right_name = 'selimpchapter';
                $deletedRows = DataAccess::where('user_id', $user->id)->where('right_name', $right_name)->delete();
            }
            if ($request->has('seluimpindianport')) {
                $right_name = 'selimpport';
                $deletedRows = DataAccess::where('user_id', $user->id)->where('right_name', $right_name)->delete();
                foreach ($request->input('seluimpindianport') as $row) {
                    DataAccess::create([
                        'user_id' => $user->id,
                        'right_name' => $right_name,
                        'right_option' => $row,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
            }
            else {
                $right_name = 'selimpport';
                $deletedRows = DataAccess::where('user_id', $user->id)->where('right_name', $right_name)->delete();
            }

            if ($request->has('seluexpcal')) {
                $right_name = 'selexpcal';
                $deletedRows = DataAccess::where('user_id', $user->id)->where('right_name', $right_name)->delete();
                foreach ($request->input('seluexpcal') as $row) {
                    DataAccess::create([
                        'user_id' => $user->id,
                        'right_name' => $right_name,
                        'right_option' => $row,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
            }
            else {
                $right_name = 'selexpcal';
                $deletedRows = DataAccess::where('user_id', $user->id)->where('right_name', $right_name)->delete();
            }
            if ($request->has('seluexphscode')) {
                $right_name = 'selexphscode';
                $deletedRows = DataAccess::where('user_id', $user->id)->where('right_name', $right_name)->delete();
                foreach ($request->input('seluexphscode') as $row) {
                    DataAccess::create([
                        'user_id' => $user->id,
                        'right_name' => $right_name,
                        'right_option' => $row,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
            }
            else {
                $right_name = 'selexphscode';
                $deletedRows = DataAccess::where('user_id', $user->id)->where('right_name', $right_name)->delete();
            }
            if ($request->has('seluexpchapter')) {
                $right_name = 'selexpchapter';
                $deletedRows = DataAccess::where('user_id', $user->id)->where('right_name', $right_name)->delete();
                foreach ($request->input('seluexpchapter') as $row) {
                    DataAccess::create([
                        'user_id' => $user->id,
                        'right_name' => $right_name,
                        'right_option' => $row,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
            }
            else {
                $right_name = 'selexpchapter';
                $deletedRows = DataAccess::where('user_id', $user->id)->where('right_name', $right_name)->delete();
            }
            if ($request->has('seluexpindianport')) {
                $right_name = 'selexpport';
                $deletedRows = DataAccess::where('user_id', $user->id)->where('right_name', $right_name)->delete();
                foreach ($request->input('seluexpindianport') as $row) {
                    DataAccess::create([
                        'user_id' => $user->id,
                        'right_name' => $right_name,
                        'right_option' => $row,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
            }
            else {
                $right_name = 'selexpport';
                $deletedRows = DataAccess::where('user_id', $user->id)->where('right_name', $right_name)->delete();
            }


            if ($request->has('selupoints')) {
                if(null !== $request->input('selupoints') and $request->input('selupoints') != '') {
                    $right_name = 'selpoints';
                    $deletedRows = DataAccess::where('user_id', $user->id)->where('right_name', $right_name)->delete();

                    DataAccess::create([
                        'user_id' => $user->id,
                        'right_name' => $right_name,
                        'right_option' => $request->input('selupoints'),
                        'description' => '',
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
                else {
                    $right_name = 'selpoints';
                    $deletedRows = DataAccess::where('user_id', $user->id)->where('right_name', $right_name)->delete();
                }
            }
            else {
                $right_name = 'selpoints';
                $deletedRows = DataAccess::where('user_id', $user->id)->where('right_name', $right_name)->delete();
            }
            if ($request->has('seluexpirydate')) {
                if(null !== $request->input('seluexpirydate') and $request->input('seluexpirydate') != '') {
                    $selDate = $request->input('seluexpirydate');
                    $selDate = \DateTime::createFromFormat('d/m/Y', $selDate);
                    $newSelDate = Carbon::parse(date_format($selDate, 'Y-m-d'));

                    $user->update(['expiry_date' => $newSelDate]);
                }
                else {
                    $user->update(['expiry_date' => null]);
                }
            }
            else {
                $user->update(['expiry_date' => null]);
            }
        }
        return redirect()->route('user.index')->withStatus(__('User successfully updated.'));
    }

    /**
     * Remove the specified user from storage
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User  $user)
    {
        $user->delete();

        return redirect()->route('user.index')->withStatus(__('User successfully deleted.'));
    }

    public function downloadall (){
        $allUsers = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->select(
                'users.id',
                'users.name As user_name',
                'users.email',
                'roles.name AS role_name',
                'users.created_at'
            )
            ->get();
        if($allUsers->count()){
            $allUsersArray = $allUsers->map(function($obj){
                return (array) $obj;
            })->toArray();
            return (new FastExcel($allUsersArray))->download('ImportExportallusers.xlsx');
        }
    }
}
