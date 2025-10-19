<?php
// joraki/api/logout.php

header('Content-Type: application/json');
session_start();

if (isset($_SESSION['user_id'])) {
    session_unset();
    session_destroy();
    echo json_encode(['success' => true, 'message' => 'Logout successful.']);
} else {
    echo json_encode(['success' => false, 'message' => 'No active session found.']);
}
