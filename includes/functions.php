<?php
// includes/functions.php - Helper Functions

// ============================================
// SECURITY & VALIDATION FUNCTIONS
// ============================================

/**
 * Sanitize user input
 * @param string $data Input data
 * @return string Sanitized data
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate email address
 * @param string $email Email to validate
 * @return bool
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (Kenyan format)
 * @param string $phone Phone number
 * @return bool
 */
function validate_phone($phone) {
    // Kenyan phone format: +254... or 0... or 7... or 1...
    $pattern = '/^(\+?254|0)?[71]\d{8}$/';
    return preg_match($pattern, preg_replace('/\s+/', '', $phone));
}

/**
 * Validate price
 * @param mixed $price Price to validate
 * @return bool
 */
function validate_price($price) {
    return is_numeric($price) && $price > 0;
}

/**
 * Check if category is restricted
 * @param string $category Category name
 * @return bool
 */
function is_restricted_category($category) {
    return in_array($category, RESTRICTED_CATEGORIES);
}

/**
 * Generate CSRF token
 * @return string
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token Token to verify
 * @return bool
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// ============================================
// FILE UPLOAD FUNCTIONS
// ============================================

/**
 * Upload and validate image file
 * @param array $file $_FILES array element
 * @param string $target_dir Target directory
 * @return array ['success' => bool, 'filename' => string, 'error' => string]
 */
function upload_image($file, $target_dir = UPLOAD_PATH) {
    $result = ['success' => false, 'filename' => '', 'error' => ''];
    
    // Check if file was uploaded
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        $result['error'] = 'No file uploaded';
        return $result;
    }
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        $result['error'] = 'File size exceeds maximum allowed size of ' . format_file_size(MAX_FILE_SIZE);
        return $result;
    }
    
    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, ALLOWED_IMAGE_TYPES)) {
        $result['error'] = 'Invalid file type. Only JPG and PNG images are allowed';
        return $result;
    }
    
    // Check file extension
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, ALLOWED_EXTENSIONS)) {
        $result['error'] = 'Invalid file extension';
        return $result;
    }
    
    // Generate unique filename
    $new_filename = uniqid('item_', true) . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        // Set proper permissions
        chmod($target_file, 0644);
        
        $result['success'] = true;
        $result['filename'] = $new_filename;
    } else {
        $result['error'] = 'Failed to move uploaded file';
    }
    
    return $result;
}

/**
 * Delete uploaded file
 * @param string $filename Filename to delete
 * @param string $dir Directory
 * @return bool
 */
function delete_file($filename, $dir = UPLOAD_PATH) {
    if (empty($filename)) {
        return false;
    }
    
    $filepath = $dir . $filename;
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    
    return false;
}

// ============================================
// FORMATTING FUNCTIONS
// ============================================

/**
 * Format price with currency
 * @param float $price Price
 * @return string Formatted price
 */
function format_price($price) {
    return CURRENCY_SYMBOL . ' ' . number_format($price, 2);
}

/**
 * Format file size
 * @param int $bytes File size in bytes
 * @return string Formatted size
 */
function format_file_size($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Format date for display
 * @param string $date Date string
 * @param string $format Date format
 * @return string Formatted date
 */
function format_date($date, $format = 'M d, Y') {
    if (empty($date) || $date === '0000-00-00 00:00:00') {
        return 'N/A';
    }
    return date($format, strtotime($date));
}

/**
 * Time ago function
 * @param string $datetime Datetime string
 * @return string Time ago text
 */
function time_ago($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return format_date($datetime);
    }
}

/**
 * Truncate text
 * @param string $text Text to truncate
 * @param int $length Maximum length
 * @param string $suffix Suffix to add
 * @return string Truncated text
 */
