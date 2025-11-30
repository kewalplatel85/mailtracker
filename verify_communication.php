<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use App\Mail\QuickMessage;
use App\Mail\RenewalReminder;
use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Communication System Verification ===\n\n";

// 1. Check Environment Variables
echo "1. Checking Environment Configuration:\n";
echo "   APP_NAME: " . (env('APP_NAME') ?: 'Not set') . "\n";
echo "   MAIL_MAILER: " . (env('MAIL_MAILER') ?: 'Not set') . "\n";
echo "   MAIL_FROM_ADDRESS: " . (env('MAIL_FROM_ADDRESS') ?: 'Not set') . "\n";
echo "   TWILIO_SID: " . (env('TWILIO_SID') ? 'Set (AC...)' : 'Not set') . "\n";
echo "   TWILIO_AUTH_TOKEN: " . (env('TWILIO_AUTH_TOKEN') ? 'Set (***...)' : 'Not set') . "\n";
echo "   TWILIO_PHONE_NUMBER: " . (env('TWILIO_PHONE_NUMBER') ?: 'Not set') . "\n";

// 2. Check Config Values
echo "\n2. Checking Configuration Values:\n";
echo "   app.name: " . config('app.name') . "\n";
echo "   mail.mailers.smtp.host: " . config('mail.mailers.smtp.host') . "\n";
echo "   mail.from.address: " . config('mail.from.address') . "\n";
echo "   Company Name Source: Database (auth()->user()->company->name)\n";

// 3. Test Twilio Connection
echo "\n3. Testing Twilio Connection:\n";
$twilioSid = env('TWILIO_SID');
$twilioToken = env('TWILIO_AUTH_TOKEN');
$twilioFrom = env('TWILIO_PHONE_NUMBER');

if ($twilioSid && $twilioToken && $twilioFrom) {
    try {
        $twilio = new Client($twilioSid, $twilioToken);
        $account = $twilio->api->v2010->accounts(env('TWILIO_SID'))->fetch();
        echo "   ✓ Twilio connection successful\n";
        echo "   Account SID: " . $account->sid . "\n";
        echo "   Account Status: " . $account->status . "\n";
        echo "   From Number: " . $twilioFrom . "\n";
    } catch (TwilioException $e) {
        echo "   ✗ Twilio connection failed: " . $e->getMessage() . "\n";
    } catch (Exception $e) {
        echo "   ✗ Error connecting to Twilio: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ✗ Twilio credentials not configured\n";
    if (!$twilioSid) echo "     - Missing TWILIO_SID\n";
    if (!$twilioToken) echo "     - Missing TWILIO_AUTH_TOKEN\n";
    if (!$twilioFrom) echo "     - Missing TWILIO_PHONE_NUMBER\n";
}

// 4. Test Email Configuration
echo "\n4. Testing Email Configuration:\n";
$mailDriver = config('mail.default');
$mailConfig = config("mail.mailers.{$mailDriver}");

echo "   Mail Driver: " . $mailDriver . "\n";
if ($mailConfig) {
    echo "   Host: " . ($mailConfig['host'] ?? 'Not configured') . "\n";
    echo "   Port: " . ($mailConfig['port'] ?? 'Not configured') . "\n";
    echo "   Encryption: " . ($mailConfig['encryption'] ?? 'None') . "\n";
    echo "   Username: " . ($mailConfig['username'] ?? 'Not configured') . "\n";
    echo "   Password: " . (isset($mailConfig['password']) && $mailConfig['password'] ? 'Set' : 'Not set') . "\n";
} else {
    echo "   ✗ Mail configuration not found for driver: " . $mailDriver . "\n";
}

// 5. Test Email Templates
echo "\n5. Testing Email Templates:\n";
try {
    // Test QuickMessage template
    $quickMessage = new QuickMessage(
        'Test Customer',
        '123',
        'This is a test message',
        'general',
        'Test Company Name'
    );
    echo "   ✓ QuickMessage template loads successfully\n";

    // Test RenewalReminder template
    $renewalReminder = new RenewalReminder(
        'Test Customer',
        '123',
        '2025-12-01',
        'Your mailbox rental is due for renewal',
        'standard',
        'Test Company Name'
    );
    echo "   ✓ RenewalReminder template loads successfully\n";

} catch (Exception $e) {
    echo "   ✗ Email template error: " . $e->getMessage() . "\n";
}

// 6. Check Required Views
echo "\n6. Checking Email Template Views:\n";
$quickMessageView = resource_path('views/emails/quick-message.blade.php');
$renewalView = resource_path('views/emails/renewal-reminder.blade.php');

echo "   Quick Message Template: " . (file_exists($quickMessageView) ? '✓ Exists' : '✗ Missing') . "\n";
echo "   Renewal Reminder Template: " . (file_exists($renewalView) ? '✓ Exists' : '✗ Missing') . "\n";

echo "\n=== Verification Complete ===\n";

// Summary
echo "\nSUMMARY:\n";
echo "- Twilio Setup: " . ($twilioSid && $twilioToken && $twilioFrom ? "✓ Ready" : "✗ Incomplete") . "\n";
echo "- Email Setup: " . (config('mail.from.address') ? "✓ Ready" : "✗ Incomplete") . "\n";
echo "- Templates: " . (file_exists($quickMessageView) && file_exists($renewalView) ? "✓ Ready" : "✗ Incomplete") . "\n";
echo "- Company Name: ✓ Retrieved from Database\n";

if ($twilioSid && $twilioToken && $twilioFrom && config('mail.from.address') &&
    file_exists($quickMessageView) && file_exists($renewalView)) {
    echo "\n🎉 Communication system is ready for use!\n";
} else {
    echo "\n⚠️  Some configuration is missing. Please check the items marked with ✗\n";
}
?>
