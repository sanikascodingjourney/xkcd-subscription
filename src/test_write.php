<?php
$file = __DIR__ . '/verification_codes.json';
$data = ['test@example.com' => '123456'];

$result = file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

if ($result === false) {
    echo "❌ Failed to write to verification_codes.json";
} else {
    echo "✅ Successfully wrote to verification_codes.json";
}
