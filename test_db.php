<?php
require_once 'config/database.php';

// Test fetching data
$response = $supabase->from('your_table_name')->select('*')->execute();
if ($response['status'] === 200) {
    echo "Data fetched successfully!";
    print_r($response['data']);
} else {
    echo "Error: " . $response['message'];
}