function truncate_text($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

// ============================================
// SESSION & AUTH FUNCTIONS
// ============================================

/**
 * Check if user is logged in
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 * @return bool
 */
function is_admin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

/**
 * Require login (redirect if not logged in)
 * @param string $redirect_to URL to redirect to
 */
function require_login($redirect_to = 'admin/') {
    if (!is_logged_in()) {
        $_SESSION['error_message'] = 'Please login to access this page';
        header('Location: ' . BASE_URL . $redirect_to);
        exit();
    }
}

/**
 * Require admin privileges
 * @param string $redirect_to URL to redirect to
 */
function require_admin($redirect_to = 'admin/') {
    if (!is_admin()) {
        $_SESSION['error_message'] = 'You do not have permission to access this page';
        header('Location: ' . BASE_URL . $redirect_to);
        exit();
    }
}

/**
 * Set success message
 * @param string $message Message text
 */
function set_success($message) {
    $_SESSION['success_message'] = $message;
}

/**
 * Set error message
 * @param string $message Message text
 */
function set_error($message) {
    $_SESSION['error_message'] = $message;
}

/**
 * Set warning message
 * @param string $message Message text
 */
function set_warning($message) {
    $_SESSION['warning_message'] = $message;
}

/**
 * Set info message
 * @param string $message Message text
 */
function set_info($message) {
    $_SESSION['info_message'] = $message;
}

// ============================================
// STATUS & BADGE FUNCTIONS
// ============================================

/**
 * Get status badge HTML
 * @param string $status Item status
 * @return string HTML badge
 */
function get_status_badge($status) {
    $badges = [
        'pending' => '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pending</span>',
        'purchased' => '<span class="badge bg-info"><i class="fas fa-shopping-bag me-1"></i>Purchased</span>',
        'under_modification' => '<span class="badge bg-primary"><i class="fas fa-tools me-1"></i>Under Modification</span>',
        'refurbished' => '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Refurbished</span>',
        'sold' => '<span class="badge bg-secondary"><i class="fas fa-check-double me-1"></i>Sold</span>',
        'rejected' => '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Rejected</span>'
    ];
    
    return $badges[$status] ?? '<span class="badge bg-secondary">' . htmlspecialchars($status) . '</span>';
}

/**
 * Get category icon
 * @param string $category Category name
 * @return string Font Awesome icon class
 */
function get_category_icon($category) {
    $icons = [
        'Car' => 'fa-car',
        'Bike' => 'fa-motorcycle',
        'Electronics' => 'fa-laptop',
        'Furniture' => 'fa-couch',
        'Appliances' => 'fa-blender',
        'Tools' => 'fa-tools',
        'Sports' => 'fa-basketball-ball',
        'Other' => 'fa-box'
    ];
    
    return $icons[$category] ?? 'fa-tag';
}

// ============================================
// PAGINATION FUNCTIONS
// ============================================

/**
 * Generate pagination HTML
 * @param int $current_page Current page number
 * @param int $total_pages Total pages
 * @param string $base_url Base URL for pagination links
 * @return string HTML pagination
 */
function generate_pagination($current_page, $total_pages, $base_url) {
    if ($total_pages <= 1) {
        return '';
    }
    
    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
    
    // Previous button
    $prev_disabled = $current_page <= 1 ? 'disabled' : '';
    $prev_page = max(1, $current_page - 1);
    $html .= '<li class="page-item ' . $prev_disabled . '">';
    $html .= '<a class="page-link" href="' . $base_url . '?page=' . $prev_page . '">Previous</a>';
    $html .= '</li>';
    
    // Page numbers
    $start = max(1, $current_page - 2);
    $end = min($total_pages, $current_page + 2);
    
    if ($start > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $base_url . '?page=1">1</a></li>';
        if ($start > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    for ($i = $start; $i <= $end; $i++) {
        $active = $i === $current_page ? 'active' : '';
        $html .= '<li class="page-item ' . $active . '">';
        $html .= '<a class="page-link" href="' . $base_url . '?page=' . $i . '">' . $i . '</a>';
        $html .= '</li>';
    }
    
    if ($end < $total_pages) {
        if ($end < $total_pages - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $html .= '<li class="page-item"><a class="page-link" href="' . $base_url . '?page=' . $total_pages . '">' . $total_pages . '</a></li>';
    }
    
    // Next button
    $next_disabled = $current_page >= $total_pages ? 'disabled' : '';
    $next_page = min($total_pages, $current_page + 1);
    $html .= '<li class="page-item ' . $next_disabled . '">';
    $html .= '<a class="page-link" href="' . $base_url . '?page=' . $next_page . '">Next</a>';
    $html .= '</li>';
    
    $html .= '</ul></nav>';
    
    return $html;
}

// ============================================
// REDIRECT FUNCTIONS
// ============================================

/**
 * Redirect to URL
 * @param string $url URL to redirect to
 */
function redirect($url) {
    header('Location: ' . $url);
    exit();
}

/**
 * Redirect back to previous page
 */
function redirect_back() {
    $referrer = $_SERVER['HTTP_REFERER'] ?? BASE_URL;
    redirect($referrer);
}

// ============================================
// DEBUG FUNCTIONS
// ============================================

/**
 * Debug print (only in debug mode)
 * @param mixed $data Data to print
 */
function debug_print($data) {
    if (DEBUG_MODE) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}

/**
 * Dump and die (only in debug mode)
 * @param mixed $data Data to dump
 */
function dd($data) {
    if (DEBUG_MODE) {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
        die();
    }
}