<?php

namespace Modules\Admin\App\Http\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Modules\User\app\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Models\Config;
use Illuminate\Support\Facades\Log;
use Modules\Booking\app\Models\Booking;
use Modules\Booking\app\Models\BookingDate;
use Modules\Package\app\Models\PackageMessage;
use Modules\Booking\app\Models\BookingCommission;
use Modules\User\app\Models\AgentDetails;
use Dompdf\Dompdf;
use Dompdf\Options;

class AgentService
{
    public function getBookingsByAgentId($customerId)
    {
        $bookings = Booking::where('bookings.customer_id', $customerId)
            ->leftJoin('packages', 'bookings.package_id', '=', 'packages.id')
            ->leftJoin('booking_dates', 'booking_dates.booking_id', '=', 'bookings.id')
            ->leftJoin('booking_commissions', 'booking_commissions.booking_id', '=', 'bookings.id')
            ->leftJoin('agent_details', 'agent_details.user_id', '=', 'bookings.customer_id')
            ->select(
                'bookings.id as booking_id',
                'bookings.*',
                'packages.id as package_id',
                'packages.name as package_name',
                'agent_details.first_name as agent_first_name',
                'agent_details.last_name as agent_last_name',
                'booking_commissions.commission as booking_commission',
                'booking_commissions.group_commission',
                'booking_commissions.commission_amount',
                'booking_commissions.payment_status',
                'booking_commissions.claim_status',
                DB::raw('(SELECT MAX(cost) FROM booking_dates WHERE booking_id = bookings.id) as booking_cost'),
                DB::raw('(SELECT SUM(adults + children) FROM booking_rooms WHERE booking_id = bookings.id) as total_pax')
            )
            ->groupBy(
                'bookings.id',
                'booking_commissions.commission',
                'booking_commissions.group_commission',
                'booking_commissions.commission_amount',
                'booking_commissions.payment_status',
                'booking_commissions.claim_status',
                'agent_details.first_name',
                'agent_details.last_name',
            )
            ->orderBy('bookings.updated_at', 'asc')
            ->get();

        // Map payment status and claim status
        $bookings->each(function ($booking) {
            $booking->payment_status = $this->mapPaymentStatus($booking->payment_status);
            $booking->claim_status = $this->mapClaimStatus($booking->claim_status);
        });

        return $bookings;
    }

    private function mapPaymentStatus($status)
    {
        switch ($status) {
            case 0:
                return 'Open';
            case 1:
                return 'In Process';
            case 2:
                return 'Processed';
            default:
                return 'Unknown';
        }
    }

    private function mapClaimStatus($status)
    {
        switch ($status) {
            case 0:
                return 'Not Claimed';
            case 1:
                return 'Claimed';
            case 2:
                return 'Paid';
            case 3:
                return 'Cancelled';
            default:
                return 'Unknown';
        }
    }

    public function getAgentsData(Request $request)
    {
        $draw = $request->input('draw'); // DataTables draw counter
        $start = $request->input('start'); // Starting index
        $length = $request->input('length'); // Number of records to fetch
        $searchValue = $request->input('search')['value'] ?? ''; // Search value
        $orderColumnIndex = $request->input('order')[0]['column'] ?? 0; // Column index for ordering
        $orderDirection = $request->input('order')[0]['dir'] ?? 'desc'; // Ordering direction

        // Map the column index to actual column names
        $columns = [
            0 => 'last_3_months_paid',
            1 => 'agent_fname',
            2 => null,
            3 => null,
            4 => null,
            5 => null,
            6 => null
        ];

        $orderColumn = $columns[$orderColumnIndex] ?? 'last_3_months_paid';

        $threeMonthsAgo = Carbon::now()->subMonths(3)->startOfMonth();

        $subQuery = BookingCommission::select('user_id', DB::raw("SUM(paid_amount) as last_3_months_paid"))
            ->where('payment_status', 2)
            ->where('payment_date', '>=', $threeMonthsAgo)
            ->groupBy('user_id');

        $query = AgentDetails::select(
            'agent_details.id as user_id',
            'agent_details.first_name as agent_fname',
            'agent_details.last_name as agent_lname',
            'agent_details.agent_code',
            'users.mobile',
            'users.email',
            'agent_details.created_at',
            'agent_details.status',
            DB::raw("COALESCE(last_3_months_paid, 0) as last_3_months_paid")
        )
            ->leftJoin('users', 'agent_details.user_id', '=', 'users.id')
            ->leftJoin(DB::raw("({$subQuery->toSql()}) as commissions"), 'agent_details.id', '=', 'commissions.user_id')
            ->mergeBindings($subQuery->getQuery())
            ->where('users.user_type', User::ROLE_AGENT);

        if ($searchValue) {
            $query->where(function($q) use ($searchValue) {
                $q->where('agent_details.first_name', 'like', "%{$searchValue}%")
                    ->orWhere('agent_details.last_name', 'like', "%{$searchValue}%")
                    ->orWhere('agent_details.agent_code', 'like', "%{$searchValue}%")
                    ->orWhere('users.mobile', 'like', "%{$searchValue}%")
                    ->orWhere('users.email', 'like', "%{$searchValue}%");
            });
        }

        // Count total records without filtering
        $totalRecords = $query->count();

        // Apply ordering
        $query->orderBy($orderColumn, $orderDirection);

        // Count total records after filtering but before pagination
        $filteredRecords = $query->count();

        // Apply pagination
        $query->skip($start)->take($length);

        $agents = $query->get();

        // Process data for DataTables
        $data = $agents->map(function ($agent) {
            //$booking->formatted_date = Carbon::parse($booking->created_at)->format('jS F');
            $agent->fullname = $agent->agent_fname . ' ' . $agent->agent_lname;
            $agent->formatted_date = $agent->created_at ? $agent->created_at->diffForHumans() : '';
            $agent->status = '<span class="no-margin-switcher"><input type="checkbox" class="js-switch toggle-status" data-id="' . $agent->user_id . '" ' . ($agent->status ? 'checked' : '') . '></span>';
            $agent->action = '<a href="' . route('admin.agents.show', $agent->user_id) . '" class="pr-10" data-toggle="tooltip" data-original-title="Details"><i class="fa fa-file-text-o" aria-hidden="true"></i></a>';
            return $agent;
        });

        return [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];
    }

