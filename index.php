<?php
// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 0);
error_reporting(0);

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method not allowed');
}

// Get the submitted code from form input
$submittedCode = $_POST['code'] ?? '';

// Retrieve the correct code from environment variable
$correctCode = getenv('SECRET_CODE'); // Set this in Render dashboard

$destinationUrl = getenv('DESTINATION_URL');
if (!$destinationUrl) {
    $destinationUrl = 'https://jovrin.digital/private';
}

// Validate code (4 digits)
if (!preg_match('/^\d{4}$/', $submittedCode)) {
    // Invalid format – show error page
    showError('Invalid code format. Please enter exactly 4 digits.');
    exit;
}

// Constant-time comparison to prevent timing attacks
if (hash_equals($correctCode, $submittedCode)) {
    // Code correct – redirect to destination
    header("Location: " . $destinationUrl, true, 302);
    exit;
} else {
    // Wrong code
    showError('Incorrect access code.');
    exit;
}

/**
 * Display a simple error page and return to the gateway
 */
function showError($message) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Access Error</title>
        <style>
            body { font-family: 'Segoe UI', sans-serif; background: #f3f2f1; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
            .error-box { background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center; max-width: 400px; border-top: 4px solid #d13438; }
            h2 { color: #d13438; margin-top: 0; }
            p { color: #333; }
            a { display: inline-block; margin-top: 1rem; color: #0078d4; text-decoration: none; }
            a:hover { text-decoration: underline; }
        </style>
    </head>
    <body>
        <div class="error-box">
            <h2>⛔ Access Denied</h2>
            <p><?php echo htmlspecialchars($message); ?></p>
            <a href="javascript:history.back()">← Try again</a>
        </div>
    </body>
    </html>
    <?php
}
