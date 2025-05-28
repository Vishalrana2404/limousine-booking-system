<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'The :attribute field must be accepted.',
    'accepted_if' => 'The :attribute field must be accepted when :other is :value.',
    'active_url' => 'The :attribute field must be a valid URL.',
    'after' => 'The :attribute field must be a date after :date.',
    'after_or_equal' => 'The :attribute field must be a date after or equal to :date.',
    'alpha' => 'The :attribute field must only contain letters.',
    'alpha_dash' => 'The :attribute field must only contain letters, numbers, dashes, and underscores.',
    'alpha_num' => 'The :attribute field must only contain letters and numbers.',
    'array' => 'The :attribute field must be an array.',
    'ascii' => 'The :attribute field must only contain single-byte alphanumeric characters and symbols.',
    'before' => 'The :attribute field must be a date before :date.',
    'before_or_equal' => 'The :attribute field must be a date before or equal to :date.',
    'between' => [
        'array' => 'The :attribute field must have between :min and :max items.',
        'file' => 'The :attribute field must be between :min and :max kilobytes.',
        'numeric' => 'The :attribute field must be between :min and :max.',
        'string' => 'The :attribute field must be between :min and :max characters.',
    ],
    'boolean' => 'The :attribute field must be true or false.',
    'can' => 'The :attribute field contains an unauthorized value.',
    'confirmed' => 'The :attribute field confirmation does not match.',
    'current_password' => 'The password is incorrect.',
    'date' => 'The :attribute field must be a valid date.',
    'date_equals' => 'The :attribute field must be a date equal to :date.',
    'date_format' => 'The :attribute field must match the format :format.',
    'decimal' => 'The :attribute field must have :decimal decimal places.',
    'declined' => 'The :attribute field must be declined.',
    'declined_if' => 'The :attribute field must be declined when :other is :value.',
    'different' => 'The :attribute field and :other must be different.',
    'digits' => 'The :attribute field must be :digits digits.',
    'digits_between' => 'The :attribute field must be between :min and :max digits.',
    'dimensions' => 'The :attribute field has invalid image dimensions.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'doesnt_end_with' => 'The :attribute field must not end with one of the following: :values.',
    'doesnt_start_with' => 'The :attribute field must not start with one of the following: :values.',
    'email' => 'The :attribute field must be a valid email address.',
    'ends_with' => 'The :attribute field must end with one of the following: :values.',
    'enum' => 'The selected :attribute is invalid.',
    'exists' => 'The selected :attribute is invalid.',
    'extensions' => 'The :attribute field must have one of the following extensions: :values.',
    'file' => 'The :attribute field must be a file.',
    'filled' => 'The :attribute field must have a value.',
    'gt' => [
        'array' => 'The :attribute field must have more than :value items.',
        'file' => 'The :attribute field must be greater than :value kilobytes.',
        'numeric' => 'The :attribute field must be greater than :value.',
        'string' => 'The :attribute field must be greater than :value characters.',
    ],
    'gte' => [
        'array' => 'The :attribute field must have :value items or more.',
        'file' => 'The :attribute field must be greater than or equal to :value kilobytes.',
        'numeric' => 'The :attribute field must be greater than or equal to :value.',
        'string' => 'The :attribute field must be greater than or equal to :value characters.',
    ],
    'hex_color' => 'The :attribute field must be a valid hexadecimal color.',
    'image' => 'The :attribute field must be an image.',
    'in' => 'The selected :attribute is invalid.',
    'in_array' => 'The :attribute field must exist in :other.',
    'integer' => 'The :attribute field must be an number.',
    'ip' => 'The :attribute field must be a valid IP address.',
    'ipv4' => 'The :attribute field must be a valid IPv4 address.',
    'ipv6' => 'The :attribute field must be a valid IPv6 address.',
    'json' => 'The :attribute field must be a valid JSON string.',
    'list' => 'The :attribute field must be a list.',
    'lowercase' => 'The :attribute field must be lowercase.',
    'lt' => [
        'array' => 'The :attribute field must have less than :value items.',
        'file' => 'The :attribute field must be less than :value kilobytes.',
        'numeric' => 'The :attribute field must be less than :value.',
        'string' => 'The :attribute field must be less than :value characters.',
    ],
    'lte' => [
        'array' => 'The :attribute field must not have more than :value items.',
        'file' => 'The :attribute field must be less than or equal to :value kilobytes.',
        'numeric' => 'The :attribute field must be less than or equal to :value.',
        'string' => 'The :attribute field must be less than or equal to :value characters.',
    ],
    'mac_address' => 'The :attribute field must be a valid MAC address.',
    'max' => [
        'array' => 'The :attribute field must not have more than :max items.',
        'file' => 'The :attribute field must not be greater than :max kilobytes.',
        'numeric' => 'The :attribute field must not be greater than :max.',
        'string' => 'The :attribute field must not be greater than :max characters.',
    ],
    'max_digits' => 'The :attribute field must not have more than :max digits.',
    'mimes' => 'The :attribute field must be a file of type: :values.',
    'mimetypes' => 'The :attribute field must be a file of type: :values.',
    'min' => [
        'array' => 'The :attribute field must have at least :min items.',
        'file' => 'The :attribute field must be at least :min kilobytes.',
        'numeric' => 'The :attribute field must be at least :min.',
        'string' => 'The :attribute field must be at least :min characters.',
    ],
    'min_digits' => 'The :attribute field must have at least :min digits.',
    'missing' => 'The :attribute field must be missing.',
    'missing_if' => 'The :attribute field must be missing when :other is :value.',
    'missing_unless' => 'The :attribute field must be missing unless :other is :value.',
    'missing_with' => 'The :attribute field must be missing when :values is present.',
    'missing_with_all' => 'The :attribute field must be missing when :values are present.',
    'multiple_of' => 'The :attribute field must be a multiple of :value.',
    'not_in' => 'The selected :attribute is invalid.',
    'not_regex' => 'The :attribute field format is invalid.',
    'numeric' => 'The :attribute field must be a number.',
    'password' => [
        'letters' => 'The :attribute field must contain at least one letter.',
        'mixed' => 'The :attribute field must contain at least one uppercase and one lowercase letter.',
        'numbers' => 'The :attribute field must contain at least one number.',
        'symbols' => 'The :attribute field must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.',
    ],
    'present' => 'The :attribute field must be present.',
    'present_if' => 'The :attribute field must be present when :other is :value.',
    'present_unless' => 'The :attribute field must be present unless :other is :value.',
    'present_with' => 'The :attribute field must be present when :values is present.',
    'present_with_all' => 'The :attribute field must be present when :values are present.',
    'prohibited' => 'The :attribute field is prohibited.',
    'prohibited_if' => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits' => 'The :attribute field prohibits :other from being present.',
    'regex' => 'The :attribute field format is invalid.',
    'required' => 'The :attribute field is required.',
    'required_array_keys' => 'The :attribute field must contain entries for: :values.',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_if_accepted' => 'The :attribute field is required when :other is accepted.',
    'required_if_declined' => 'The :attribute field is required when :other is declined.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute field must match :other.',
    'size' => [
        'array' => 'The :attribute field must contain :size items.',
        'file' => 'The :attribute field must be :size kilobytes.',
        'numeric' => 'The :attribute field must be :size.',
        'string' => 'The :attribute field must be :size characters.',
    ],
    'starts_with' => 'The :attribute field must start with one of the following: :values.',
    'string' => 'The :attribute field must be a string.',
    'timezone' => 'The :attribute field must be a valid timezone.',
    'unique' => 'The :attribute has already been taken.',
    'uploaded' => 'The :attribute failed to upload.',
    'uppercase' => 'The :attribute field must be uppercase.',
    'url' => 'The :attribute field must be a valid URL.',
    'ulid' => 'The :attribute field must be a valid ULID.',
    'uuid' => 'The :attribute field must be a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
        'atleast_one' => 'Please Select Atleast one Row.',
        'invalid_image' => 'Invalid file type. Please select a jpeg, png, or gif image.',
        'invalid_image' => 'Invalid file type. Please select a jpeg, png, or gif image.',
        'invalid_image_size' => 'The file size exceeds the limit. Please select an image with a maximum size of 5 MB.',
        'required_current_password' => "Please enter your current password.",
        'incorrect_current_password' => "Incorrect current password.",
        'required_new_password' => "Please enter a new password.",
        'password_min_length' => "Password must be at least 8 characters long.",
        'password_diffrent_from_current_password' => "New password must be different from current password.",
        'confirm_password' => "Please confirm your new password.",
        'password_not_match' => "Passwords do not match.",
        'password_required' => 'Please provide a password.',
        'password_regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one numeric digit, and one special character.',
        'name' => [
            'required' => 'Please enter name.',
            'min' => 'Name must be at least 3 characters long.',
            'max' => 'Name must not exceed 50 characters.',
        ],
        'first_name' => [
            'required' => 'Please enter first name.',
            'min' => 'First name must be at least 2 characters long.',
            'max' => 'First name must not exceed 50 characters.',
        ],
        'last_name' => [
            'required' => 'Please enter last name.',
            'min' => 'Last name must be at least 2 characters long.',
            'max' => 'Last name must not exceed 50 characters.',
        ],
        'user_type' => 'Please select a user type.',
        'client_type' => 'Please select a client type.',
        'department' => [
            'required' => 'Please select a department.',
        ],
        'country_code' => [
            'required' => "Please enter country code.",
            'integer' => 'The country code must be an number.',
            'min' => 'Country code must be at least 1 digits long.',
            'max' => 'Country code must not exceed 3 digits.',
            'digits_between' => 'The country code must be between 1 to 3 digits.'
        ],
        'phone' => [
            'required' => "Please enter contact number.",
            // 'regex' => 'The contact number must be contain number.',
            'min' => "Contact number should be greater than or equal to 6.",
            // 'max' => "Contact number should not be greater than 10.",
            // 'digits_between' => 'The contact number must be between 6 to 10 digits.'
        ],
        'email' => [
            'required' => "Please enter an email address.",
            'email' => "Please enter a valid email address.",
            'already_exist' => 'Email already exist in system.'
        ],
        'status' => [
            'required' => "Please select a status.",
            'custum_status' => "The status field must be either 'ACTIVE' or 'INACTIVE'."
        ],
        'customFixedMultiplier' => "The field must either be 'Fixed' or 'Multiplier.'",
        'client' => [
            'required' => "Please select a client.",
        ],
        'invoice' => [
            'required' => "Please enter the hotel invoice name.",
            'min' => "Invoice must be at least 2 characters long.",
            'max' => "Invoice cannot exceed 50 characters.",
        ],
        'event' => [
            'required' => "Please enter the hotel event.",
            'min' => "Event must be at least 3 characters long.",
            'max' => "Event cannot exceed 50 characters.",
        ],
        'decimal' => [
            'required' => "Please enter the value.",
            'pattern' => "The field must be a decimal with precision of 10 and scale of 2.",
            'regex' => "The field must be a decimal with precision of 10 and scale of 2.",
        ],
        'fixed_multiplier' => 'Please select fixed or multiplier.',
        'type' => 'Please select a driver type.',
        'gender' => 'Please select a gender.',
        'class_name' => [
            'required' => 'Please enter vehicle class name.',
            'min' => 'Name must be at least 3 characters long.',
            'max' => 'Name must not exceed 50 characters.',
        ],
        'seating_capacity' => [
            'required' => "Please enter seating capacity.",
            'digits' => "Please enter only digits.",
            'min' => "Please enter a value greater than or equal to 1",
            'max' => "Please enter a value less than or equal to 45",
        ],
        'total_luggage' => [
            'required' => "Please enter total luggage.",
            'digits' => "Please enter only digits.",
        ],
        'total_pax' => [
            'required' => "Please enter total pax.",
            'digits' => "Please enter only digits.",
            'max' => 'Total pax should not be greater than seating capacity.',
            'lte' => 'Total pax should not be greater than seating capacity.'
        ],
        'vehicle_class' => [
            'required' => "Please select vehicle class.",
        ],
        'vehicle_number' => [
            'required' => "Please enter vehicle number.",
            'already_exist' => 'Vehicle number already exists.'
        ],
        'image' => [
            'required' => "Please upload an image.",
            'accept' => "Please upload a valid image file (jpeg, png, jpg).",
        ],
        'brand' => [
            'required' => "Please enter brand name.",
        ],
        'model' => [
            'required' => "Please enter vehicle model.",
        ],
        'service_type_id' => [
            'required' => "Please select a service type.",
        ],
        'pick_up_location_id' => [
            'required' => "Please select a arrival pick up location.",
        ],
        'drop_off_location_id' => [
            'required' => "Please select a departure drop off location.",
        ],
        'pick_up_location' => [
            'required' => "Please enter pick up location.",
            'min' => 'Pick up location must be at least 3 characters long.'
        ],
        'drop_of_location' => [
            'required' => "Please enter drop off location.",
            'min' => 'Drop off location must be at least 3 characters long.'
        ],
        'vehicle_type_id' => [
            'required' => "Please select a vehicle type.",
        ],
        'pickup_date' => [
            'required' => "Please select a pick up date.",
            'regex' => 'Please enter a date in the format dd/MM/yyyy.',
            'notPastDate'=>'Pickup date cannot be a past date.'
        ],
        'pickup_time' => [
            'required' => "Please select a pick up time.",
            'notPastTime'=> "Pickup time cannot be in the past.",
        ],
        'time_format' => 'Please enter a time in the format HH:mm.',
        'date_time_format'=> 'Please enter a date in the format dd/MM/yyyy HH:mm.',
        'departure_time' => [
            'required' => "Please select a departure time.",
        ],
        'no_of_hours' => [
            'required' => "Please enter the number of hours.",
            'digits' => "Please enter a valid number.",
            'max' => "Number of hours should no be greater then 24.",
            'min' => "Minimum hours should be 3.",
            'min_less_then_13_seat' => "Minimum hours should be 3 for less than 13 seats.",
            'min_greater_13_seat' => "Minimum hours should be 4 for greater than 13 seats.",
            'min_cross_border' => "Minimum hours must be 6 for a cross border service.",
            'min_10_hours'=>"Minimum hours must be 10 during blackout period."
        ],
        'total_pax_booking' => [
            'required' => "Please enter the total number of passengers.",
            'digits' => "Please enter a valid number.",
            'min' => "Please enter a value greater than or equal to 1.",
            'max' => "Please enter a value less than or equal to 45."
        ],
        'total_luggage_booking' => [
            'required' => "Please enter the total number of luggage.",
            'digits' => "Please enter only digits.",
            'min' => "Please enter a value greater than or equal to 1.",
            'max' => "Please enter a value less than or equal to 100.",
        ],
        'select_driver' => [
            'required' => "Please select a driver.",
        ],
        'child_seat_required' => [
            'required' => "Please select an option.",
        ],
        'guest_name' => [
            'required' => "Please enter the name of the guest(s).",
            'min' => 'Name must be at least 3 characters long.',
            'max' => 'Name must not exceed 100 characters.',
        ],
        'driver_name' => [
            'required' => 'Please enter name.',
            'min' => 'Name must be at least 2 characters long.',
            'max' => 'Name must not exceed 50 characters.',
        ],
        'only_string' => "Please enter only letters and spaces.",
        'laterThan' => "Date cannot be earlier than or equal to pick up date time.",
        'flight_detail' => [
            'required' => "Please enter the flight details.",
            'min' => 'Flight details must be at least 3 characters long.',
            'max' => 'Flight details must not exceed 50 characters.',
        ],
        'attachment' => [
            "extension" =>  "Please upload a file with a valid type (jpg, jpeg, png, gif, doc, docx, txt, pdf, xls, xlsx)",
            "validFileSize" => "Please upload a file smaller than 5 MB."
        ],
        'city_surcharge' => [
            'already_inside' => "You are inside the boundaries.",
            'already_marked' => "Location already marked."
        ],
        'event_name' => [
            'required' => "Please enter event name.",
            'min' => 'Event name must be at least 3 characters long.',
            'max' => 'Event name must not exceed 100 characters.',
        ],
        'event_start_date' => [
            'required' => "Please select event start date.",
            'regex' => 'Please enter a date in the format dd/MM/yyyy.'
        ],
        'event_end_date' => [
            'required' => "Please select event end date.",
            'regex' => 'Please enter a date in the format dd/MM/yyyy.',
            'after_or_equal' => 'The end date must be equal or greater than start date.'
        ],
        'vehicle_id' => [
            'required' => "Please select a vehicle."
        ],
        'customDateFormat' => "Please enter a date in the format dd/mm/yyyy hh:mm.",
        'departure_charge' => [
            'required' => "Please enter departure charge.",
        ],
        'arrival_charge' => [
            'required' => "Please enter arrival charge.",
        ],
        'transfer_charge' => [
            'required' => "Please enter transfer charge.",
        ],
        'disposal_charge' => [
            'required' => "Please enter disposal charge.",
        ],
        'delivery_charge' => [
            'required' => "Please enter delivery charge.",
        ],
        'is_driver_notified' => [
            'required' => "Please select driver notified.",
        ],
        'is_driver_acknowledge' => [
            'required' => "Please select driver acknowledge.",
        ],
        'assign_driver' => "Please assign a driver first.",
        'already_inside' => 'You are inside the boundaries.',
        'chat_id' => [
            'required' => "Please enter telegram chat id.",
            'min' => "Telegram id must be at least 11 characters long.",
            'max' => "Telegram id must be at least 11 characters long.",
            'valid' => "Please enter a valid chat ID.",
        ],
        'term_conditions'=>[
            'required' => "Please enter term & conditions.",
            'min' => "Term & conditions must be at least 3 characters long.",
        ],
        'hotel_id'=>[
            'required' => "Please select a corporate.",
        ],
        'client_id'=>[
            'required' => "Please select a client.",
        ],
        'event_id'=>[
            'required' => "Please select an event.",
        ],
        'hotel_vehicle_fair_arrival'=>[
            'required' => "Please add some fair for arrival.",
            'digits' => "Please enter only digits.",
        ],
        'hotel_vehicle_fair_departure'=>[
            'required' => "Please add some fair for departure.",
            'digits' => "Please enter only digits.",
        ],
        'hotel_vehicle_fair_transfer'=>[
            'required' => "Please add some fair for transfer.",
            'digits' => "Please enter only digits.",
        ],
        'hotel_vehicle_fair_per_hour'=>[
            'required' => "Please add some fair for per hour.",
            'digits' => "Please enter only digits.",
        ]
        
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