    public function getOpenCommissions(Request $request)
    {
        $draw = $request->input('draw'); // DataTables draw counter
        $start = $request->input('start'); // Starting index
        $length = $request->input('length'); // Number of records to fetch
        $searchValue = $request->input('search')['value']; // Search value
        $orderColumnIndex = $request->input('order')[0]['column']; // Column index for ordering
        $orderDirection = $request->input('order')[0]['dir']; // Ordering direction
        $agentId = $request->input('agentId');

        // Map the column index to actual column names
        $columns = [
            0 => 'agent_details.first_name',
            1 => null,
            2 => 'total_base_price',
            3 => 'total_commission',
            4 => 'total_commission_amount',
            5 => 'total_group_commission_amount',
        ];

        $orderColumn = $columns[$orderColumnIndex] ?? 'agent_details.first_name';

        $query = BookingCommission::leftJoin('agent_details', 'booking_commissions.user_id', '=', 'agent_details.id')
            ->select(
                'booking_commissions.user_id',
                DB::raw("CONCAT(agent_details.first_name, ' ', agent_details.last_name) as fullname"),
                DB::raw("SUM(booking_commissions.base_price) as total_base_price"),
                DB::raw("SUM(booking_commissions.commission_amount) as total_commission"),
                DB::raw("SUM((booking_commissions.base_price * booking_commissions.commission) / 100) as total_commission_amount"),
                DB::raw("SUM((booking_commissions.base_price * booking_commissions.group_commission) / 100) as total_group_commission_amount"),
                DB::raw("DATE_FORMAT(booking_commissions.claimed_date, '%Y-%m') as month")
            )
            ->where('booking_commissions.payment_status', 0)
            ->whereNull('booking_commissions.invoice_number');

        if ($agentId) {
            $query->where('booking_commissions.user_id', $agentId);
        }

        if ($fromMonth = $request->input('from_month')) {
            $fromDate = Carbon::parse($fromMonth)->startOfMonth();
            $query->whereDate('booking_commissions.payment_date', '>=', $fromDate);
        }

        if ($toMonth = $request->input('to_month')) {
            $toDate = Carbon::parse($toMonth)->endOfMonth();
            $query->whereDate('booking_commissions.payment_date', '<=', $toDate);
        }

        if ($searchValue) {
            $query->where(function($q) use ($searchValue) {
                $q->where('agent_details.first_name', 'like', "%{$searchValue}%")
                    ->orWhere('agent_details.last_name', 'like', "%{$searchValue}%");
            });
        }

        // Count total records without filtering
        $totalRecords = $query->count(DB::raw('DISTINCT booking_commissions.invoice_number'));

        // Apply pagination and ordering
        $query->groupBy('booking_commissions.user_id','booking_commissions.invoice_number','booking_commissions.claimed_date')
            ->orderBy(DB::raw($orderColumn), $orderDirection)
            ->skip($start)
            ->take($length);

        $filteredRecords = $query->count();
        $bookings = $query->get();

        // Process data for DataTables
        $data = $bookings->map(function ($booking) {
            $booking->payment_status = $this->mapPaymentStatus($booking->payment_status);
            $booking->commission = round($booking->total_commission_amount);
            $booking->incentive = round($booking->total_group_commission_amount);
            $booking->total_commission = round($booking->total_commission);
            $booking->total_cancelled = 0;
            $booking->net_basic = round($booking->total_base_price) - round($booking->total_cancelled);
            return $booking;
        });

        return [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];
    }

