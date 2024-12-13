<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once 'config.php';

use Supabase\Client;  // Verify the correct class name

// Create a Supabase client
$supabase = new Client(SUPABASE_URL, SUPABASE_KEY);

// Example of querying data
try {
    $response = $supabase->from('your_table_name')->select('*')->execute();
    
    if ($response->status === 200) {
        $data = $response->data;
        // Process your data as needed
    } else {
        echo "Error fetching data: " . $response->message;
    }
} catch (Exception $e) {
    echo "An error occurred: " . $e->getMessage();
}
?>