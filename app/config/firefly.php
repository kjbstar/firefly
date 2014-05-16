<?php
return ['date_format'      => 'D j-M Y',
        'date_format_small' => 'd-m-y',
        'allowRegistration' => true,
        'predictionStart'  => ['type' => 'date', 'value' => '2012-01-01'],
        'defaultAllowance' => ['type' => 'float', 'value' => 0],
        'piggyAccount'     => ['type' => 'int', 'value' => 0],
        'frontpageAccount' => ['type' => 'string', 'value' => ''],
        'currency' => ['type' => 'int', 'value' => 0],
        'currencies' => [
            [
                'name' => 'Euro',
                'code' => 'EUR',
                'symbol' => '&#8364;',
            ],
            [
                'name' => 'US Dollar',
                'code' => 'USD',
                'symbol' => '&#36;',
            ],
        ]
];