<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default inbound pipeline owner
    |--------------------------------------------------------------------------
    |
    | User id assigned to sell/mortgage tickets when no broker is linked yet.
    | Set CRM_DEFAULT_INBOUND_OWNER_USER_ID in .env (admin or team lead user id).
    |
    */
    'default_inbound_owner_user_id' => env('CRM_DEFAULT_INBOUND_OWNER_USER_ID'),

    /*
    |--------------------------------------------------------------------------
    | Commission split percentages (must sum to 100 per scenario)
    |--------------------------------------------------------------------------
    |
    | Lister = broker who listed / first showed the unit.
    | Closer = broker on the approved deal.
    | Manager = closer's team lead when set.
    |
    */
    'commission_splits' => [
        'with_lister' => [
            'lister' => 20,
            'closer' => 60,
            'manager' => 20,
        ],
        'without_lister' => [
            'closer' => 80,
            'manager' => 20,
        ],
    ],
];
