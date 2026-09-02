<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;
use App\Services\Crm\SalesVisibility;

class ContactPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-contacts') || $user->can('view-pipeline');
    }

    public function view(User $user, Contact $contact): bool
    {
        if (! $this->viewAny($user)) {
            return false;
        }

        $visibility = app(SalesVisibility::class);
        if ($visibility->canViewAll($user)) {
            return true;
        }

        return $visibility->scopeContacts(Contact::query(), $user)->whereKey($contact->id)->exists();
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Contact $contact): bool
    {
        return $this->view($user, $contact);
    }
}
