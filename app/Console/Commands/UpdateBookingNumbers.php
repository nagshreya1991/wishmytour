<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\User\app\Models\AgentDetails;
use Modules\Package\app\Models\Package;

class UpdateBookingNumbers extends Command
{
    protected $signature = 'update:booking-numbers';
    protected $description = 'Update blank booking numbers for existing bookings';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $packages = Package::get();

        foreach ($packages as $package) {
            $package->package_code = $this->generatePackageId($package->user_id, $package->id);
            $package->save();
        }

        $this->info('Booking numbers updated successfully.');
    }

    private function generatePackageId($vendorId, $packageId)
    {
        $prefix = 'WMTH'; // Fixed prefix for package ID
        $randomAlphanumeric = strtoupper(substr(md5(mt_rand()), 0, 5)); // 5 random alphanumeric characters
        return $prefix . '-' . $vendorId . '-' . $packageId . '-' . $randomAlphanumeric;
    }
}
