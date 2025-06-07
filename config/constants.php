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
    ],
    'destination_numbers' => [
        2, 3, 4, 5, 6, 7, 8, 9, 10,
        11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
        21, 22, 23, 24, 25, 26, 27, 28, 29, 30,
        31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
        41, 42, 43, 44, 45, 46, 47, 48, 49, 50
    ],

    'destination_labels' => [
        "Second", "Third", "Fourth", "Fifth", "Sixth", "Seventh", "Eighth", "Ninth", "Tenth",
        "Eleventh", "Twelfth", "Thirteenth", "Fourteenth", "Fifteenth", "Sixteenth", "Seventeenth", "Eighteenth", "Nineteenth", "Twentieth",
        "Twenty-first", "Twenty-second", "Twenty-third", "Twenty-fourth", "Twenty-fifth", "Twenty-sixth", "Twenty-seventh", "Twenty-eighth", "Twenty-ninth", "Thirtieth",
        "Thirty-first", "Thirty-second", "Thirty-third", "Thirty-fourth", "Thirty-fifth", "Thirty-sixth", "Thirty-seventh", "Thirty-eighth", "Thirty-ninth", "Fortieth",
        "Forty-first", "Forty-second", "Forty-third", "Forty-fourth", "Forty-fifth", "Forty-sixth", "Forty-seventh", "Forty-eighth", "Forty-ninth", "Fiftieth"
    ],
];
