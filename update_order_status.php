<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once __DIR__ . '/includes/data_helpers.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to load data_helpers: ' . $e->getMessage()]);
    exit;
}

header('Content-Type: application/json');

// Enable error logging but not display
// error_reporting(E_ALL);
// ini_set('display_errors', 0);
ini_set('log_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$trackingNo = trim($_POST['tracking_no'] ?? '');
$newStatus  = trim($_POST['status'] ?? '');

if (empty($trackingNo) || empty($newStatus)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing tracking number or status']);
    exit;
}

$allowed = ['Delivered', 'In Transit', 'Returned', 'Pending', 'Cancelled', 'Prepaid'];
if (!in_array($newStatus, $allowed)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid status value']);
    exit;
}

// Ensure the function exists
if (!function_exists('updateOrderStatus')) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Function updateOrderStatus not defined']);
    exit;
}

$result = updateOrderStatus($trackingNo, $newStatus);

if ($result['success']) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $result['error']]);
}