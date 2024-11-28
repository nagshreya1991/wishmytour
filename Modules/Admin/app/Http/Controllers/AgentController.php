<?php

namespace Modules\Admin\app\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\User\app\Models\VendorDetail;
use Modules\User\app\Models\Vendor;
use Modules\User\app\Models\CommissionGroup;
use App\Helpers\NotificationHelper;
use App\Helpers\Helper;
use Modules\Admin\app\Http\Services\AgentService;
use App\Models\Notification;
use Modules\Booking\app\Models\BookingDate;
use Modules\User\app\Models\AgentDetails;
use Modules\Booking\app\Models\BookingCommission;


class AgentController extends Controller
{
    protected $agentService;

    public function __construct(AgentService $agentService)
    {
        $this->agentService = $agentService;
    }

    /**
     * Display the list of agents.
     *
     * @return View
     */
    public function index()
    {
        return view('backend.agents.index');
    }

    /**
     * Retrieve and return agents data in JSON format.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAgentsData(Request $request)
    {
        $openCommissions = $this->agentService->getAgentsData($request);

        return response()->json($openCommissions);
    }

    /**
     * Display detailed information for a specific agent.
     *
     * @param int $id
     * @return View
     */
    public function agentShow($id)
    {
        $agent = AgentDetails::select(
            'agent_details.*',
            'agent_details.first_name as agent_fname',
            'agent_details.last_name as agent_lname',
            'users.name as user_name',
            'users.id as user_id',
            'users.email',
            'users.mobile'
        )
            ->join('users', 'agent_details.user_id', '=', 'users.id')
            ->where('agent_details.id', $id)
            ->first();

        if (!$agent) {
            return redirect()->back()->with('error', 'Agent not found');
        }
        $agent->joined_at = Carbon::parse($agent->created_at)->format('l, jS F Y h:i A');

        return view('backend.agents.show', compact('agent'));
    }

