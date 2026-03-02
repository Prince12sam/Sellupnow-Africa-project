<?php

return [
    // Used by CountriesNowLocationImporter.

    // Seconds.
    'timeout' => (int) env('COUNTRIESNOW_TIMEOUT', 25),

    // Optional path to a CA bundle (recommended fix for Windows cURL error 60).
    // Example: storage_path('certs/cacert.pem')
    'ca_bundle' => (string) env('COUNTRIESNOW_CA_BUNDLE', ''),

    // If true, and ONLY in local/dev/testing (or APP_DEBUG=true), we will retry once
    // with SSL verification disabled when we hit cURL error 60.
    'allow_insecure_fallback' => (bool) env('COUNTRIESNOW_ALLOW_INSECURE_FALLBACK', true),
];
