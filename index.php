<?php
// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method not allowed');
}

// Get the submitted code from the form
$submittedCode = $_POST['code'] ?? '';

// --- IMPORTANT: These are read from Render Environment Variables ---
$correctCode = getenv('SECRET_CODE');
$destinationUrl = getenv('DESTINATION_URL');
// -----------------------------------------------------------------

// Basic validation
if (!$correctCode || !$destinationUrl) {
    error_log("Server misconfiguration: Missing SECRET_CODE or DESTINATION_URL");
    showError("Server configuration error.");
    exit;
}

// Validate code format (4 digits)
if (!preg_match('/^\d{4}$/', $submittedCode)) {
    showError('Invalid code format. Please enter exactly 4 digits.');
    exit;
}

// Securely compare the codes
if (hash_equals($correctCode, $submittedCode)) {
    // Code is correct! Redirect the user to the hidden destination.
    header("Location: " . $destinationUrl, true, 302);
    exit;
} else {
    // Wrong code
    showError('Incorrect access code.');
    exit;
}

/**
 * Displays a simple error page and a link to go back.
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