    public function getInvoicedCommissions(Request $request)
    {
        $draw = $request->input('draw'); // DataTables draw counter
        $start = $request->input('start'); // Starting index
        $length = $request->input('length'); // Number of records to fetch
        $searchValue = $request->input('search')['value']; // Search value
        $orderColumnIndex = $request->input('order')[0]['column']; // Column index for ordering
        $orderDirection = $request->input('order')[0]['dir']; // Ordering direction
        $agentId = $request->input('agentId');

        // Map the column index to actual column names
        $columns = [
            0 => 'agent_details.first_name',
            1 => null,
            2 => 'total_base_price',
            3 => 'total_commission',
            4 => 'total_commission_amount',
            5 => 'total_group_commission_amount',
        ];

        $orderColumn = $columns[$orderColumnIndex] ?? 'agent_details.first_name';

        $query = BookingCommission::leftJoin('agent_details', 'booking_commissions.user_id', '=', 'agent_details.id')
            ->select(
                'booking_commissions.user_id',
                DB::raw("CONCAT(agent_details.first_name, ' ', agent_details.last_name) as fullname"),
                DB::raw("SUM(booking_commissions.base_price) as total_base_price"),
                DB::raw("SUM(booking_commissions.commission_amount) as total_commission"),
                DB::raw("SUM((booking_commissions.base_price * booking_commissions.commission) / 100) as total_commission_amount"),
                DB::raw("SUM((booking_commissions.base_price * booking_commissions.group_commission) / 100) as total_group_commission_amount"),
                DB::raw("DATE_FORMAT(booking_commissions.claimed_date, '%Y-%m') as month")
            )
            ->where('booking_commissions.payment_status', 1)
            ->whereNotNull('booking_commissions.invoice_number');

        if ($agentId) {
            $query->where('booking_commissions.user_id', $agentId);
        }

        if ($fromMonth = $request->input('from_month')) {
            $fromDate = Carbon::parse($fromMonth)->startOfMonth();
            $query->whereDate('booking_commissions.payment_date', '>=', $fromDate);
        }

        if ($toMonth = $request->input('to_month')) {
            $toDate = Carbon::parse($toMonth)->endOfMonth();
            $query->whereDate('booking_commissions.payment_date', '<=', $toDate);
        }

        if ($searchValue) {
            $query->where(function($q) use ($searchValue) {
                $q->where('agent_details.first_name', 'like', "%{$searchValue}%")
                    ->orWhere('agent_details.last_name', 'like', "%{$searchValue}%");
            });
        }

        // Count total records without filtering
        $totalRecords = $query->count(DB::raw('DISTINCT booking_commissions.invoice_number'));

        // Apply pagination and ordering
        $query->groupBy('booking_commissions.user_id','booking_commissions.invoice_number','booking_commissions.claimed_date')
            ->orderBy(DB::raw($orderColumn), $orderDirection)
            ->skip($start)
            ->take($length);

        $filteredRecords = $query->count();
        $bookings = $query->get();

        // Process data for DataTables
        $data = $bookings->map(function ($booking) {
            $booking->payment_status = $this->mapPaymentStatus($booking->payment_status);
            $booking->commission = round($booking->total_commission_amount);
            $booking->incentive = round($booking->total_group_commission_amount);
            $booking->total_commission = round($booking->total_commission);
            $booking->total_cancelled = 0;
            $booking->net_basic = round($booking->total_base_price) - round($booking->total_cancelled);
            return $booking;
        });

        return [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];
    }

    public function getProcessedCommissions(Request $request)
    {
        $draw = $request->input('draw'); // DataTables draw counter
        $start = $request->input('start'); // Starting index
        $length = $request->input('length'); // Number of records to fetch
        $searchValue = $request->input('search')['value']; // Search value
        $orderColumnIndex = $request->input('order')[0]['column']; // Column index for ordering
        $orderDirection = $request->input('order')[0]['dir']; // Ordering direction
        $agentId = $request->input('agentId');

        // Map the column index to actual column names
        $columns = [
            0 => 'agent_details.first_name',
            1 => 'total_base_price',
            2 => 'total_commission',
            3 => 'total_commission_amount',
            4 => 'total_group_commission_amount',
        ];

        $orderColumn = $columns[$orderColumnIndex] ?? 'agent_details.first_name';

        $query = BookingCommission::leftJoin('agent_details', 'booking_commissions.user_id', '=', 'agent_details.id')
            ->select(
                'booking_commissions.user_id',
                'booking_commissions.voucher_number',
                'booking_commissions.invoice_number',
                DB::raw("DATE_FORMAT(booking_commissions.payment_date, '%Y-%m') as month"),
                DB::raw("CONCAT(agent_details.first_name, ' ', agent_details.last_name) as fullname"),
                DB::raw("SUM(booking_commissions.base_price) as total_base_price"),
                DB::raw("SUM(booking_commissions.commission_amount) as total_commission"),
                DB::raw("SUM((booking_commissions.base_price * booking_commissions.commission) / 100) as total_commission_amount"),
                DB::raw("SUM((booking_commissions.base_price * booking_commissions.group_commission) / 100) as total_group_commission_amount")
            )
            ->where('booking_commissions.payment_status', 2)
            ->whereNotNull('booking_commissions.voucher_number');

        if ($agentId) {
            $query->where('booking_commissions.user_id', $agentId);
        }

        if ($fromMonth = $request->input('from_month')) {
            $fromDate = Carbon::parse($fromMonth)->startOfMonth();
            $query->whereDate('booking_commissions.payment_date', '>=', $fromDate);
        }

        if ($toMonth = $request->input('to_month')) {
            $toDate = Carbon::parse($toMonth)->endOfMonth();
            $query->whereDate('booking_commissions.payment_date', '<=', $toDate);
        }

        if ($searchValue) {
            $query->where(function($q) use ($searchValue) {
                $q->where('agent_details.first_name', 'like', "%{$searchValue}%")
                    ->orWhere('agent_details.last_name', 'like', "%{$searchValue}%");
            });
        }


// Apply pagination and ordering
        $query->groupBy(
            'booking_commissions.user_id',
            'booking_commissions.voucher_number',
            'booking_commissions.invoice_number',
            DB::raw("DATE_FORMAT(booking_commissions.payment_date, '%Y-%m')"),
            'agent_details.first_name',
            'agent_details.last_name'
        );

// Count total records without filtering
        $totalRecords = $query->count();

        $query->orderBy(DB::raw($orderColumn), $orderDirection)
            ->skip($start)
            ->take($length);

        $filteredRecords = $query->count();
        $bookings = $query->get();

        // Process data for DataTables
        $data = $bookings->map(function ($booking) {
            $booking->payment_status = $this->mapPaymentStatus($booking->payment_status);
            $booking->commission = round($booking->total_commission_amount);
            $booking->incentive = round($booking->total_group_commission_amount);
            $booking->total_commission = round($booking->total_commission); // Using already summed commission_amount
            $tds = $booking->total_commission * 0.01;
            $booking->tds = round($tds);
            $booking->total_paid = round($booking->total_commission - $tds);
            $voucher_number = $booking->voucher_number;
            $booking->voucher_url = url('storage/app/public/vouchers/' . $voucher_number.'.pdf');
            $invoice_number = $booking->invoice_number;
            $booking->invoice_url = url('storage/app/public/invoiceAgent/' . $invoice_number.'.pdf');

            return $booking;
        });

        return [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];
    }

