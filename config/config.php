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
define('SUPABASE_URL', 'https://db.eainxuivwxjmctkwstmq.supabase.co'); // Your Supabase URL
define('SUPABASE_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImpsaHhiYWFjem5mZnZ0bGVxbWZjIiwicm9sZSI6ImFub24iLCJpYXQiOjE3MzQwNTU5ODcsImV4cCI6MjA0OTYzMTk4N30.BtHQhky_Y8p64fEFzRdCEQAf3uAHw8D7NvIocBBJumM'); // Your Supabase anon key

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