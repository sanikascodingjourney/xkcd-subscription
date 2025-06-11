<?php
require_once 'functions.php';
sendXKCDUpdatesToSubscribers();

require_once 'functions.php';

echo "🚀 Cron job is running...\n";

sendXKCDUpdatesToSubscribers();

echo "✅ XKCD comic sent to all registered users.\n";

?>

