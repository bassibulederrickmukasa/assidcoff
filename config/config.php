<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include Composer's autoload file
require 'vendor/autoload.php'; // Adjust the path if necessary

// Use Supabase client
use Supabase\SupabaseClient;

// Supabase configuration constants
define('SUPABASE_URL', 'https://eainxuivwxjmctkwstmq.supabase.co'); // Your Supabase URL
define('SUPABASE_KEY', 'your_supabase_anon_key'); // Your Supabase anon key

// Initialize Supabase client
$supabase = new SupabaseClient(SUPABASE_URL, SUPABASE_KEY);

// Example function to fetch data from a Supabase table
function fetchData($tableName) {
    global $supabase;

    $response = $supabase->from($tableName)->select('*')->execute();

    if ($response['status'] === 200) {
        return $response['data'];
    } else {
        echo "Error fetching data: " . $response['message'];
        return null;
    }
}