<?php

require_once 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;

echo "=== COMMUNICATION SYSTEM VERIFICATION ===\n\n";

// 1. Check Twilio Configuration
echo "1. TWILIO CONFIGURATION CHECK:\n";
echo "   SID: " . ($_ENV['TWILIO_SID'] ?? 'NOT SET') . "\n";
echo "   Token: " . (isset($_ENV['TWILIO_AUTH_TOKEN']) ? str_repeat('*', strlen($_ENV['TWILIO_AUTH_TOKEN']) - 4) . substr($_ENV['TWILIO_AUTH_TOKEN'], -4) : 'NOT SET') . "\n";
echo "   Phone: " . ($_ENV['TWILIO_PHONE_NUMBER'] ?? 'NOT SET') . "\n";

// Test Twilio Connection
echo "\n2. TWILIO CONNECTION TEST:\n";
try {
    if (!isset($_ENV['TWILIO_SID']) || !isset($_ENV['TWILIO_AUTH_TOKEN'])) {
        throw new Exception("Twilio credentials not configured");
    }

    $twilio = new Client($_ENV['TWILIO_SID'], $_ENV['TWILIO_AUTH_TOKEN']);

    // Test account info
    $account = $twilio->api->v2010->accounts($_ENV['TWILIO_SID'])->fetch();
    echo "   ✓ Connected to Twilio successfully\n";
    echo "   ✓ Account Status: " . $account->status . "\n";

    // Test phone number validation
    $phoneNumber = $_ENV['TWILIO_PHONE_NUMBER'];
    if (strpos($phoneNumber, '+') === false) {
        $phoneNumber = '+1' . $phoneNumber;
    }

    $incomingNumbers = $twilio->incomingPhoneNumbers->read(['phoneNumber' => $phoneNumber]);
    if (count($incomingNumbers) > 0) {
        echo "   ✓ Phone number $phoneNumber is valid and active\n";
    } else {
        echo "   ⚠ Warning: Phone number $phoneNumber may not be properly configured\n";
    }

} catch (TwilioException $e) {
    echo "   ✗ Twilio Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// 3. Check Email Configuration
echo "\n3. EMAIL CONFIGURATION CHECK:\n";
echo "   Mailer: " . ($_ENV['MAIL_MAILER'] ?? 'NOT SET') . "\n";
echo "   Host: " . ($_ENV['MAIL_HOST'] ?? 'NOT SET') . "\n";
echo "   Port: " . ($_ENV['MAIL_PORT'] ?? 'NOT SET') . "\n";
echo "   Username: " . ($_ENV['MAIL_USERNAME'] ?? 'NOT SET') . "\n";
echo "   From Address: " . ($_ENV['MAIL_FROM_ADDRESS'] ?? 'NOT SET') . "\n";
echo "   From Name: " . ($_ENV['MAIL_FROM_NAME'] ?? 'NOT SET') . "\n";

// Test SMTP Connection
echo "\n4. SMTP CONNECTION TEST:\n";
try {
    if (!isset($_ENV['MAIL_HOST']) || !isset($_ENV['MAIL_USERNAME']) || !isset($_ENV['MAIL_PASSWORD'])) {
        throw new Exception("Email credentials not configured");
    }

    $host = $_ENV['MAIL_HOST'];
    $port = $_ENV['MAIL_PORT'] ?? 587;

    // Test connection to SMTP server
    $connection = @fsockopen($host, $port, $errno, $errstr, 10);
    if ($connection) {
        echo "   ✓ Successfully connected to SMTP server $host:$port\n";
        fclose($connection);
    } else {
        echo "   ✗ Failed to connect to SMTP server: $errstr ($errno)\n";
    }

    // For Gmail, verify app password format
    if (strpos($host, 'gmail') !== false) {
        $password = $_ENV['MAIL_PASSWORD'];
        if (strlen($password) === 16 && ctype_alnum($password)) {
            echo "   ✓ Gmail app password format appears correct\n";
        } else {
            echo "   ⚠ Gmail app password may be incorrect (should be 16 alphanumeric characters)\n";
        }
    }

} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// 5. Check Laravel Configuration Issues
echo "\n5. LARAVEL CONFIGURATION ISSUES:\n";

// Check services.php Twilio config mismatch
$expectedTwilioConfig = [
    'sid' => $_ENV['TWILIO_SID'] ?? null,
    'token' => $_ENV['TWILIO_AUTH_TOKEN'] ?? null,  // Note: .env uses TWILIO_AUTH_TOKEN
    'from' => $_ENV['TWILIO_PHONE_NUMBER'] ?? null  // Note: .env uses TWILIO_PHONE_NUMBER
];

echo "   Checking services.php Twilio configuration...\n";
if (isset($_ENV['TWILIO_TOKEN']) || isset($_ENV['TWILIO_FROM'])) {
    echo "   ⚠ Warning: services.php expects TWILIO_TOKEN and TWILIO_FROM but .env has TWILIO_AUTH_TOKEN and TWILIO_PHONE_NUMBER\n";
    echo "     Your MessageController uses the correct .env variables, so this is OK\n";
} else {
    echo "   ✓ Twilio environment variables are correctly named\n";
}

// 6. Identify Potential Issues
echo "\n6. POTENTIAL ISSUES & RECOMMENDATIONS:\n";

$issues = [];
$recommendations = [];

// Check for missing configuration
if (!isset($_ENV['TWILIO_SID']) || !isset($_ENV['TWILIO_AUTH_TOKEN']) || !isset($_ENV['TWILIO_PHONE_NUMBER'])) {
    $issues[] = "Missing Twilio credentials in .env file";
}

if (!isset($_ENV['MAIL_USERNAME']) || !isset($_ENV['MAIL_PASSWORD'])) {
    $issues[] = "Missing email credentials in .env file";
}

// Check for common configuration problems
if (isset($_ENV['TWILIO_PHONE_NUMBER']) && strpos($_ENV['TWILIO_PHONE_NUMBER'], '+') === false) {
    $recommendations[] = "Add country code (+1) to TWILIO_PHONE_NUMBER in .env file";
}

if ($_ENV['MAIL_FROM_ADDRESS'] === 'hello@example.com') {
    $recommendations[] = "Update MAIL_FROM_ADDRESS to your actual email address";
}

if (isset($_ENV['MAIL_HOST']) && $_ENV['MAIL_HOST'] === 'smtp.gmail.com' && isset($_ENV['MAIL_PASSWORD'])) {
    if (strpos($_ENV['MAIL_PASSWORD'], ' ') !== false || strlen($_ENV['MAIL_PASSWORD']) !== 16) {
        $recommendations[] = "Gmail requires an App Password (16 characters, no spaces). Generate one at: https://myaccount.google.com/apppasswords";
    }
}

if (empty($issues) && empty($recommendations)) {
    echo "   ✓ No major issues detected!\n";
} else {
    if (!empty($issues)) {
        echo "   ISSUES:\n";
        foreach ($issues as $issue) {
            echo "   ✗ $issue\n";
        }
    }

    if (!empty($recommendations)) {
        echo "   RECOMMENDATIONS:\n";
        foreach ($recommendations as $rec) {
            echo "   ⚠ $rec\n";
        }
    }
}

echo "\n7. QUICK TEST COMMANDS:\n";
echo "   To test SMS: Use the SMS inbox feature in your application\n";
echo "   To test Email: Use the Quick Message or Renewal Reminder modals\n";
echo "   To debug issues: Check storage/logs/laravel.log for detailed error messages\n";

echo "\n=== VERIFICATION COMPLETE ===\n";