    /**
     * Update agent details and send notifications if statuses have changed.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function agentUpdate(Request $request, int $id): RedirectResponse
    {
        $agentDetails = AgentDetails::findOrFail($id);

        $agentDetails->bank_verified = $request->bank_verified ? 1 : 0;
        $agentDetails->is_verified = $request->is_verified ? 1 : 0;
        $agentDetails->save();

        return redirect()->route('admin.agents')->with('success', 'Agent Details updated successfully');
    }

    /**
     * Retrieve and display agent commission details.
     *
     * @param int $agentId
     * @return View
     */
    public function agentCommissions(int $agentId): View
    {
        $agent = AgentDetails::find($agentId);
        $totalBookings = 0;
        $totalPaidCommissions = 0;
        $last3Months = 0;
        $totalDueCommissions = 0;
        if ($agent) {
            // Fetch total bookings for the agent
            $totalBookings = BookingCommission::where('user_id', $agentId)->count();

            // Fetch total paid commissions for the agent
            $totalPaidCommissions = BookingCommission::where('user_id', $agentId)
                ->where('payment_status', 2)
                ->sum('paid_amount');

            // Fetch total due commissions for the agent
            $totalDueCommissions = BookingCommission::where('user_id', $agentId)
                ->where('payment_status','!=', 2)
                ->sum('commission_amount');

            $endDate = Carbon::now()->startOfMonth()->subDay(); // Last day of the previous month
            $startDate = $endDate->copy()->subMonths(3)->startOfMonth(); // First day of the month, three months ago
            $last3Months = BookingCommission::where('user_id', $agentId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('paid_amount');
        }

        return view('backend.agents.commissions', compact('agent', 'totalBookings', 'totalPaidCommissions', 'last3Months', 'totalDueCommissions'));
    }

    /**
     * Retrieve and display agent commission details.
     *
     * @param int $agentId
     * @return View
     */
    public function agentCommissionDetails(int $agentId): View
    {
        return view('backend.agents.commissions', compact('agentId'));
    }

    /**
     * Process payout for a specific booking commission.
     *
     * @param int $id
     * @param Request $request
     * @return RedirectResponse
     */
    public function agentPayout($id, Request $request)
    {
        try {
            $booking = BookingCommission::where('booking_id', $id)->firstOrFail();

            // Update claim status to 2 (assuming 2 means "Payout")
            $booking->claim_status = 2;
            $booking->payment_status = 2;
            $booking->payment_date = now();
            $booking->save();

            $agentDetail = AgentDetails::where('user_id', $booking->user_id)->first();
            $agentFullName = $agentDetail->first_name . ' ' . $agentDetail->last_name;
            $notificationMessage = "Admin paid commission for Booking Id: #$id";

            Helper::sendNotification($booking->user_id, $notificationMessage);

            return redirect()->route('admin.booking.commissions', ['id' => $booking->user_id])
                ->with('success', 'Booking payout successfully.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.booking.commissions', ['id' => $booking->user_id])
                ->with('error', 'Booking not found.');
        } catch (\Exception $e) {
            return redirect()->route('admin.booking.commissions', ['id' => $booking->user_id])
                ->with('error', 'An error occurred while processing the payout.');
        }
    }

    /**
     * Display the commissions page.
     *
     * @param Request $request
     * @return View
     */
    public function commissions(Request $request): View
    {
        return view('backend.booking.commissions');
    }

    /**
     * Retrieve and return open commissions in JSON format.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getOpenCommissions(Request $request): JsonResponse
    {
        $openCommissions = $this->agentService->getOpenCommissions($request);

        return response()->json($openCommissions);
    }

    /**
     * Retrieve and return invoiced commissions in JSON format.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getInvoicedCommissions(Request $request): JsonResponse
    {
        $invoicedCommissions = $this->agentService->getInvoicedCommissions($request);

        return response()->json($invoicedCommissions);
    }

    /**
     * Retrieve and return processed commissions in JSON format.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getProcessedCommissions(Request $request): JsonResponse
    {
        $processedCommissions = $this->agentService->getProcessedCommissions($request);

        return response()->json($processedCommissions);
    }

    /**
     * Retrieve and return open commissions for a specific agent in JSON format.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAgentOpenCommissions(Request $request): JsonResponse
    {
        $openCommissions = $this->agentService->getAgentOpenCommissions($request);

        return response()->json($openCommissions);
    }

    /**
     * Retrieve and return invoiced commissions for a specific agent in JSON format.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAgentInvoicedCommissions(Request $request): JsonResponse
    {
        $invoicedCommissions = $this->agentService->getAgentInvoicedCommissions($request);

        return response()->json($invoicedCommissions);
    }

    /**
     * Retrieve and return processed commissions for a specific agent in JSON format.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAgentProcessedCommissions(Request $request): JsonResponse
    {
        $processedCommissions = $this->agentService->getAgentProcessedCommissions($request);

        return response()->json($processedCommissions);
    }

    /**
     * Process commissions based on the request data.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function processCommissions(Request $request): JsonResponse
    {
        $result = $this->agentService->processCommissions($request);

        return response()->json($result);
    }

    /**
     * Toggle the status of an agent.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function agentToggleStatus(Request $request): JsonResponse
    {
        $agent = AgentDetails::find($request->id);
        if ($agent) {
            $agent->status = $request->status;
            $agent->save();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }

    /**
     * Display the ledger for a specific agent.
     *
     * @param int $agentId
     * @return View
     */
    public function agentLedger(int $agentId): View
    {
        $agent = AgentDetails::select(
            'agent_details.*',
            'users.email',
            'users.mobile'
        )
            ->join('users', 'agent_details.user_id', '=', 'users.id')
            ->where('agent_details.id', $agentId)
            ->first();


        return view('backend.agents.ledger', compact('agent'));
    }

    /**
     * Retrieve and return the agent's ledger data in JSON format.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAgentLedger(Request $request): JsonResponse
    {
        $result = $this->agentService->getAgentLedger($request);

        return response()->json($result);
    }

    /**
     * Retrieve and return the agent's ledger data in JSON format.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function generateLedgerPdf(Request $request)
    {
        $result = $this->agentService->generateLedgerPdf($request);

        return response()->json(null);
    }


}