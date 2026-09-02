<?php

namespace App\Policies;

use App\Models\PipelineTicket;
use App\Models\User;
use App\Services\Crm\SalesVisibility;

class PipelineTicketPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-pipeline') || $user->can('view-contacts');
    }

    public function view(User $user, PipelineTicket $ticket): bool
    {
        if (! $this->viewAny($user)) {
            return false;
        }

        return app(SalesVisibility::class)->ownsTicket($user, $ticket);
    }

    public function update(User $user, PipelineTicket $ticket): bool
    {
        return $this->view($user, $ticket);
    }

    public function transfer(User $user, PipelineTicket $ticket): bool
    {
        return $user->can('view-all-pipeline') || $user->can('view-team-pipeline') || $this->view($user, $ticket);
    }
}