    public function getAgentOpenCommissions(Request $request)
    {
        $draw = $request->input('draw'); // DataTables draw counter
        $start = $request->input('start'); // Starting index
        $length = $request->input('length'); // Number of records to fetch
        $searchValue = $request->input('search')['value'] ?? ''; // Search value
        $orderColumnIndex = $request->input('order')[0]['column'] ?? 0; // Column index for ordering
        $orderDirection = $request->input('order')[0]['dir'] ?? 'desc'; // Ordering direction
        $agentId = $request->input('agentId');

        // Map the column index to actual column names
        $columns = [
            0 => 'booking_commissions.created_at',
            1 => null,
            2 => 'basic_amount',
            3 => 'cancelled_amount',
            4 => null,
            5 => 'commission',
            6 => 'group_commission',
            7 => 'total_commission',
        ];

        $orderColumn = $columns[$orderColumnIndex] ?? 'booking_commissions.created_at';

        $query = BookingCommission::join('bookings', 'bookings.id', '=', 'booking_commissions.booking_id')
            ->leftJoin('booking_cancellations', 'booking_cancellations.booking_id', '=', 'booking_commissions.booking_id')
            ->select(
                'booking_commissions.created_at',
                'booking_commissions.booking_id',
                'bookings.booking_number',
                'booking_commissions.base_price as basic_amount',
                DB::raw("COALESCE((SELECT SUM(refund_amount - gst_charge) FROM booking_cancellations WHERE booking_cancellations.booking_id = bookings.id), 0) as cancelled_amount"),
                DB::raw("(booking_commissions.base_price * booking_commissions.commission) / 100 as commission"),
                DB::raw("(booking_commissions.base_price * booking_commissions.group_commission) / 100 as group_commission"),
                'booking_commissions.commission_amount as total_commission'
            )
            //->where('booking_commissions.payment_status', 0)
            ->whereNull('booking_commissions.invoice_number');

        if ($agentId) {
            $query->where('booking_commissions.user_id', $agentId);
        }

        if ($fromMonth = $request->input('from_month')) {
            $fromDate = Carbon::parse($fromMonth)->startOfMonth();
            $query->whereDate('booking_commissions.payment_date', '>=', $fromDate);
        }

        if ($toMonth = $request->input('to_month')) {
            $toDate = Carbon::parse($toMonth)->endOfMonth();
            $query->whereDate('booking_commissions.payment_date', '<=', $toDate);
        }

        if ($searchValue) {
            $query->where(function($q) use ($searchValue) {
                $q->where('agent_details.first_name', 'like', "%{$searchValue}%")
                    ->orWhere('agent_details.last_name', 'like', "%{$searchValue}%")
                    ->orWhere('booking_commissions.booking_number', 'like', "%{$searchValue}%");
            });
        }

        // Count total records without filtering
        $totalRecords = $query->count();

        // Apply ordering by default
        $query->orderBy('booking_commissions.created_at', 'desc')
            ->orderBy($orderColumn, $orderDirection);

        // Count total records after filtering but before pagination
        $filteredRecords = $query->count();

        // Apply pagination
        $query->skip($start)->take($length);

        $bookings = $query->get();

        // Process data for DataTables
        $data = $bookings->map(function ($booking) {
            $booking->formatted_date = Carbon::parse($booking->created_at)->format('jS F');
            $booking->net_amount = round($booking->basic_amount) - round($booking->cancelled_amount);
            return $booking;
        });

        return [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];
    }

