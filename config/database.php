<?php
// config/database.php

// Include the Guzzle client from config.php
require_once __DIR__ . '/config.php';

// Example of querying data
function getDataFromSupabase($tableName) {
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