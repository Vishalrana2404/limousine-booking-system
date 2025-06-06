<?php

return [
    'paginationSize' => 100,
    'page' => 1,
    'userTypes' => [
        [
            'name' => 'Administrator',
            'description' => 'Administrator with super admin privileges',
            'type' => 'admin',
            'slug' => 'admin'
        ],
        [
            'name' => 'Staff',
            'description' => 'Staff with admin privileges',
            'type' => 'admin',
            'slug' => 'admin-staff'
        ],
        [
            'name' => 'Administrator',
            'description' => 'Super admin specifically for hotels',
            'type' => 'client',
            'slug' => 'client-admin'
        ],
        [
            'name' => 'Staff',
            'description' => 'Staff specifically for hotels',
            'type' => 'client',
            'slug' => 'client-staff'
        ]
    ],
    'hotelGroups' => [
        [
            'name' => 'ABC Hotel Group',
        ],
        [
            'name' => 'KBC Hotel Group',
        ],
        [
            'name' => 'Jain Hotel Group',
        ],
        [
            'name' => 'Panjabi Hotel Group',
        ]
    ],
    'race' => [
        'Chinese',
        'Malay',
        'Indian',
        'Eurasian',
        'Others',
    ],
    'departments' => [
        'Management',
        'Finance',
        'Human Resources',
        'Sales',
        'Marketing',
        'Operations',
        'Supervisor',
        'Customer Service'
    ],
    'entities' => [
        'Holdings',
        'Chauffeur',
        'Logistics'
    ],

    'serviceTypes' => [
        ['id' => 1, 'name' => 'Arrival'],
        ['id' => 2, 'name' => 'Transfer'],
        ['id' => 3, 'name' => 'Departure'],
        ['id' => 4, 'name' => 'Disposal'],
        ['id' => 5, 'name' => 'Delivery'],
        ['id' => 6, 'name' => 'Arrival/Disposal'],
        ['id' => 7, 'name' => 'Departure/Disposal'],
    ],
    'locations' => [
        ['id' => 1, 'name' => 'Changi Airport Terminal 1', 'is_instant_acceptable' => true],
        ['id' => 2, 'name' => 'Changi Airport Terminal 2', 'is_instant_acceptable' => true],
        ['id' => 3, 'name' => 'Changi Airport Terminal 3', 'is_instant_acceptable' => true],
        ['id' => 4, 'name' => 'Changi Airport Terminal 4', 'is_instant_acceptable' => true],
        // ['id' => 5, 'name' => 'Changi Airport Jet Query', 'is_instant_acceptable' => false],
        // ['id' => 6, 'name' => 'Changi Airport VIP Complex', 'is_instant_acceptable' => false],
        ['id' => 7, 'name' => 'Seletar Airport', 'is_instant_acceptable' => true],
        ['id' => 8, 'name' => 'Woodlands Checkpoint', 'is_instant_acceptable' => true],
        ['id' => 9, 'name' => 'Tanah Merah Ferry Terminal', 'is_instant_acceptable' => true],
        ['id' => 10, 'name' => 'Singapore Cruise Centre', 'is_instant_acceptable' => true],
        ['id' => 11, 'name' => 'Marina Bay Cruise Centre', 'is_instant_acceptable' => true],
        ['id' => 12, 'name' => 'Others', 'is_instant_acceptable' => false]
    ]
];
