<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Schedule the updateCompletedBookings method to run daily at 4 AM
        $schedule->call(function () {
            app(\Modules\Booking\app\Http\Controllers\BookingController::class)->updateCompletedBookings();
        })->dailyAt('04:00');

        // Schedule the reminderSms method to run daily at 4 AM
        $schedule->call(function () {
            app(\Modules\Booking\app\Http\Controllers\BookingController::class)->reminderSms();
        })->dailyAt('08:00');

        // Schedule the invoices:generate command to run monthly on the last day at 11:59 PM
        $schedule->command('invoices:generate')->monthlyOn(date('t'), '23:59');

        $schedule->call(function () {
            $response = Http::get(url('/customer-invoices'));

            // Log the response from the API call
            \Log::info('Customer Invoices API Response: ' . $response->body());
        })->dailyAt('08:00');
    
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
    protected function commands()
   {
    $this->load(__DIR__.'/Commands');

    require base_path('routes/console.php');

    $this->commands([
        Commands\GenerateCommissionInvoices::class,
    ]);
   }
}