    public function getAgentInvoicedCommissions(Request $request)
    {
        $draw = $request->input('draw'); // DataTables draw counter
        $start = $request->input('start'); // Starting index
        $length = $request->input('length'); // Number of records to fetch
        $searchValue = $request->input('search')['value'] ?? ''; // Search value
        $orderColumnIndex = $request->input('order')[0]['column'] ?? 0; // Column index for ordering
        $orderDirection = $request->input('order')[0]['dir'] ?? 'desc'; // Ordering direction
        $agentId = $request->input('agentId');

        // Map the column index to actual column names
        $columns = [
            0 => 'booking_commissions.created_at',
            1 => null,
            2 => 'basic_amount',
            3 => 'cancelled_amount',
            4 => null,
            5 => 'commission',
            6 => 'group_commission',
            7 => 'total_commission',
        ];

        $orderColumn = $columns[$orderColumnIndex] ?? 'booking_commissions.created_at';

        $query = BookingCommission::join('bookings', 'bookings.id', '=', 'booking_commissions.booking_id')
            ->leftJoin('booking_cancellations', 'booking_cancellations.booking_id', '=', 'booking_commissions.booking_id')
            ->select(
                'booking_commissions.created_at',
                'booking_commissions.booking_id',
                'bookings.booking_number',
                'booking_commissions.base_price as basic_amount',
                DB::raw("(SELECT COALESCE(SUM(refund_amount - gst_charge), 0) FROM booking_cancellations WHERE booking_cancellations.booking_id = bookings.id) as cancelled_amount"),
                DB::raw("(booking_commissions.base_price * booking_commissions.commission) / 100 as commission"),
                DB::raw("(booking_commissions.base_price * booking_commissions.group_commission) / 100 as group_commission"),
                'booking_commissions.commission_amount as total_commission'
            )
            //->where('booking_commissions.payment_status', 0)
            ->whereNull('booking_commissions.voucher_number');

        if ($agentId) {
            $query->where('booking_commissions.user_id', $agentId);
        }

        if ($fromMonth = $request->input('from_month')) {
            $fromDate = Carbon::parse($fromMonth)->startOfMonth();
            $query->whereDate('booking_commissions.payment_date', '>=', $fromDate);
        }

        if ($toMonth = $request->input('to_month')) {
            $toDate = Carbon::parse($toMonth)->endOfMonth();
            $query->whereDate('booking_commissions.payment_date', '<=', $toDate);
        }

        if ($searchValue) {
            $query->where(function($q) use ($searchValue) {
                $q->where('agent_details.first_name', 'like', "%{$searchValue}%")
                    ->orWhere('agent_details.last_name', 'like', "%{$searchValue}%")
                    ->orWhere('booking_commissions.booking_number', 'like', "%{$searchValue}%");
            });
        }

        // Count total records without filtering
        $totalRecords = $query->count();

        // Apply ordering by default
        $query->orderBy('booking_commissions.created_at', 'desc')
            ->orderBy($orderColumn, $orderDirection);

        // Count total records after filtering but before pagination
        $filteredRecords = $query->count();

        // Apply pagination
        $query->skip($start)->take($length);

        $bookings = $query->get();

        // Process data for DataTables
        $data = $bookings->map(function ($booking) {
            $booking->formatted_date = Carbon::parse($booking->created_at)->format('jS F');
            $booking->net_amount = round($booking->basic_amount) - round($booking->cancelled_amount);
            return $booking;
        });

        return [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];
    }

    public function getAgentProcessedCommissions(Request $request)
    {
        $draw = $request->input('draw'); // DataTables draw counter
        $start = $request->input('start'); // Starting index
        $length = $request->input('length'); // Number of records to fetch
        $searchValue = $request->input('search')['value']; // Search value
        $orderColumnIndex = $request->input('order')[0]['column']; // Column index for ordering
        $orderDirection = $request->input('order')[0]['dir']; // Ordering direction
        $agentId = $request->input('agentId');

        // Map the column index to actual column names
        $columns = [
            0 => 'booking_commissions.payment_date',
            1 => 'total_base_price',
            2 => 'total_commission',
            3 => 'total_commission_amount',
            4 => 'total_group_commission_amount',
        ];

        $orderColumn = $columns[$orderColumnIndex] ?? 'agent_details.first_name';

        $query = BookingCommission::leftJoin('booking_cancellations', 'booking_cancellations.booking_id', '=', 'booking_commissions.booking_id')
            ->select(
                'booking_commissions.invoice_number',
                'booking_commissions.voucher_number',
                DB::raw("SUM(booking_commissions.base_price) as basic_amount"),
                DB::raw("COALESCE(SUM(booking_cancellations.refund_amount - booking_cancellations.gst_charge), 0) as cancelled_amount"),
                DB::raw("SUM(booking_commissions.commission_amount) as total_commission"),
                DB::raw("SUM((booking_commissions.base_price * booking_commissions.commission) / 100) as total_commission_amount"),
                DB::raw("SUM((booking_commissions.base_price * booking_commissions.group_commission) / 100) as total_group_commission_amount"),
                DB::raw("DATE_FORMAT(booking_commissions.payment_date, '%Y-%m') as month")
            )
            ->where('booking_commissions.payment_status', 2)
            ->whereNotNull('booking_commissions.voucher_number');

        if ($agentId) {
            $query->where('booking_commissions.user_id', $agentId);
        }

        if ($fromMonth = $request->input('from_month')) {
            $fromDate = Carbon::parse($fromMonth)->startOfMonth();
            $query->whereDate('booking_commissions.payment_date', '>=', $fromDate);
        }

        if ($toMonth = $request->input('to_month')) {
            $toDate = Carbon::parse($toMonth)->endOfMonth();
            $query->whereDate('booking_commissions.payment_date', '<=', $toDate);
        }

//        if ($searchValue) {
//            $query->where(function($q) use ($searchValue) {
//                $q->where('DATE_FORMAT(booking_commissions.payment_date, "%Y-%m") as month', 'like', "%{$searchValue}%");
//            });
//        }

        // Count total records without filtering
        $totalRecords = $query->count(DB::raw('DISTINCT booking_commissions.invoice_number'));

        // Apply pagination and ordering
        $query->groupBy('booking_commissions.invoice_number','booking_commissions.voucher_number','booking_commissions.payment_date')
            ->orderBy(DB::raw($orderColumn), $orderDirection)
            ->skip($start)
            ->take($length);

        $filteredRecords = $query->count(DB::raw('DISTINCT booking_commissions.invoice_number'));
        $bookings = $query->get();



        // Process data for DataTables
        $data = $bookings->map(function ($booking) {
            $voucher_number = $booking->voucher_number;
            $voucherUrl = url('storage/app/public/vouchers/' . $voucher_number.'.pdf');
            $invoice_number = $booking->invoice_number;
            $invoiceUrl = url('storage/app/public/invoiceAgent/' . $invoice_number.'.pdf');

            $booking->payment_status = $this->mapPaymentStatus($booking->payment_status);
            $booking->basic_amount = round($booking->basic_amount);
            $booking->net_amount = round($booking->basic_amount) - round($booking->cancelled_amount);
            $booking->commission = round($booking->total_commission_amount);
            $booking->incentive = round($booking->total_group_commission_amount);
            $booking->total_commission = round($booking->total_commission); // Using already summed commission_amount
            $tds = $booking->total_commission * 0.01;
            $booking->tds = round($tds);
            $booking->total_paid = round($booking->total_commission - $tds);
            $booking->voucher = $voucherUrl;
            $booking->invoice = $invoiceUrl;
            return $booking;
        });

        return [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];
    }

