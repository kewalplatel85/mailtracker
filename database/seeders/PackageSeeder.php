<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'customer_name' => 'John Doe',
                'phone_number' => '(555) 123-4567',
                'mailbox_number' => 123,
                'tracking_number' => '1Z999AA1234567890',
                'status' => 'Incoming',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_name' => 'Jane Smith',
                'phone_number' => '(555) 987-6543',
                'mailbox_number' => 456,
                'tracking_number' => '1Z888BB0987654321',
                'status' => 'Incoming',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_name' => 'Bob Johnson',
                'phone_number' => '(555) 555-1234',
                'mailbox_number' => 789,
                'tracking_number' => '1Z777CC1122334455',
                'status' => 'Incoming',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_name' => 'Alice Brown',
                'phone_number' => '(555) 444-9876',
                'mailbox_number' => 321,
                'tracking_number' => '1Z666DD5544332211',
                'status' => 'Outgoing',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_name' => 'John Doe',
                'phone_number' => '(555) 123-4567',
                'mailbox_number' => 123,
                'tracking_number' => '1Z111EE9988776655',
                'status' => 'Incoming',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }
    }
}
