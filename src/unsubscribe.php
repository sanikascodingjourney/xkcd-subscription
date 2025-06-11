<?php
require_once 'functions.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    if (isset($_POST["unsubscribe_email"]) && !isset($_POST["verification_code"])) {
        $email = trim($_POST["unsubscribe_email"]);

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $code = generateVerificationCode();

            
            $codes = [];
            $file = __DIR__ . '/verification_codes.json';

            if (file_exists($file)) {
                $codes = json_decode(file_get_contents($file), true);
            }

            $codes[$email] = $code;
            file_put_contents($file, json_encode($codes, JSON_PRETTY_PRINT));

            sendUnsubscribeVerificationEmail($email, $code);
        } else {
            echo "❌ Invalid email format.";
        }
    }

    
    elseif (isset($_POST["verification_code"])) {
        $email = trim($_POST["unsubscribe_email"] ?? '');
        $code = trim($_POST["verification_code"]);

        if ($email && $code) {
            if (verifyCode($email, $code)) {
                unsubscribeEmail($email);
                echo "✅ Email unsubscribed successfully.";
            } else {
                echo "❌ Invalid verification code.";
            }
        } else {
            echo "❌ Enter email and code both.";
        }
    }
}
?>

<h2>Unsubscribe</h2>
<form method="post">
    <input type="email" name="unsubscribe_email" required placeholder="Your email">
    <button id="submit-unsubscribe">Unsubscribe</button>
</form>

<h2>Enter Unsubscribe Code</h2>
<form method="post">
    <input type="email" name="unsubscribe_email" required placeholder="Same email">
    <input type="text" name="verification_code" maxlength="6" required>
    <button id="submit-verification">Verify</button>
</form>
