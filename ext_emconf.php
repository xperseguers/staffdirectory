<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Staff Directory',
    'description' => 'Directory of groups of persons and their department membership with RDFa support',
    'category' => 'plugin',
    'author' => 'Xavier Perseguers',
    'author_email' => 'xavier@causal.ch',
    'author_company' => 'Causal Sàrl',
    'state' => 'stable',
    'version' => '2.1.0',
    'constraints' => [
        'depends' => [
            'php' => '8.1.0-8.3.99',
            'typo3' => '11.5.0-12.4.99',
            'static_info_tables' => '11.5.0-12.4.99'
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
