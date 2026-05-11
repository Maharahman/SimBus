<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TicketService
{
    /**
     * Generate next ticket ID.
     */
    public function generateTicketId(): string
    {
        $lastTicket = Ticket::orderBy('id', 'desc')->first();
        
        if (!$lastTicket || !$lastTicket->ticket_id) {
            return 'CK00000001';
        }

        $number = intval(substr($lastTicket->ticket_id, 2)) + 1;
        return 'CK' . str_pad($number, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new ticket.
     */
    public function createTicket(User $user = null, array $data): Ticket
    {
        return Ticket::create([
            'ticket_id' => $this->generateTicketId(),
            'user_id' => $user?->id,
            'user_num' => $user?->num ?? $data['num'] ?? null,
            'category' => $user ? 'app_issue' : 'registration',
            'subject' => $data['subject'] ?? 'New User Registration',
            'message' => $data['message'] ?? 'Requesting access to SimBus',
            'status' => 'Open',
            'registration_data' => !$user ? json_encode($data) : null,
        ]);
    }

    /**
     * Approve a registration ticket and create user.
     */
    public function approveRegistration(Ticket $ticket, User $developer, string $level = 'user'): User
    {
        $regData = json_decode($ticket->registration_data, true);

        if (!$regData) {
            throw new \Exception('Registration data is missing or corrupt.');
        }

        return DB::transaction(function () use ($regData, $ticket, $developer, $level) {
            $user = User::create([
                'name' => $regData['name'],
                'sur_name' => $regData['sur_name'],
                'num' => $regData['num'],
                'pass' => $regData['pass'] ?? $regData['password'],
                'level' => $level,
                'profile_photo' => null,
            ]);

            $ticket->update([
                'status' => 'Approve',
                'handled_by' => $developer->sur_name,
                'message' => 'User approved by ' . $developer->sur_name . ' on ' . now()->format('d M, H:i')
            ]);

            return $user;
        });
    }

    /**
     * Reject a ticket.
     */
    public function rejectTicket(Ticket $ticket, User $developer, string $reason = ''): Ticket
    {
        return $ticket->update([
            'status' => 'Reject',
            'handled_by' => $developer->sur_name,
            'dev_reply' => $reason ?: 'Request denied by administrator.'
        ]) ? $ticket : $ticket->refresh();
    }

    /**
     * Update ticket status.
     */
    public function updateTicketStatus(Ticket $ticket, string $status, ?User $handler = null): Ticket
    {
        $ticket->update([
            'status' => $status,
            'handled_by' => $handler?->sur_name,
        ]);
        
        return $ticket->refresh();
    }
}