    public function processCommissions(Request $request)
    {
        $selectedIds  = $request->input('ids');
        $paymentDate = Carbon::now()->toDateString();

        if (empty($selectedIds)) {
            return ['res'=> false,'msg' => 'No agents selected for payout.','data'=>''];
        }

        $currentDateTime = Carbon::now()->format('dmyHis');
        foreach ($selectedIds as $selectedId) {
            $voucherNumber = 'WMTV' . $selectedId . $currentDateTime;

            $commissions = BookingCommission::where('user_id', $selectedId)
                ->where('payment_status', 1)
                ->whereNotNull('invoice_number')
                ->get();

            foreach ($commissions as $commission) {
                $paid_amount = round(($commission->commission_amount - $commission->tds_amount),2);

                $commission->update([
                    'payment_status' => 2,
                    'claim_status' => 2,
                    'payment_date' => $paymentDate,
                    'voucher_number' => $voucherNumber,
                    'paid_amount' => $paid_amount
                ]);
            }
        }

        return ['res'=> true,'msg' => 'Payout processed successfully.','data'=>''];
    }

    public function getAgentLedger(Request $request)
    {
        $agentId = $request->input('agent_id');
        $fromMonth = $request->input('from_month');
        $toMonth = $request->input('to_month');

        // Define the start and end dates
        if (!$fromMonth && !$toMonth) {
            $startDate = Carbon::now()->subMonth ()->startOfMonth();
            $endDate = Carbon::now()->subMonth()->endOfMonth();
        } else {
            $startDate = $fromMonth ? Carbon::parse($fromMonth)->startOfMonth() : Carbon::now()->subMonth()->startOfMonth();
            $endDate = $toMonth ? Carbon::parse($toMonth)->endOfMonth() : Carbon::now()->endOfMonth();
        }

        $transactions = $this->getTransactions($agentId, $startDate, $endDate);

        return ['data' => $transactions];
    }

    private function getTransactions($agentId, $startDate, $endDate)
    {
        $transactions = collect();

        // Get opening balance
        $previousDate = Carbon::parse($startDate)->subDay()->toDateString();
        $previousTransactions = $this->getPreviousTransactions($agentId, $previousDate);
        $openingBalance = $this->calculateClosingBalance($previousTransactions);

        // Add opening balance entry
        $transactions->push((object)[
            'date' => Carbon::parse($startDate)->format('Y-m-d'),
            'description' => 'Opening Balance',
            'debit' => $openingBalance < 0 ? abs($openingBalance) : null,
            'credit' => $openingBalance >= 0 ? $openingBalance : null,
        ]);

        // Get commissions grouped by invoice_number
        $commissions = BookingCommission::where('user_id', $agentId)
            ->whereBetween('claimed_date', [$startDate, $endDate])
            ->whereNotNull('invoice_number')
            ->groupBy('invoice_number', 'claimed_date')
            ->select(
                'claimed_date as date',
                DB::raw('SUM(commission_amount) as credit'),
                DB::raw('NULL as debit'),
                'invoice_number as reference_number',
                DB::raw("CONCAT('Commission for ', DATE_FORMAT(claimed_date, '%M %Y')) as description")
            )
            ->get();

        // Add commission entries and separate TDS entries
        $commissions->each(function ($commission) use ($transactions, $startDate) {
            $transactions->push($commission);

            $tds = (object)[
                'date' => $commission->date,
                'reference_number' => $commission->reference_number,
                'description' => 'TDS for ' . Carbon::parse($commission->date)->format('F Y'),
                'debit' => $commission->credit * 0.01, // Assuming TDS is 1% of commission
                'credit' => null
            ];

            $transactions->push($tds);
        });

        // Get payouts grouped by voucher_number
        $payouts = BookingCommission::where('user_id', $agentId)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->whereNotNull('voucher_number')
            ->groupBy('voucher_number', 'payment_date')
            ->select(
                'payment_date as date',
                DB::raw('SUM(paid_amount) as debit'),
                DB::raw('NULL as credit'),
                'voucher_number as reference_number',
                DB::raw("'Payout' as description")
            )
            ->get();

        // Add payout entries
        $payouts->each(function ($payout) use ($transactions) {
            $transactions->push($payout);
        });

        // Calculate closing balance
        $closingBalance = $this->calculateClosingBalance($transactions);
        // Add closing balance entry
//        $transactions->push((object)[
//            'date' => Carbon::parse($endDate)->format('Y-m-d'),
//            'description' => 'Closing Balance',
//            'debit' => $closingBalance < 0 ? abs($closingBalance) : null,
//            'credit' => $closingBalance >= 0 ? $closingBalance : null,
//        ]);
        $transactions->push((object)[
            'date' => Carbon::parse($endDate)->format('Y-m-d'),
            'description' => 'Closing Balance',
            'debit' => $closingBalance > 0 ? $closingBalance : null,
            'credit' => $closingBalance < 0 ? abs($closingBalance) : null,
        ]);

        return $transactions->sortBy('date')->values()->all();
    }

