<?php

return [
    \Causal\Staffdirectory\Domain\Model\Person::class => [
        'tableName' => 'fe_users',
        'recordType' => 'tx_staffdirectory',
        'properties' => [
            'gender' => [
                'fieldName' => 'tx_staffdirectory_gender',
            ],
            'alternateEmail' => [
                'fieldName' => 'tx_staffdirectory_email2',
            ],
            'mobilePhone' => [
                'fieldName' => 'tx_staffdirectory_mobilephone',
            ],
        ],
    ],
    \Causal\Staffdirectory\Domain\Model\Member::class => [
        'properties' => [
            'person' => [
                'fieldName' => 'feuser_id',
            ],
        ],
    ],
];
