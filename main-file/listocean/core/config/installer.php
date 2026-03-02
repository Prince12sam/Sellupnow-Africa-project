<?php

return [
    'app_name' => 'Listocean',
    'super_admin_role_id' => 1,
    'admin_model' => \App\Models\Backend\Admin::class,
    'admin_table' => 'admins',
    'multi_tenant' => false,
    'author' => 'byteseed',
    'product_key' => '9cabd95c36f0aad9a8a88cd53b751af99d3e0a28',
    'php_version' => '8.1',
    'extensions' => ['BCMath', 'Ctype', 'JSON', 'Mbstring', 'OpenSSL', 'PDO', 'pdo_mysql', 'Tokenizer', 'XML', 'cURL', 'fileinfo'],
    'website' => 'https://bytesed.com',
    'email' => 'support@bytesed.com',
    'env_example_path' => public_path('env-sample.txt'),
    'broadcast_driver' => 'log',
    'cache_driver' => 'file',
    'queue_connection' => 'sync',
    'mail_port' => '587',
    'mail_encryption' => 'tls',
    'model_has_roles' => true,
    'bundle_pack' => false, //if the product has bundle pack
    'bundle_pack_key' => '54d6905e67e9618d4a7b2fdfe1c17508d495b991', //bundle pack product key
];
