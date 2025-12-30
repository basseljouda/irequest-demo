<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;

class GenerateMonthlySummary extends Command
{
    protected $signature = 'report:generate-monthly-summary';
    protected $description = 'Generate and store monthly revenue summary for faster dashboard loading';

    public function handle()
    {
        $this->info('Generating monthly revenue summary...');

        // Delete existing data for the current year to prevent duplicates
        DB::table('monthly_revenue_summary')->delete();

        // Run the heavy query ONCE to populate the summary table
        DB::insert("
            INSERT INTO monthly_revenue_summary 
        ");

        $this->info('Monthly revenue summary updated successfully.');
    }
}
