<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function index()
    {
        $auth = Auth::user();

        if ($auth->level === 'developer') {
            $tickets = Ticket::with('user')->latest()->get();
            return view('index.children_views.dev_tickets', compact('tickets'));
        } else {
            $tickets = Ticket::where('user_id', $auth->id)
                ->where('category', 'app_issue')
                ->latest()
                ->get();
            return view('index.children_views.admin_tickets', compact('tickets'));
        }
    }

    public function store(Request $request)
    {
        $auth = Auth::user();

        $lastTicket = Ticket::orderBy('id', 'desc')->first();
        if (!$lastTicket || !$lastTicket->ticket_id) {
            $nextId = 'CK00000001';
        } else {
            $number = intval(substr($lastTicket->ticket_id, 2)) + 1;
            $nextId = 'CK' . str_pad($number, 8, '0', STR_PAD_LEFT);
        }

        Ticket::create([
            'ticket_id' => $nextId,
            'user_id'   => $auth ? $auth->id : null,
            'user_num'  => $auth ? $auth->num : ($request->num ?? null),
            'category'  => $auth ? 'app_issue' : 'registration',
            'subject'   => $request->subject ?? 'New User Registration',
            'message'   => $request->message ?? 'Requesting access to SimBus',
            'status'    => 'Open',
            'registration_data' => !$auth ? json_encode($request->all()) : null,
        ]);

        return redirect()->back()->with('success', "Ticket $nextId submitted.");
    }

    /**
     * Logic for User Registrations
     * Uses: Approve / Reject
     */
    public function approve(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $regData = json_decode($ticket->registration_data, true);
        $devName = Auth::user()->sur_name;

        if ($request->decision === 'reject') {
            $ticket->update([
                'status' => 'Reject',
                'handled_by' => $devName,
                'dev_reply' => $request->reject_reason ?? 'Registration denied by administrator.'
            ]);
            return back()->with('success', 'Registration rejected.');
        }

        if (!$regData) {
            return redirect()->back()->with('error', 'Registration data is missing or corrupt.');
        }

        DB::transaction(function () use ($regData, $ticket, $request, $devName) {
            User::create([
                'name' => $regData['name'],
                'sur_name' => $regData['sur_name'],
                'num' => $regData['num'],
                'pass' => $regData['pass'] ?? $regData['password'],
                'level' => $request->level,
                'profile_photo' => null,
            ]);

            $ticket->update([
                'status' => 'Approve',
                'handled_by' => $devName,
                'message' => 'User approved by ' . $devName . ' on ' . now()->format('d M, H:i')
            ]);
        });

        return redirect()->back()->with('success', 'User ' . $regData['sur_name'] . ' approved!');
    }

    /**
     * Logic for app_issue Reports
     * Uses: Open / Progress / Closed
     */
    public function updateStatus(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $devName = Auth::user()->sur_name;

        // If you are closing an app_issue, it sets status to 'Closed'
        // If it's just moving to progress, it stays 'Progress'
        $ticket->update([
            'status'     => $request->status, // Values: 'Open', 'Progress', 'Closed'
            'handled_by' => ($request->status !== 'Open') ? $devName : null,
            'dev_reply'  => $request->dev_reply 
        ]);

        return redirect()->back()->with('success', 'Ticket #' . $ticket->ticket_id . ' status updated to ' . $request->status);
    }

    public function destroy($id)
    {
        Ticket::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Ticket deleted.');
    }
}