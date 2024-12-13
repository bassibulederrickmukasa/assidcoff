<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Absolute path to vendor directory
$vendorPath = __DIR__ . '/../vendor';

// Primary autoloader
require_once $vendorPath . '/autoload.php';

// Use GuzzleHttp client
use GuzzleHttp\Client;

// Supabase configuration constants
define('SUPABASE_URL', getenv('SUPABASE_URL')); // Get from environment variable
define('SUPABASE_KEY', getenv('SUPABASE_KEY')); // Get from environment variable

// Initialize Guzzle client
$client = new Client([
    'base_uri' => SUPABASE_URL,
    'headers' => [
        'Authorization' => 'Bearer ' . SUPABASE_KEY,
        'Content-Type' => 'application/json',
    ],
]);

// Example function to fetch data from a Supabase table
function fetchData($tableName) {
    global $client;

     try {
        $response = $client->request('GET', "/rest/v1/$tableName");
        $data = json_decode($response->getBody(), true);
        return $data;
    } catch (Exception $e) {
        echo "Error fetching data: " . $e->getMessage();
        return null;
    }
}

// Example usage
$tableData = fetchData('your_table'); // Replace 'your_table' with your actual table name
print_r($tableData);
