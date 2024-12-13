<?php
require_once 'config.php';
require 'vendor/autoload.php'; // Ensure Composer's autoload is included

use Supabase\SupabaseClient;

// Create a Supabase client
$supabase = new SupabaseClient(SUPABASE_URL, SUPABASE_KEY);

// Example of querying data
$response = $supabase->from('your_table_name')->select('*')->execute();
if ($response['status'] === 200) {
    $data = $response['data'];
    // Process your data as needed
} else {
    echo "Error fetching data: " . $response['message'];
}