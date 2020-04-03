<?php

########################################################################
# Extension Manager/Repository config file for ext "staffdirectory".
#
# Auto generated 15-09-2011 08:43
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = [
    'title' => 'Staff Directory',
    'description' => 'Directory of groups of persons and their department membership with RDFa support',
    'category' => 'plugin',
    'author' => 'Xavier Perseguers',
    'author_email' => 'xavier@causal.ch',
    'author_company' => 'Causal Sàrl',
    'shy' => '',
    'dependencies' => '',
    'conflicts' => '',
    'priority' => '',
    'module' => '',
    'state' => 'beta',
    'internal' => '',
    'uploadfolder' => 0,
    'createDirs' => 'uploads/tx_staffdirectory/rte/',
    'modify_tables' => '',
    'clearCacheOnLoad' => 0,
    'lockType' => '',
    'version' => '0.7.0-dev',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-10.25.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
