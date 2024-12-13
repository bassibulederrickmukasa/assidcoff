<?php
require 'config/config.php'; // Adjust the path as needed
require 'config/database.php'; // Include the database functions file

// Test fetching data from a specific table
$tableData = fetchData('your_table'); // Replace 'your_table' with your actual table name

if ($tableData) {
    echo "Data fetched successfully:\n";
    print_r($tableData);
} else {
    echo "Failed to fetch data.";
}