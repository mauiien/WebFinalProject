<?php
require_once 'db.php';

// Initialize response
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Simple validation
    if (empty($name) || empty($email) || empty($message)) {
        $response['message'] = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Please enter a valid email address.';
    } else {
        // Prepare and bind
        $stmt = $conn->prepare('INSERT INTO submissions (name, email, message) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $name, $email, $message);
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Thank you for contacting us!';
        } else {
            $response['message'] = 'Error: ' . $stmt->error;
        }
        $stmt->close();
    }
    $conn->close();
}

// If using AJAX, you can echo json_encode($response);
// For now, redirect back with a query string
if ($response['success']) {
    header('Location: index.php?success=1');
    exit();
} else {
    header('Location: index.php?error=' . urlencode($response['message']));
    exit();
}
?> 