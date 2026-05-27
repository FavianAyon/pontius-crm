<?php

return [
    'initial_contact_due_minutes' => 60,
    'due_soon_minutes' => 30,
    'public_api_cache_minutes' => 10,
    'case_file_document_templates' => [
        'lead' => [
            ['name' => 'ID', 'document_type' => 'id'],
            ['name' => 'Proof of address', 'document_type' => 'proof_of_address'],
            ['name' => 'Tax document', 'document_type' => 'tax_document'],
        ],
        'buyer' => [
            ['name' => 'ID', 'document_type' => 'id'],
            ['name' => 'Proof of address', 'document_type' => 'proof_of_address'],
            ['name' => 'Tax document', 'document_type' => 'tax_document'],
            ['name' => 'Bank statement', 'document_type' => 'bank_statement'],
        ],

        'seller' => [
            ['name' => 'ID', 'document_type' => 'id'],
            ['name' => 'Proof of address', 'document_type' => 'proof_of_address'],
            ['name' => 'Tax document', 'document_type' => 'tax_document'],
            ['name' => 'Property deed', 'document_type' => 'property_deed'],
        ],

        'listing' => [
            ['name' => 'Property deed', 'document_type' => 'property_deed'],
            ['name' => 'Proof of address', 'document_type' => 'proof_of_address'],
            ['name' => 'Tax document', 'document_type' => 'tax_document'],
        ],
    ],
];
