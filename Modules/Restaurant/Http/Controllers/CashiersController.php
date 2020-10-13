<?php

namespace Modules\Restaurant\Http\Controllers;

use App\Employee;
use App\Transaction;
use App\{Account, Entry, Safe};
use App\{User, Role, Permission};
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Modules\Restaurant\Models\{Waiter, Order, Delivery, Driver};
class CashiersController extends Controller
{
    
    public function __construct() {
        $this->middleware('permission:employees-create')->only(['create', 'store']);
        $this->middleware('permission:employees-read')->only(['index', 'show']);
        $this->middleware('permission:employees-update')->only(['edit', 'update']);
        $this->middleware('permission:employees-delete')->only('destroy');
    }
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        $cashiers = Employee::cashiers()->except([auth()->user()->employee->id]);
        return view('restaurant::cashiers.index', compact('cashiers'));
    }
    
    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        $permissions = Permission::all();
        return view('restaurant::cashiers.create', compact('permissions'));
    }
    
    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
        $rules = [
        'name' => 'required|string|max:45',
        'phone' => 'required|string|max:30',
        'address' => 'required|string|max:255',
        'username' => 'required|string|max:100|min:3 |regex:/^[A-Za-z-0-9]+$/| unique:users',
        'password' => 'required|string|min:6',
        ];
        $request->validate($rules);
        
        $employee = Employee::create($request->only(['name', 'phone', 'address','salary']));
        
        $user_data['username'] = $request->username;
        $user_data['phone'] = $request->phone;
        $user_data['password'] = bcrypt($request->password);
        $user_data['employee_id'] = $employee->id;
        $user = User::create($user_data);
        if ($user) {
            $user->roles()->attach(Role::cashier()->id);
            if ($request->permissions) $user->permissions()->attach($request->permissions);
        }
        
        session()->flash('success', 'تمت اضافة الموظف بنجاح');
        if ($request->next == 'list') {
            return redirect()->route('restaurant.cashiers.index');
        }
        elseif ($request->next == 'show') {
            return redirect()->route('restaurant.cashiers.show', $employee);
        }
        else{
            return back();
        }
    }
    
    /**
    * Display the specified resource.
    *
    * @param  \App\Employee  $employee
    * @return \Illuminate\Http\Response
    */
    public function show(Request $request, Employee $cashier)
    {
        $user = $cashier->user;
        $account = $cashier->account;
        $builder = Order::where('user_id', $user->id)->orderBy('updated_at');
        $date = $request->has('date') ? $request->date : date('Y-m-d');
        $to_date = $date;
        
        $type = $request->has('type') ? $request->type : 'all';
        $status = $request->has('status') ? $request->status : 'all';
        $status = $type == 'takeaway' && $status == 'open' ? 'closed' : $status;
        
        $date_time = $date . ' 00:00:00';
        $to_date_time = $to_date . ' 23:59:59';
        
        $builder->whereBetween('created_at', [$date_time, $to_date_time]);
        
        if ($type != 'all') {
            $builder->where('type', Order::getTypeValue($type));
        }
        
        if ($status != 'all') {
            $builder->where('status', Order::getStatusValue($status));
        }
        // dd($user->orders->whereBetween('created_at', [$date_time, $to_date_time])->pluck('status'));
        $orders = $builder->get();
        $orders = $orders->sortByDesc('status');
        /*->groupBy(function($order) {
        return Carbon::parse($order->created_at)->format('Y-m-d');
        });*/
        
        $daily_entries = $cashier->dailyEntries($date);
        $opening_entry = $cashier->openingEntry($date);
        $close_entry = $cashier->closeEntry($date);
        // dd($daily_entries, $opening_entry, $close_entry, $adjust_entry);
        $accounts = Account::where('id', '!=', $account->id)->get();
        $safes = Safe::all();
        $closing_amount = $orders->sum('amount');
        $closing_amount += $opening_entry ? $opening_entry->amount : 0;
        $deducation = null;
        if ($close_entry) {
            $close_date_time = $close_entry->created_at;
            $close_date_time_plus_2_minutes = $close_entry->created_at->addMinutes(2);
            $deducation = Transaction::where('employee_id', $cashier->id)->where('type', Transaction::TYPE_DEDUCATION)->whereBetween('created_at', [$close_date_time->toDateTimeString(), $close_date_time_plus_2_minutes->toDateTimeString()])->first();
        }
        return view('restaurant::cashiers.show', compact('cashier', 'user', 'orders', 'safes', 'accounts', 'account', 'deducation', 'type', 'status', 'date', 'closing_amount', 'to_date', 'daily_entries', 'opening_entry', 'close_entry'));
    }
    
    /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Employee  $cashier
    * @return \Illuminate\Http\Response
    */
    public function edit(Employee $cashier)
    {
        $user = $cashier->user;
        $permissions = Permission::all();
        $cashier_permissions = $cashier->permissions;
        $cashier_permissions = is_null($cashier_permissions) ? [] : $cashier->permissions->pluck('id')->toArray();
        return view('restaurant::cashiers.edit', compact('cashier', 'user', 'permissions', 'cashier_permissions'));
    }
    
    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\Employee  $employee
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, Employee $cashier)
    {
        $user = $cashier->user;
        $request->validate([
        'name' => 'required|string|max:45',
        'phone' => 'required|string|max:30|unique:employees,phone,'.$cashier->id,
        'address' => 'required|string|max:255',
        'username' => 'required|string|max:100|min:3 |regex:/^[A-Za-z-0-9]+$/|unique:users,username,'.$user->id,
        'password' => 'string|min:6|nullable',
        ]);
        
        $cashier->update($request->only(['name', 'phone', 'address','salary']));
        
        $user_data['username'] = $request->username;
        
        if ($request->has('phone')) {
            $user_data['phone'] = $request->phone;
        }
        
        if (!empty($request->password)) {
            $user_data['password'] = bcrypt($request->password);
        }
        
        $user->update($user_data);
        if ($request->permissions) {
            $user->permissions()->detach();
            $user->permissions()->attach($request->permissions);
        }
        session()->flash('success', 'تمت العملية بنجاح');
        
        if ($request->next == 'list') {
            return redirect()->route('restaurant.cashiers.index');
        }
        elseif ($request->next == 'show') {
            return redirect()->route('restaurant.cashiers.show', $cashier);
        }
        else{
            return back();
        }
    }
    
    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Employee  $employee
    * @return \Illuminate\Http\Response
    */
    public function destroy(Employee $cashier)
    {
        $cashier->delete();
        
        session()->flash('success', 'تمت العملية بنجاح');
        
        
        return redirect()->route('restaurant.cashiers.index');
    }
}