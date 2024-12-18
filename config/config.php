<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include Composer's autoload file
require 'vendor/autoload.php'; // Adjust the path if necessary

// Use Supabase client
use Supabase\SupabaseClient;

// Supabase configuration constants
define('SUPABASE_URL', 'https://db.eainxuivwxjmctkwstmq.supabase.co'); // Your Supabase URL
define('SUPABASE_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImVhaW54dWl2d3hqbWN0a3dzdG1xIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTczNDA0NTYyOCwiZXhwIjoyMDQ5NjIxNjI4fQ.P_heEvjHfg18lovtM2yHdq43BBwSpYHNCGea_9Uc_Lg'); // Your Supabase anon key

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