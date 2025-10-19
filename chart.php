<?php
// chart.php - Joraki Ventures Chatbot + Chart API
// ============================================================
// 🧩 Includes:
// - Chatbot logic
// - Chart API for frontend visualization
// - Proper JSON responses
// ============================================================

ini_set('display_errors', 1);
error_reporting(E_ALL);

// ✅ set session options before starting session
ini_set('session.cookie_samesite', 'Lax');

session_start();
session_regenerate_id(true);

require_once __DIR__ . '/includes/db.php'; // ✅ ensure DB is connected


// ============================================================
// 🔹 Chatbot Section (same as before)
// ============================================================

// Initialize chat history
if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

// Knowledge base
$knowledge_base = [
    'about' => [
        'name' => 'Joraki Ventures',
        'description' => 'A platform to buy and sell professionally refurbished items with confidence',
        'tagline' => 'Quality products, fair prices, and sustainable shopping',
        'mission' => 'Providing quality refurbished products while promoting sustainable shopping and reducing waste'
    ],
    'features' => [
        'Secure Transactions' => 'All payments processed securely with full buyer protection',
        'Quality Guaranteed' => 'Professional inspection and refurbishment by certified technicians',
        'Best Prices' => 'Fair pricing for sellers, unbeatable deals for buyers',
        'Eco-Friendly' => 'Sustainable shopping that reduces waste and protects our planet'
    ],
    'how_it_works' => [
        'Step 1: Submit Your Item' => 'Fill out our simple form with details, upload photos, and set your price',
        'Step 2: We Review' => 'Our team reviews within 24-48 hours and arranges pickup and payment',
        'Step 3: Refurbishment' => 'Expert technicians inspect, repair, and certify every item',
        'Step 4: Ready to Sell' => 'Listed on marketplace at competitive prices for buyers'
    ],
    'categories' => ['Electronics', 'Furniture', 'Appliances', 'Other'],
    'pages' => [
        'Buy Section' => 'buy_section/index.php',
        'Sell Section' => 'sell_section/index.php',
        'Item Details' => 'buy_section/item_details.php'
    ]
];

function getChatbotResponse($query, $knowledge_base)
{
    $query = strtolower(trim($query));

    if (preg_match('/\b(hello|hi|hey|greetings)\b/', $query)) {
        return "Hello! 👋 Welcome to Joraki Ventures! How can I help you today?";
    }

    if (preg_match('/\b(what is|about|describe)\b/', $query)) {
        return "Joraki Ventures is a platform for buying and selling refurbished items sustainably.";
    }

    if (preg_match('/\b(how|process|work|steps)\b/', $query)) {
        $response = "Here's how Joraki Ventures works:\n\n";
        foreach ($knowledge_base['how_it_works'] as $step => $desc) {
            $response .= "📌 {$step}: {$desc}\n\n";
        }
        return $response;
    }

    if (preg_match('/\b(features|why|benefits)\b/', $query)) {
        $response = "Here’s why Joraki Ventures stands out:\n\n";
        foreach ($knowledge_base['features'] as $k => $v) {
            $response .= "• {$k}: {$v}\n";
        }
        return $response;
    }

    if (preg_match('/\b(category|categories|type|types)\b/', $query)) {
        return "We offer categories such as: " . implode(', ', $knowledge_base['categories']);
    }

    return "I can tell you about how Joraki Ventures works, its categories, or features. What would you like to know?";
}

// ============================================================
// 📨 Handle chatbot messages
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    $msg = trim($_POST['message']);
    if ($msg === '') {
        echo json_encode(['success' => false, 'error' => 'Empty message']);
        exit;
    }

    $_SESSION['chat_history'][] = ['type' => 'user', 'message' => $msg];
    $response = getChatbotResponse($msg, $knowledge_base);
    $_SESSION['chat_history'][] = ['type' => 'bot', 'message' => $response];

    echo json_encode(['success' => true, 'response' => $response]);
    exit;
}

// ============================================================
// 📊 Chart API Endpoint
// ============================================================

if (isset($_GET['action']) && $_GET['action'] === 'chart_data') {
    ob_clean();
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');

    try {
        // Example: query your DB for chart data
        $stmt = $pdo->query("SELECT month, sales FROM sales_data ORDER BY id ASC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $data]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// ============================================================
// 🧹 Clear chat history
// ============================================================
if (isset($_GET['clear'])) {
    $_SESSION['chat_history'] = [];
    header('Location: chart.php');
    exit;
}
?>
