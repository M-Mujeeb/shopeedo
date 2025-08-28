<?php

namespace App\Http\Controllers;

// use App\Exports\DeliveryBoyExport;
use App\Services\MailjetAuthMailer;
use App\Models\DeliveryBoyShiftHistory;
use Auth;
use Hash;
use Artisan;
use Carbon\Carbon;
use App\Models\City;
use App\Models\User;
use App\Models\Order;
use App\Models\State;
use App\Models\Country;
use App\Models\DeliveryBoy;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Models\DeliveryHistory;
use App\Models\DeliveryBoyPayment;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\DeliveryBoyCollection;
use App\Models\CollectionReturnHistory;
use App\Models\DeliveryBoyBonusHistory;
use App\Exports\DeliveryBoyExport;
use App\Models\AppliedDeliveryBoy;

class DeliveryBoyController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:view_all_delivery_boy'])->only('index');
        $this->middleware(['permission:add_delivery_boy'])->only('create');
        $this->middleware(['permission:edit_delivery_boy'])->only('edit');
        $this->middleware(['permission:ban_delivery_boy'])->only('ban');
        $this->middleware(['permission:collect_from_delivery_boy'])->only('order_collection_form');
        $this->middleware(['permission:pay_to_delivery_boy'])->only('delivery_earning_form');
        $this->middleware(['permission:bonus_to_delivery_boy'])->only('delivery_bonus_form');
        $this->middleware(['permission:delivery_boy_payment_history'])->only('delivery_boys_payment_histories');
        $this->middleware(['permission:collected_histories_from_delivery_boy'])->only('delivery_boys_collection_histories');
        $this->middleware(['permission:order_cancle_request_by_delivery_boy'])->only('cancel_request_list');
        $this->middleware(['permission:delivery_boy_configuration'])->only('delivery_boy_configure');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $delivery_boys = DeliveryBoy::orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $sort_search = $request->search;
            $user_ids = User::where('user_type', 'delivery_boy')->where(function ($user) use ($sort_search) {
                $user->where('name', 'like', '%' . $sort_search . '%')
                    ->orWhere('email', 'like', '%' . $sort_search . '%');
            })->pluck('id')->toArray();
            $delivery_boys = $delivery_boys->where(function ($delivery_boy) use ($user_ids) {
                $delivery_boy->whereIn('user_id', $user_ids);
            });
        }

        $delivery_boys = $delivery_boys->paginate(15);

        foreach ($delivery_boys as $delivery_boy) {
            $delivery_boy->total_bonus = DeliveryBoyBonusHistory::where('user_id', $delivery_boy->user_id)->where('is_paid', 0)
                ->sum('bonus_amount');
        }

        return view('backend.delivery_boys.index', compact('delivery_boys', 'sort_search'));
    }

    public function export_delivery_boys() {
       return Excel::download(new \App\Exports\DeliveryBoyExport, 'delivery_boys.xlsx');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::where('status', 1)->get();
        return view('backend.delivery_boys.create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'name'          => 'required',
        'email'         => 'required|email|unique:users,email|max:255',
        'phone'         => 'required',
        'city_name'     => 'required',
        'bank_name'     => 'required',
        'bank_acc_name' => 'required',
        'bank_acc_no'   => 'required',
        'password'      => 'required|min:6',
    ]);

    // Create new user
    $user = new User;
    $user->user_type         = 'delivery_boy';
    $user->name              = $request->name;
    $user->email             = $request->email;
    $user->phone             = $request->phone;
    $user->city              = $request->city_name;
    $user->avatar_original   = $request->avatar_original ?? null;
    $user->address           = $request->address ?? null;
    $user->email_verified_at = now();
    $user->password          = Hash::make($request->password);
    $user->save();

    // Create delivery boy entry
    $delivery_boy = new DeliveryBoy;
    $delivery_boy->user_id        = $user->id;
    $delivery_boy->status         = 0;
    $delivery_boy->online_since   = null;
    $delivery_boy->bank_name      = $request->bank_name;
    $delivery_boy->bank_acc_name  = $request->bank_acc_name;
    $delivery_boy->bank_acc_no    = $request->bank_acc_no;
    $delivery_boy->save();

    // Delete from applied_delivery_boys table if exists
    AppliedDeliveryBoy::where('email', $request->email)->delete();

     try {
    \Log::info('Starting Mailjet send process for delivery boy', [
        'email' => $user->email,
        'template_id' => env('MAILJET_TEMPLATE_DELIVERY_BOY_WELLCOME')
    ]);

    $mailjet = new MailjetAuthMailer();

    $templateId = env('MAILJET_TEMPLATE_DELIVERY_BOY_WELLCOME');

    $name = $user->name ?? ucfirst($request->user_type);
    $email = $user->email ?? ucfirst($request->user_type);
    $phone = $user->phone ?? ucfirst($request->user_type);

    $array = [
        'to' => $user->email,
        'subject' => "Welcome to Shopeedo! Your Rider Sign-Up is Confirmed",
        'template_id' => $templateId,
        'variables' => [
            'rider_name' => $name,
            'rider_email' => $email,
            'rider_phone' => $phone,
        ],
        'view' => 'emails.verification',
        'content' => "
            We are excited to welcome you to the Shopeedo team! Your sign-up as a rider has been successfully completed.
            <br><br>
            <strong style='color:#7D9A40'>For your security, do not share this code with anyone.</strong><br><br>
            If you did not request this code, you can safely ignore this email.<br><br>
            Best regards,<br>The Shopeedo Team
        "
    ];

    \Log::debug('Mailjet payload prepared', $array);

    $response = $mailjet->send($array);

    if (!$response->success()) {
        \Log::error('Mailjet send failed', [
            'status' => $response->getStatus(),
            'body' => $response->getBody()
        ]);
    } else {
        \Log::info('Mailjet email sent successfully', [
            'status' => $response->getStatus(),
            'body' => $response->getBody()
        ]);
    }

} catch (\Exception $e) {
    \Log::error('Mailjet exception: ' . $e->getMessage(), [
        'trace' => $e->getTraceAsString()
    ]);
}

    flash(translate('Delivery Boy has been created successfully'))->success();
    return redirect()->route('delivery-boys.index');
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
        $countries = Country::where('status', 1)->get();
        $states = State::where('status', 1)->get();
        $cities = City::where('status', 1)->get();
        $delivery_boy = User::findOrFail($id);
        $delivery_boy_details = DeliveryBoy::where('user_id', $id)->first();

        return view('backend.delivery_boys.edit', compact('delivery_boy', 'delivery_boy_details', 'countries', 'states', 'cities'));
    }

    public function applied_delivery_boys(Request $request)
{
    $sort_search = null;

    // Start with the query builder
    $query = AppliedDeliveryBoy::query();

    if ($request->has('search')) {
        $sort_search = $request->search;

        $query->where(function ($q) use ($sort_search) {
            $q->where('name', 'like', '%' . $sort_search . '%')
              ->orWhere('email', 'like', '%' . $sort_search . '%');
        });
    }

    // Apply pagination
    $delivery_boys = $query->paginate(15);

    return view('backend.delivery_boys.applied_delivery_boys', compact('delivery_boys'));
}


    public function export_applied_delivery_boys() {
        // dd('hi');
       return Excel::download(new \App\Exports\AppliedDeliveryBoyExport, 'applied_delivery_boys.xlsx');
        // return Excel::download(new DeliveryBoyExport, 'delivery_boys.xlsx');
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
        $delivery_boy = User::findOrFail($id);
        $delivery_boy_details = DeliveryBoy::where('user_id', $id)->first();

        $request->validate([
            'name'       => 'required',
            'email'      => 'required|unique:users,email,' . $delivery_boy->id,
            'phone'      => 'required',
            'country_id' => 'required',
            'state_id'   => 'required',
            'city_id'    => 'required',
            'bank_name'     => 'required',
            'bank_acc_name' => 'required',
            'bank_acc_no'   => 'required',
        ]);

        $country = Country::where('id', $request->country_id)->first();
        $state = State::where('id', $request->state_id)->first();
        $city = City::where('id', $request->city_id)->first();

        $delivery_boy->name             = $request->name;
        $delivery_boy->email            = $request->email;
        $delivery_boy->phone            = $request->phone;
        $delivery_boy->country          = $country->name;
        $delivery_boy->state            = $state->name;
        $delivery_boy->city             = $city->name;
        $delivery_boy->avatar_original  = $request->avatar_original;
        $delivery_boy->address          = $request->address;
        $delivery_boy_details->bank_name = $request->bank_name;
        $delivery_boy_details->bank_acc_name = $request->bank_acc_name;
        $delivery_boy_details->bank_acc_no = $request->bank_acc_no;

        $delivery_boy_details->save();

        if (strlen($request->password) > 0) {
            $delivery_boy->password = Hash::make($request->password);
        }

        $delivery_boy->save();

        flash(translate('Delivery Boy has been updated successfully'))->success();
        return back();
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

    public function ban($id)
    {
        $delivery_boy = User::findOrFail($id);

        if ($delivery_boy->banned == 1) {
            $delivery_boy->banned = 0;
            flash(translate('Delivery Boy UnBanned Successfully'))->success();
        } else {
            $delivery_boy->banned = 1;
            flash(translate('Delivery Boy Banned Successfully'))->success();
        }

        $delivery_boy->save();

        return back();
    }

    /**
     * Collection form from Delivery boy.
     *
     * @return \Illuminate\Http\Response
     */
    public function order_collection_form(Request $request)
    {
        $delivery_boy_info = DeliveryBoy::with('user')
            ->where('user_id', $request->id)
            ->first();

        return view('backend.delivery_boys.order_collection_form', compact('delivery_boy_info'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function collection_from_delivery_boy(Request $request)
    {
        $request->validate([
            'payout_amount' => 'required|numeric|min:1',
        ]);

        $delivery_boy = DeliveryBoy::where('user_id', $request->delivery_boy_id)->first();

        if ($request->payout_amount > $delivery_boy->total_collection) {
            flash(translate('Collection Amount cannot Be Larger Than Collected Amount'))->error();
            return redirect()->route('delivery-boys.index');
        }

        $delivery_boy->total_collection -= $request->payout_amount;

        if ($delivery_boy->save()) {
            $delivery_boy_collection          = new DeliveryBoyCollection;
            $delivery_boy_collection->user_id = $request->delivery_boy_id;
            $delivery_boy_collection->collection_amount = $request->payout_amount;

            $delivery_boy_collection->save();

            flash(translate('Collection From Delivery Boy Successfully'))->success();
        } else {
            flash(translate('Something went wrong'))->error();
        }

        return redirect()->route('delivery-boys.index');
    }

    /**
     * Paid form for Delivery boy.
     *
     * @return \Illuminate\Http\Response
     */
    public function delivery_earning_form(Request $request)
    {
        $delivery_boy_info = DeliveryBoy::with('user')
            ->where('user_id', $request->id)
            ->first();
            $unpaid_bonus = DeliveryBoyBonusHistory::where('user_id', $request->id)
            ->where('is_paid', 0)
            ->sum('bonus_amount');
            $total_payable = $delivery_boy_info->total_earning + $unpaid_bonus;

        return view('backend.delivery_boys.delivery_earning_form', compact('delivery_boy_info', 'total_payable', 'unpaid_bonus'));
    }

    /**
     * Paid form for Delivery boy.
     *
     * @return \Illuminate\Http\Response
     */
    public function delivery_bonus_form(Request $request)
    {
        $delivery_boy_info = DeliveryBoy::with('user')
            ->where('user_id', $request->id)
            ->first();

            $unpaid_bonus = DeliveryBoyBonusHistory::where('user_id', $request->id)
            ->where('is_paid', 0)
            ->sum('bonus_amount');
            $total_payable = $unpaid_bonus;

        return view('backend.delivery_boys.delivery_bonus_form', compact('delivery_boy_info', 'total_payable'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function paid_to_delivery_boy(Request $request)
    {
        $delivery_boy = DeliveryBoy::where('user_id', $request->delivery_boy_id)->first();

        if ($request->paid_amount > $delivery_boy->total_earning) {
            flash(translate('Paid Amount cannot Be Larger Than Payable Amount'))->error();
            return redirect()->route('delivery-boys.index');
        }

        $delivery_boy->total_earning -= $request->paid_amount;

        if ($delivery_boy->save()) {
            $delivery_boy_payment          = new DeliveryBoyPayment;
            $delivery_boy_payment->user_id = $request->delivery_boy_id;
            $delivery_boy_payment->payment = $request->paid_amount;
            $delivery_boy_payment->payment_type = 'earning';

            $delivery_boy_payment->save();

            flash(translate('Pay To Delivery Boy Successfully'))->success();
        } else {
            flash(translate('Something went wrong'))->error();
        }

        return redirect()->route('delivery-boys.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function bonus_to_delivery_boy(Request $request)
{
    $delivery_boy = DeliveryBoy::where('user_id', $request->delivery_boy_id)->first();
    $bonus_amount = DeliveryBoyBonusHistory::where('user_id', $request->delivery_boy_id)
        ->where('is_paid', 0)
        ->sum('bonus_amount');

    if ($request->paid_amount > $bonus_amount) {
        flash(translate('Paid Amount cannot Be Larger Than Payable Amount'))->error();
        return redirect()->route('delivery-boys.index');
    }

    $remaining_amount = $request->paid_amount;

    $bonuses = DeliveryBoyBonusHistory::where('user_id', $request->delivery_boy_id)
        ->where('is_paid', 0)
        ->orderBy('id', 'asc')
        ->get();

    foreach ($bonuses as $bonus) {
        if ($remaining_amount <= 0) {
            break;
        }

        if ($remaining_amount >= $bonus->bonus_amount) {
            $bonus->is_paid = 1;
            $remaining_amount -= $bonus->bonus_amount;
        } else {
            $bonus->bonus_amount -= $remaining_amount;
            $remaining_amount = 0;
        }

        $bonus->save();
    }

    if ($delivery_boy->save()) {
        $delivery_boy_payment = new DeliveryBoyPayment;
        $delivery_boy_payment->user_id = $request->delivery_boy_id;
        $delivery_boy_payment->payment = $request->paid_amount;
        $delivery_boy_payment->payment_type = 'bonus';

        $delivery_boy_payment->save();

        flash(translate('Bonus Paid To Delivery Boy Successfully'))->success();
    } else {
        flash(translate('Something went wrong'))->error();
    }

    return redirect()->route('delivery-boys.index');
}




    // Delivery Boy's Panel Start
    public function delivery_boys_payment_histories(Request $request)
    {
        $delivery_boy_payment_query = DeliveryBoyPayment::query();
        if (Auth::user()->user_type == 'delivery_boy') {
            $delivery_boy_payment_query = $delivery_boy_payment_query->where('user_id', Auth::user()->id);
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $user_ids = User::where('user_type', 'delivery_boy')->where(function ($user) use ($sort_search) {
                $user->where('name', 'like', '%' . $sort_search . '%')
                    ->orWhere('email', 'like', '%' . $sort_search . '%');
            })->pluck('id')->toArray();
            $delivery_boys = $delivery_boy_payment_query->where(function ($delivery_boy) use ($user_ids) {
                $delivery_boy->whereIn('user_id', $user_ids);
            });
        }
        $delivery_boy_payment_query = $delivery_boy_payment_query->paginate(10);

        $delivery_boy_payments = $delivery_boy_payment_query;

        return view('backend.delivery_boys.delivery_boys_payment_list', compact('delivery_boy_payments'));
    }
    public function showPaymentModal($user_id)
{
    $user = User::findOrFail($user_id);
    $bonus = $user->calculateWeeklyBonus();
    $outstanding = $user->calculateOutstandingAmount();
    $total = $bonus + $outstanding;
    return view('backend.delivery_boys.payment_modal', compact('user', 'bonus', 'outstanding', 'total'));
}
public function processPayment(Request $request)
{
    $request->validate(['amount' => 'required|numeric|min:1']);
    $user = User::findOrFail($request->user_id);

    if (Carbon::now()->dayOfWeek !== Carbon::FRIDAY) {
        return back()->with('error', 'Payments can only be made on Fridays.');
    }

    DeliveryBoyPayment::create([
        'user_id' => $user->id,
        'payment' => $request->amount,
        'bonus' => $user->calculateWeeklyBonus(),
        'outstanding_amount' => $user->calculateOutstandingAmount() - $request->amount
    ]);

    return back()->with('success', 'Payment processed successfully.');
}

    public function delivery_boys_collection_histories(Request $request)
    {
        $delivery_boy_collection_query = DeliveryBoyCollection::query();
        if (Auth::user()->user_type == 'delivery_boy') {
            $delivery_boy_collection_query = $delivery_boy_collection_query->where('user_id', Auth::user()->id);
        }

        if ($request->has('search')) {
            $sort_search = $request->search;
            $user_ids = User::where('user_type', 'delivery_boy')->where(function ($user) use ($sort_search) {
                $user->where('name', 'like', '%' . $sort_search . '%')
                    ->orWhere('email', 'like', '%' . $sort_search . '%');
            })->pluck('id')->toArray();
            $delivery_boys = $delivery_boy_collection_query->where(function ($delivery_boy) use ($user_ids) {
                $delivery_boy->whereIn('user_id', $user_ids);
            });
        }
        $delivery_boy_collection_query = $delivery_boy_collection_query->paginate(10);

        $delivery_boy_collections = $delivery_boy_collection_query;

        return view('backend.delivery_boys.delivery_boys_collection_list', compact('delivery_boy_collections'));
    }

    public function delivery_boys_timesheet(Request $request)
    {
        $tz = 'Asia/Karachi';
    
        // Filters (defaults: current month → today, local time)
        $fromLocal = $request->filled('from')
            ? Carbon::parse($request->input('from').' 00:00:00', $tz)
            : Carbon::now($tz)->startOfMonth();
    
        $toLocal = $request->filled('to')
            ? Carbon::parse($request->input('to').' 23:59:59', $tz)
            : Carbon::now($tz)->endOfDay();
    
        // Convert to UTC for DB query
        $fromUtc = $fromLocal->copy()->utc();
        $toUtc   = $toLocal->copy()->utc();
    
        // Base list of delivery boys (searchable)
        $sort_search = $request->input('search');
        $delivery_boys_q = DeliveryBoy::with('user')->orderByDesc('created_at');
    
        if ($sort_search) {
            $user_ids = User::where('user_type', 'delivery_boy')
                ->where(function ($q) use ($sort_search) {
                    $q->where('name', 'like', "%{$sort_search}%")
                      ->orWhere('email', 'like', "%{$sort_search}%");
                })
                ->pluck('id')
                ->toArray();
    
            $delivery_boys_q->whereIn('user_id', $user_ids);
        }
    
        // Paginate boys; compute timesheets only for the current page
        $delivery_boys = $delivery_boys_q->paginate(10);
    
        $pageUserIds = $delivery_boys->pluck('user_id')->all();
    
        // Fetch closed sessions overlapping the range (for users on current page)
        $sessions = DeliveryBoyShiftHistory::whereIn('user_id', $pageUserIds)
            ->whereNotNull('end_at')
            ->whereTime('end_at', '!=', '00:00:00')
            ->where(function ($q) use ($fromUtc, $toUtc) {
                $q->whereBetween('start_at', [$fromUtc, $toUtc])
                  ->orWhereBetween('end_at',   [$fromUtc, $toUtc])
                  ->orWhere(function ($q2) use ($fromUtc, $toUtc) {
                      $q2->where('start_at', '<=', $fromUtc)
                         ->where('end_at',   '>=', $toUtc);
                  });
            })
            ->orderByDesc('start_at')
            ->get(['user_id','start_at','end_at']);
    
        // Build per-user timesheets (group by local date)
        $timesheets = [];          // [user_id][Y-m-d] => ['intervals' => [...], 'day_seconds' => int]
        $userTotals = [];          // [user_id] => total_seconds
    
        foreach ($sessions as $s) {
            // Force parse as UTC from raw DB string
            $startUtc = Carbon::createFromFormat('Y-m-d H:i:s', $s->getRawOriginal('start_at'), 'UTC');
            $endUtc   = Carbon::createFromFormat('Y-m-d H:i:s', $s->getRawOriginal('end_at'),   'UTC');
    
            $durSecs = $endUtc->diffInSeconds($startUtc);
    
            $startLocal = $startUtc->copy()->setTimezone($tz);
            $endLocal   = $endUtc->copy()->setTimezone($tz);
            $dateKey    = $startLocal->format('Y-m-d');
    
            if (!isset($timesheets[$s->user_id][$dateKey])) {
                $timesheets[$s->user_id][$dateKey] = [
                    'intervals'   => [],
                    'day_seconds' => 0,
                ];
            }
    
            $timesheets[$s->user_id][$dateKey]['intervals'][] = [
                'start_time' => $startLocal->format('H:i:s'),
                'end_time'   => $endLocal->format('H:i:s'),
                'seconds'    => $durSecs,
            ];
            $timesheets[$s->user_id][$dateKey]['day_seconds'] += $durSecs;
    
            $userTotals[$s->user_id] = ($userTotals[$s->user_id] ?? 0) + $durSecs;
        }
    
        foreach ($timesheets as $uid => &$days) {
            // Sort intervals within day (latest → oldest)
            foreach ($days as &$day) {
                usort($day['intervals'], function ($a, $b) {
                    return strcmp($b['start_time'], $a['start_time']);
                });
            }
            unset($day);
    
            // Sort dates desc
            uksort($days, function ($a, $b) { return strcmp($b, $a); });
        }
        unset($days);
    
        return view(
            'backend.delivery_boys.delivery_boys_timesheet',
            compact('delivery_boys', 'timesheets', 'userTotals', 'fromLocal', 'toLocal', 'sort_search', 'tz')
        );
    }

    public function delivery_boys_bonus_histories(){
        $delivery_boy_collection_query = DeliveryBoyBonusHistory::query();
        if (Auth::user()->user_type == 'delivery_boy') {
            $delivery_boy_collection_query = $delivery_boy_collection_query->where('user_id', Auth::user()->id);
        }
        $delivery_boy_collection_query = $delivery_boy_collection_query->paginate(10);

        $delivery_boy_collections = $delivery_boy_collection_query;

        return view('backend.delivery_boys.delivery_boys_bonus_list', compact('delivery_boy_collections'));
    }

    public function delivery_boys_cancel_request_list()
    {
        $order_query = Order::query();
        if (Auth::user()->user_type == 'delivery_boy') {
            $order_query = $order_query->where('assign_delivery_boy', Auth::user()->id);
        }
        $order_query = $order_query->where('delivery_status', '!=', 'cancelled')->where('cancel_request', 1);
        $order_query = $order_query->paginate(10);

        $cancel_requests = $order_query;
        return view('delivery_boys.cancel_request_list', compact('cancel_requests'));
    }

    public function cancel_request_list()
    {
        $order_query = Order::query();
        if (Auth::user()->user_type == 'delivery_boy') {
            $order_query = $order_query->where('assign_delivery_boy', Auth::user()->id);
        }
        $order_query = $order_query->where('delivery_status', '!=', 'cancelled')->where('cancel_request', 1);
        $order_query = $order_query->paginate(10);

        $cancel_requests = $order_query;
        return view('backend.delivery_boys.cancel_request_list', compact('cancel_requests'));
    }

    /**
     * Configuration of delivery boy.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delivery_boy_configure()
    {
        return view('backend.delivery_boys.delivery_boy_configure');
    }

    public function order_detail($id)
    {
        $order = Order::findOrFail(decrypt($id));
        return view('delivery_boys.order_detail', compact('order'));
    }

    /**
     * Show the list of assigned delivery by the admin.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function assigned_delivery()
    {
        $order_query = Order::query();
        $order_query->where('assign_delivery_boy', Auth::user()->id);

        $order_query->where(function ($query) {
            $query->where(function ($q) {
                $q->where('delivery_status', 'pending')
                    ->where('cancel_request', '0');
            })->orWhere(function ($q) {
                $q->where('delivery_status', 'confirmed')
                    ->where('cancel_request', '0');
            });
        });

        $assigned_deliveries = $order_query->paginate(10);

        return view('delivery_boys.assigned_delivery', compact('assigned_deliveries'));
    }

    /**
     * Show the list of pickup delivery by the delivery boy.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function pickup_delivery()
    {
        $pickup_deliveries = Order::where('assign_delivery_boy', Auth::user()->id)
            ->where('delivery_status', 'picked_up')
            ->where('cancel_request', '0')
            ->paginate(10);

        return view('delivery_boys.pickup_delivery', compact('pickup_deliveries'));
    }

    /**
     * Show the list of pickup delivery by the delivery boy.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function on_the_way_deliveries()
    {
        $on_the_way_deliveries = Order::where('assign_delivery_boy', Auth::user()->id)
            ->where('delivery_status', 'on_the_way')
            ->where('cancel_request', '0')
            ->paginate(10);

        return view('delivery_boys.on_the_way_delivery', compact('on_the_way_deliveries'));
    }

    /**
     * Show the list of completed delivery by the delivery boy.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function completed_delivery()
    {
        $completed_deliveries = DeliveryHistory::where('delivery_boy_id', Auth::user()->id)
            ->where('delivery_status', 'delivered')
            ->paginate(10);

        return view('delivery_boys.completed_delivery', compact('completed_deliveries'));
    }

    /**
     * Show the list of pending delivery by the delivery boy.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function pending_delivery()
    {
        $pending_deliveries = Order::where('assign_delivery_boy', Auth::user()->id)
            ->where('delivery_status', '!=', 'delivered')
            ->where('delivery_status', '!=', 'cancelled')
            ->where('cancel_request', '0')
            ->paginate(10);

        return view('delivery_boys.pending_delivery', compact('pending_deliveries'));
    }

    /**
     * Show the list of cancelled delivery by the delivery boy.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancelled_delivery()
    {
        $cancelled_deliveries = Order::where('assign_delivery_boy', Auth::user()->id)
            ->where('delivery_status', 'cancelled')
            ->paginate(10);

        return view('delivery_boys.cancelled_delivery', compact('cancelled_deliveries'));
    }

    /**
     * Show the list of total collection by the delivery boy.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function total_collection()
    {
        $today_collections = DeliveryHistory::where('delivery_boy_id', Auth::user()->id)
            ->where('delivery_status', 'delivered')
            ->where('payment_type', 'cash_on_delivery')
            ->paginate(10);

        return view('delivery_boys.total_collection_list', compact('today_collections'));
    }

    /**
     * Show the list of total earning by the delivery boy.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function total_earning()
    {
        $total_earnings = DeliveryHistory::where('delivery_boy_id', Auth::user()->id)
            ->where('delivery_status', 'delivered')
            ->paginate(10);

        return view('delivery_boys.total_earning_list', compact('total_earnings'));
    }

    public function cancel_request($order_id)
    {
        $order = Order::findOrFail($order_id);
        $order->cancel_request = '1';
        $order->cancel_request_at = date("Y-m-d H:i:s");
        $order->save();

        return back();
    }

    /**
     * For only delivery boy while changing delivery status.
     * Call from order controller
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function store_delivery_history($order)
    // {
    //     dd('by');

    //     $delivery_history = new DeliveryHistory;

    //     $delivery_history->order_id         = $order->id;
    //     $delivery_history->delivery_boy_id  = Auth::user()->id;
    //     $delivery_history->delivery_status  = $order->delivery_status;
    //     $delivery_history->payment_type     = $order->payment_type;
    //     if ($order->delivery_status == 'delivered') {
    //         $delivery_boy = DeliveryBoy::where('user_id', Auth::user()->id)->first();

    //         if (get_setting('delivery_boy_payment_type') == 'commission') {
    //             $delivery_history->earning      = get_setting('delivery_boy_commission');
    //             $delivery_boy->total_earning   += get_setting('delivery_boy_commission');
    //         }
    //         if ($order->payment_type == 'cash_on_delivery') {
    //             $delivery_history->collection    = $order->grand_total;
    //             $delivery_boy->total_collection += $order->grand_total;

    //             $order->payment_status           = 'paid';
    //             if ($order->commission_calculated == 0) {
    //                 calculateCommissionAffilationClubPoint($order);
    //                 $order->commission_calculated = 1;
    //             }
    //         }

    //         $delivery_boy->save();
    //     }
    //     $order->delivery_history_date = date("Y-m-d H:i:s");

    //     $order->save();
    //     $delivery_history->save();
    // }

    public function store_delivery_history($order)
    {


        $delivery_history = new DeliveryHistory;
    //  dd($order->delivery_boy);
        $delivery_history->order_id         = $order->id;
        $delivery_history->delivery_boy_id  = $order->delivery_boy;
        $delivery_history->delivery_status  = $order->delivery_status;
        $delivery_history->payment_type     = $order->payment_type;

        if ($order->delivery_status == 'delivered' && $order->delivery_boy != null) {
            $delivery_boy = null;
            if($order->delivery_boy != null){
                $delivery_boy = $order->delivery_boy->id;
            }
            // $delivery_boy = DeliveryBoy::where('user_id', $order->delivery_boy->id)->first();

            if (get_setting('delivery_boy_payment_type') == 'commission') {
                $commission = get_setting('delivery_boy_commission');
                $flat_or_percentage = get_setting('delivery_boy_commission_type');
                if($flat_or_percentage == 'percentage'){
                    $shippingCost = get_setting('flat_rate_shipping_cost');
                    $delivery_boy_commission = ($commission/100) * $shippingCost;

                }
                $delivery_history->earning      =  $delivery_boy_commission;

                $delivery_boy->total_earning   +=  $delivery_boy_commission;
            }
            if ($order->payment_type == 'cash_on_delivery') {
                $delivery_history->collection    = $order->grand_total;
                $delivery_boy->total_collection += $order->grand_total;

                $order->payment_status           = 'paid';
                if ($order->commission_calculated == 0) {
                    calculateCommissionAffilationClubPoint($order);
                    $order->commission_calculated = 1;
                }
            }

            $delivery_boy->save();
        }
        $order->delivery_history_date = date("Y-m-d H:i:s");

        $order->save();
        $delivery_history->save();
    }


    public function returnCollection($id)
    {

        try {
            \DB::beginTransaction();

            $collection = DeliveryBoyCollection::findOrFail($id);

            $delivery_boy = DeliveryBoy::where('user_id',$collection->user_id)->first();

            CollectionReturnHistory::create([
                'delivery_boy_id' => $delivery_boy->id,
                'amount_returned' => $collection->collection_amount,
                'remarks' => 'Amount returned to Shopeedo'
            ]);

            $collection->update(['collection_amount' => 0]);
            $delivery_boy->total_collection = 0;
            $delivery_boy->save();

            \DB::commit();

            Artisan::call('cache:clear');

        flash(translate("Collection returned successfully"))->success();
        return back();

        } catch (\Exception $e) {
            \DB::rollBack();
            flash(translate($e->getMessage()))->error();
        return back();

        }
    }

    public function setDeliveryBoyBanner(Request $request)
{
    $request->validate([
        'photos' => 'required|string',
    ]);

    $existingBanners = BusinessSetting::where('type', 'delivery_boy_banner')->value('value');

    // Convert existing banners into an array and merge with new ones
    $newBanners = explode(',', $request->photos);
    $allBanners = $existingBanners ? array_merge(explode(',', $existingBanners), $newBanners) : $newBanners;
    BusinessSetting::updateOrCreate(
        ['type' => 'delivery_boy_banner'],
        ['value' => implode(',', $allBanners)]
    );

    flash(translate("Banner updated successfully"))->success();
    return back();
}
public function deleteDeliveryBoyBanner($id)
{

    $banners = BusinessSetting::where('type', 'delivery_boy_banner')->value('value');

    if ($banners) {
        $bannerArray = explode(',', $banners);

        $filteredBanners = array_filter($bannerArray, fn($banner) => $banner != $id);

        BusinessSetting::where('type', 'delivery_boy_banner')->update([
            'value' => implode(',', $filteredBanners),
        ]);
    }

    flash(translate("Banner deleted successfully."))->success();
    return back();
}







}