    private function getPreviousTransactions($agentId, $date)
    {
        // Get all transactions up to the specified date
        $commissions = BookingCommission::where('user_id', $agentId)
            ->where('claimed_date', '<=', $date)
            ->whereNotNull('invoice_number')
            ->groupBy('invoice_number', 'claimed_date')
            ->select(
                'claimed_date as date',
                DB::raw('SUM(commission_amount) as credit'),
                DB::raw('SUM(commission_amount) * 0.01 as debit'),
                'invoice_number as reference_number',
                DB::raw("'Commission' as description")
            )
            ->get();

        $payouts = BookingCommission::where('user_id', $agentId)
            ->where('payment_date', '<=', $date)
            ->whereNotNull('voucher_number')
            ->groupBy('voucher_number', 'payment_date')
            ->select(
                'payment_date as date',
                DB::raw('SUM(paid_amount) as debit'),
                DB::raw('NULL as credit'),
                'voucher_number as reference_number',
                DB::raw("'Payout' as description")
            )
            ->get();

        return $commissions->merge($payouts)->sortBy('date')->values();
    }

    private function calculateClosingBalance($transactions)
    {
        $balance = 0;
        foreach ($transactions as $transaction) {
            $balance += $transaction->credit ?? 0;
            $balance -= $transaction->debit ?? 0;
        }
        return $balance;
    }

    public function getAgentLedgerx(Request $request)
    {
        $agentId = $request->input('agent_id');
        $fromMonth = $request->input('from_month');
        $toMonth = $request->input('to_month');

        // Define the start and end dates
        if (!$fromMonth && !$toMonth) {
            $start = Carbon::now()->subMonth()->startOfMonth();
            $end = Carbon::now()->subMonth()->endOfMonth();
        } else {
            $start = $fromMonth ? Carbon::parse($fromMonth)->startOfMonth() : Carbon::now()->subMonth()->startOfMonth();
            $end = $toMonth ? Carbon::parse($toMonth)->endOfMonth() : Carbon::now()->endOfMonth();
        }

        $ledger = [];
        $openingBalance = 0;
        $closingBalance = $openingBalance;

        // Add the initial opening balance entry
        $ledger[] = [
            'date' => $start->format('Y-m-d'),
            'description' => 'Opening Balance',
            'amount' => $openingBalance,
            'type' => 'credit' // Initial balance for the admin is 0 (credit)
        ];

        // Loop through each month in the date range
        while ($start->lessThanOrEqualTo($end)) {
            $currentMonthStart = $start->copy()->startOfMonth();
            $currentMonthEnd = $start->copy()->endOfMonth();

            // Retrieve commissions for the current month
            $commissions = BookingCommission::where('user_id', $agentId)
                ->where('payment_status', 1)
                ->whereBetween('claimed_date', [$currentMonthStart, $currentMonthEnd])
                ->select(
                    DB::raw('ROUND(SUM(commission_amount), 2) as total_commission'),
                    DB::raw('ROUND(SUM(commission_amount * 0.01), 2) as total_tds')
                )
                ->first();

            // Retrieve payouts for the current month
            $payouts = BookingCommission::where('user_id', $agentId)
                ->where('payment_status', 2)
                ->whereBetween('payment_date', [$currentMonthStart, $currentMonthEnd])
                ->select(
                    DB::raw('ROUND(SUM(paid_amount), 2) as total_payout'),
                    DB::raw('MAX(payment_date) as payment_date')
                )
                ->first();

            // Add commission entry to the ledger
            if ($commissions->total_commission) {
                $ledger[] = [
                    'date' => $currentMonthEnd->format('Y-m-d'),
                    'description' => 'Total Commission for ' . $currentMonthEnd->format('F Y'),
                    'amount' => $commissions->total_commission,
                    'type' => 'credit' // Commissions are credit
                ];

                $closingBalance += $commissions->total_commission;

                // Add TDS entry to the ledger
                $ledger[] = [
                    'date' => $currentMonthEnd->format('Y-m-d'),
                    'description' => 'Total TDS for ' . $currentMonthEnd->format('F Y'),
                    'amount' => $commissions->total_tds,
                    'type' => 'debit' // TDS is debit
                ];

                $closingBalance -= $commissions->total_tds;
            }

            // Add payout entry to the ledger
            if ($payouts->total_payout) {
                $ledger[] = [
                    'date' => Carbon::parse($payouts->payment_date)->toDateString(),
                    'description' => 'Payment',
                    'amount' => $payouts->total_payout,
                    'type' => 'debit' // Payouts are debit
                ];

                $closingBalance -= $payouts->total_payout;
            }

            // Move to the next month
            $start->addMonth();

            // Add the closing balance entry for the current month
            $ledger[] = [
                'date' => $currentMonthEnd->format('Y-m-d'),
                'description' => 'Closing Balance',
                'amount' => number_format($closingBalance, 2, '.', ''),
                'type' => 'credit' // Closing balance for the month
            ];

            // Add the opening balance entry for the next month
            $ledger[] = [
                'date' => $start->format('Y-m-d'),
                'description' => 'Opening Balance',
                'amount' => number_format($closingBalance, 2, '.', ''),
                'type' => 'debit' // Opening balance for the next month
            ];
        }

        return ['data' => $ledger];
    }


