<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Booking\app\Http\Controllers\BookingController;

class GenerateCommissionInvoices extends Command
{
    protected $signature = 'invoices:generate';

    protected $description = 'Generate commission invoices at the end of each month.';

    public function handle()
    {
        try {
            // Instantiate BookingController to access the commissionInvoice method
            $controller = new BookingController();
            $controller->commissionInvoice();

            $this->info('Commission invoices generated successfully.');
        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }
}