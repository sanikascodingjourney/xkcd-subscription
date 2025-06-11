
CRON_JOB="0 9 * * * php $(pwd)/cron.php"
(crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -
echo "CRON job set to run every day at 9 AM"
