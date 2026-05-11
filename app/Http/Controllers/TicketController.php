<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Services\TicketService;
use App\Services\UserService;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\ApproveTicketRequest;

class TicketController extends Controller
{
    protected TicketService $ticketService;
    protected UserService $userService;

    public function __construct(TicketService $ticketService, UserService $userService)
    {
        $this->ticketService = $ticketService;
        $this->userService = $userService;
    }

    /**
     * Display tickets based on user level.
     */
    public function index()
    {
        $currentUser = $this->currentUser();

        if ($this->isDeveloper()) {
            $tickets = Ticket::with('user')->latestFirst()->get();
            return view('index.children_views.dev_tickets', compact('tickets'));
        }

        $tickets = Ticket::forUser($currentUser)->appIssues()->latestFirst()->get();
        return view('index.children_views.admin_tickets', compact('tickets'));
    }

    /**
     * Store a new ticket.
     */
    public function store(StoreTicketRequest $request)
    {
        $currentUser = auth()->user();
        $ticket = $this->ticketService->createTicket($currentUser, $request->validated());

        return redirect()->back()->with('success', "Ticket {$ticket->ticket_id} submitted.");
    }

    /**
     * Approve or reject a registration ticket.
     */
    public function approve(ApproveTicketRequest $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        if ($request->decision === 'reject') {
            $this->ticketService->rejectTicket($ticket, $this->currentUser(), $request->reject_reason);
            return back()->with('success', 'Registration rejected.');
        }

        try {
            $user = $this->ticketService->approveRegistration(
                $ticket,
                $this->currentUser(),
                $request->level ?? 'user'
            );

            return redirect()->back()->with('success', "User {$user->sur_name} approved!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update ticket status (for app issues).
     */
    public function updateStatus(StoreTicketRequest $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $this->ticketService->updateTicketStatus(
            $ticket,
            $request->status ?? 'Open',
            $this->currentUser()
        );

        return redirect()->back()->with('success', "Ticket #{$ticket->ticket_id} status updated to {$request->status}");
    }

    /**
     * Delete a ticket.
     */
    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticketId = $ticket->ticket_id;
        
        $ticket->delete();

        return redirect()->back()->with('success', "Ticket {$ticketId} deleted.");
    }
}