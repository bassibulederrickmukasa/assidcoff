<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Verify user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in');
    }

    // Verify user has permission
    if (!in_array($_SESSION['role'], ['admin', 'staff'])) {
        throw new Exception('Unauthorized access');
    }

    // Validate input
    if (!isset($_POST['date']) || !isset($_POST['small_boxes']) || !isset($_POST['big_boxes'])) {
        throw new Exception('Missing required fields');
    }

    $date = $_POST['date'];
    $smallBoxes = (int)$_POST['small_boxes'];
    $bigBoxes = (int)$_POST['big_boxes'];

    // Validate date format
    if (!strtotime($date)) {
        throw new Exception('Invalid date format');
    }

    // Validate box counts
    if ($smallBoxes < 0 || $bigBoxes < 0) {
        throw new Exception('Box counts cannot be negative');
    }

    // Check if entry already exists for this date
    $stmt = $pdo->prepare("SELECT id FROM daily_production WHERE date = ?");
    $stmt->execute([$date]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Update existing record
        $stmt = $pdo->prepare("
            UPDATE daily_production 
            SET small_boxes = ?, big_boxes = ? 
            WHERE date = ?
        ");
        $stmt->execute([$smallBoxes, $bigBoxes, $date]);
    } else {
        // Insert the record
        $stmt = $pdo->prepare("
            INSERT INTO daily_production (date, small_boxes, big_boxes)
            VALUES (:date, :small_boxes, :big_boxes)
        ");

        $stmt->execute([
            ':date' => $date,
            ':small_boxes' => $smallBoxes,
            ':big_boxes' => $bigBoxes
        ]);

        $newId = $pdo->lastInsertId();

        // Log activity
        require_once '../includes/security.php';
        logActivity($pdo, $_SESSION['user_id'], 'production_added', 
            "Added production: {$smallBoxes} small boxes, {$bigBoxes} big boxes for date {$date}");
    }

    // Insert sample data for testing if none exists
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM daily_production");
    $count = $stmt->fetch()['count'];

    if ($count <= 1) {
        // Add sample data for the past 7 days
        $dates = [];
        for ($i = 6; $i >= 0; $i--) {
            $dates[] = date('Y-m-d', strtotime("-$i days"));
        }

        foreach ($dates as $sample_date) {
            if ($sample_date != $date) { // Don't duplicate today's entry
                $stmt = $pdo->prepare("
                    INSERT INTO daily_production (date, small_boxes, big_boxes) 
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE small_boxes = VALUES(small_boxes), big_boxes = VALUES(big_boxes)
                ");
                $stmt->execute([
                    $sample_date,
                    rand(50, 200), // Random small boxes between 50-200
                    rand(20, 100)  // Random big boxes between 20-100
                ]);
            }
        }

        // Add sample supplies
        $stmt = $pdo->prepare("
            INSERT INTO supplies (date, staff_id, small_boxes, big_boxes) 
            VALUES (CURRENT_DATE(), 1, ?, ?)
        ");
        $stmt->execute([rand(30, 100), rand(10, 50)]);

        // Add sample payments
        $stmt = $pdo->prepare("
            INSERT INTO payments (date, staff_id, amount, boxes_count) 
            VALUES (CURRENT_DATE(), 1, ?, ?)
        ");
        $stmt->execute([rand(50000, 200000), rand(50, 150)]);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Production record added successfully',
        'data' => [
            'id' => $existing ? $existing['id'] : $newId,
            'date' => $date,
            'small_boxes' => $smallBoxes,
            'big_boxes' => $bigBoxes
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error',
        'message' => $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Validation error',
        'message' => $e->getMessage()
    ]);
}