    public function getAgentLedgers(Request $request)
    {
        $draw = $request->input('draw'); // DataTables draw counter
        $start = $request->input('start'); // Starting index
        $length = $request->input('length'); // Number of records to fetch
        $searchValue = $request->input('search')['value'] ?? ''; // Search value
        $orderColumnIndex = $request->input('order')[0]['column'] ?? 0; // Column index for ordering
        $orderDirection = $request->input('order')[0]['dir'] ?? 'desc'; // Ordering direction

        // Map the column index to actual column names
        $columns = [
            0 => 'last_3_months_paid',
            1 => 'agent_fname',
            2 => null,
            3 => null,
            4 => null,
            5 => null,
            6 => null
        ];

        $orderColumn = $columns[$orderColumnIndex] ?? 'last_3_months_paid';

        $threeMonthsAgo = Carbon::now()->subMonths(3)->startOfMonth();

        $subQuery = BookingCommission::select('user_id', DB::raw("SUM(paid_amount) as last_3_months_paid"))
            ->where('payment_status', 2)
            ->where('payment_date', '>=', $threeMonthsAgo)
            ->groupBy('user_id');

        $query = AgentDetails::select(
            'agent_details.id as user_id',
            'agent_details.first_name as agent_fname',
            'agent_details.last_name as agent_lname',
            'agent_details.agent_code',
            'users.mobile',
            'users.email',
            'agent_details.created_at',
            'agent_details.status',
            DB::raw("COALESCE(last_3_months_paid, 0) as last_3_months_paid")
        )
            ->leftJoin('users', 'agent_details.user_id', '=', 'users.id')
            ->leftJoin(DB::raw("({$subQuery->toSql()}) as commissions"), 'agent_details.id', '=', 'commissions.user_id')
            ->mergeBindings($subQuery->getQuery())
            ->where('users.user_type', User::ROLE_AGENT);

        if ($searchValue) {
            $query->where(function($q) use ($searchValue) {
                $q->where('agent_details.first_name', 'like', "%{$searchValue}%")
                    ->orWhere('agent_details.last_name', 'like', "%{$searchValue}%")
                    ->orWhere('agent_details.agent_code', 'like', "%{$searchValue}%")
                    ->orWhere('users.mobile', 'like', "%{$searchValue}%")
                    ->orWhere('users.email', 'like', "%{$searchValue}%");
            });
        }

        // Count total records without filtering
        $totalRecords = $query->count();

        // Apply ordering
        $query->orderBy($orderColumn, $orderDirection);

        // Count total records after filtering but before pagination
        $filteredRecords = $query->count();

        // Apply pagination
        $query->skip($start)->take($length);

        $agents = $query->get();

        // Process data for DataTables
        $data = $agents->map(function ($agent) {
            //$booking->formatted_date = Carbon::parse($booking->created_at)->format('jS F');
            $agent->fullname = $agent->agent_fname . ' ' . $agent->agent_lname;
            $agent->formatted_date = $agent->created_at ? $agent->created_at->diffForHumans() : '';
            $agent->status = '<span class="no-margin-switcher"><input type="checkbox" class="js-switch toggle-status" data-id="' . $agent->user_id . '" ' . ($agent->status ? 'checked' : '') . '></span>';
            $agent->action = '<a href="' . route('admin.agents.show', $agent->user_id) . '" class="pr-10" data-toggle="tooltip" data-original-title="Details"><i class="fa fa-file-text-o" aria-hidden="true"></i></a>';
            return $agent;
        });

        return [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];
    }

    public function generateLedgerPdf(Request $request)
    {
        $agentId = $request->input('agent_id');
        $fromMonth = $request->input('from_month');
        $toMonth = $request->input('to_month');

        // Define the start and end dates
        if (!$fromMonth && !$toMonth) {
            $startDate = Carbon::now()->subMonth()->startOfMonth();
            $endDate = Carbon::now()->subMonth()->endOfMonth();
        } else {
            $startDate = $fromMonth ? Carbon::parse($fromMonth)->startOfMonth() : Carbon::now()->subMonth()->startOfMonth();
            $endDate = $toMonth ? Carbon::parse($toMonth)->endOfMonth() : Carbon::now()->endOfMonth();
        }
        // Fetch from details
        $companyName = Config::where('name', 'company_name')->value('value');
        $companyAddress = Config::where('name', 'company_address')->value('value');
        $fromInfo ='<strong>'.$companyName.'</strong><br/>'.$companyAddress;

        // Fetch the agent details
        $agent = AgentDetails::select(
            'agent_details.*',
            'users.email',
            'users.mobile'
        )
            ->join('users', 'agent_details.user_id', '=', 'users.id')
            ->where('agent_details.id', $agentId)
            ->first();
        $agentName = $agent->first_name . ' ' . $agent->last_name;
        $agentPAN = $agent->pan_number;
        $agentMobile = $agent->mobile;
        $agentEmail = $agent->email;
        $toInfo ='<strong>'.$agentName.'</strong><br/>'.$agentPAN.'<br/>'.$agentMobile.'<br/>'.$agentEmail;

        // Fetch the ledger transactions
        $transactions = $this->getTransactions($agentId, $startDate, $endDate);

        // Calculate total debit and credit
        $totalDebit = collect($transactions)->sum('debit');
        $totalCredit = collect($transactions)->sum('credit');


        $dompdf = new Dompdf();
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf->setOptions($options);

        $html = view('admin-ledger-pdf', compact(
            'fromInfo', 'toInfo', 'transactions', 'totalDebit', 'totalCredit', 'startDate', 'endDate'
        ))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream('ledger_' . $agentId . '.pdf');
    }
}
