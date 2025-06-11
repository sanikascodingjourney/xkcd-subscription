<?php
require_once 'functions.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    if (isset($_POST["email"]) && !isset($_POST["verification_code"])) {
        $email = trim($_POST["email"]);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $code = generateVerificationCode();
            sendVerificationEmail($email, $code);
        } else {
            echo "❌ Invalid email format.";
        }
    }

   
    elseif (isset($_POST["verification_code"])) {
        $email = trim($_POST["email"] ?? '');
        $code = trim($_POST["verification_code"]);

        if ($email && $code) {
            if (verifyCode($email, $code)) {
                registerEmail($email);
                echo "✅ Email verified and subscribed!";
            } else {
                echo "❌ Invalid verification code.";
            }
        } else {
            echo "❌ Please enter both email and code.";
        }
    }
}
?>
<h2>Subscribe</h2>
<form method="post">
    <input type="email" name="email" required>
    <button id="submit-email">Submit</button>
</form>

<h2>Enter Verification Code</h2>
<form method="post">
    <input type="email" name="email" required placeholder="Enter same email again">
    <input type="text" name="verification_code" maxlength="6" required>
    <button id="submit-verification">Verify</button>
</form>
