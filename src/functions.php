<?php

function generateVerificationCode() {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

function sendVerificationEmail($email, $code) {
    $file = __DIR__ . '/verification_codes.json';

  
    $codes = [];
    if (file_exists($file)) {
        $json = file_get_contents($file);
        $codes = json_decode($json, true);
        if (!is_array($codes)) {
            $codes = [];
        }
    }

    
    $codes[$email] = $code;

    $result = file_put_contents($file, json_encode($codes, JSON_PRETTY_PRINT));

    if ($result === false) {
        echo "❌ Failed to save code.";
    } else {
        echo "✅ Code saved for $email: $code";
    }

   
    return true;
}

function verifyCode($email, $code) {
    $file = __DIR__ . '/verification_codes.json';

    if (!file_exists($file)) return false;

    $codes = json_decode(file_get_contents($file), true);

    if (isset($codes[$email]) && $codes[$email] === $code) {
        return true;
    }

    return false;
}

function registerEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';

    
    $existing = [];
    if (file_exists($file)) {
        $existing = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }

    if (!in_array($email, $existing)) {
        file_put_contents($file, $email . PHP_EOL, FILE_APPEND);
    }
}

function unsubscribeEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';

    if (!file_exists($file)) return;

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $updated = array_filter($emails, function($line) use ($email) {
        return trim($line) !== trim($email);
    });

    file_put_contents($file, implode(PHP_EOL, $updated) . PHP_EOL);
}

function sendUnsubscribeVerificationEmail($email, $code) {
    $subject = "Confirm Un-subscription";
    $body = "<p>To confirm un-subscription, use this code: <strong>$code</strong></p>";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: no-reply@example.com";

    mail($email, $subject, $body, $headers);

    echo "🟡 Confirmation code sent to $email (check terminal if mail not working)<br>";
}

function fetchAndFormatXKCDData() {
    
    $randomComicId = rand(1, 2800); 

    
    $url = "https://xkcd.com/$randomComicId/info.0.json";

    $json = @file_get_contents($url);

    if ($json === FALSE) {
        return "<p>Failed to fetch XKCD comic.</p>";
    }

    $data = json_decode($json, true);

    if (!$data || !isset($data['img'])) {
        return "<p>Invalid XKCD comic data.</p>";
    }

    $title = htmlspecialchars($data['safe_title']);
    $imgUrl = htmlspecialchars($data['img']);
    $alt = htmlspecialchars($data['alt']);

    
    return "<h2>XKCD Comic: $title</h2><img src=\"$imgUrl\" alt=\"$alt\"><p>$alt</p>";
}


function sendXKCDUpdatesToSubscribers() {
    $file = __DIR__ . '/registered_emails.txt';

    if (!file_exists($file)) {
        return;
    }

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $comicData = fetchAndFormatXKCDData();

    foreach ($emails as $email) {
        $subject = "Your XKCD Comic";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: no-reply@example.com\r\n";

        $unsubscribeLink = "http://localhost/xkcd-subscription/src/unsubscribe.php";
        $body = $comicData . "<p><a href=\"$unsubscribeLink\" id=\"unsubscribe-button\">Unsubscribe</a></p>";

        
        mail($email, $subject, $body, $headers);

        
        file_put_contents(__DIR__ . '/cron_log.txt', date('Y-m-d H:i:s') . " - Sent XKCD to $email\n", FILE_APPEND);
    }
}









