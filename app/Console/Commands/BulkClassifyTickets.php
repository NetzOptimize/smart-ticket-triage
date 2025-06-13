<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Jobs\ClassifyTicketJob;
use Illuminate\Console\Command;

class BulkClassifyTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:bulk-classify 
                            {--status=open : Filter tickets by status}
                            {--limit=100 : Maximum number of tickets to process}
                            {--chunk=10 : Number of tickets to process in each chunk}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Classify unclassified tickets in bulk using job queues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $status = $this->option('status');
        $limit = (int) $this->option('limit');
        $chunkSize = (int) $this->option('chunk');

        $query = Ticket::whereNull('category_id')
            ->where('status', $status);

        $totalTickets = $query->count();

        if ($totalTickets === 0) {
            $this->info('No unclassified tickets found.');
            return;
        }

        $this->info("Found {$totalTickets} unclassified tickets.");
        $this->info("Processing up to {$limit} tickets in chunks of {$chunkSize}...");

        $bar = $this->output->createProgressBar(min($totalTickets, $limit));
        $bar->start();

        $query->take($limit)
            ->chunk($chunkSize, function ($tickets) use ($bar) {
                foreach ($tickets as $ticket) {
                    ClassifyTicketJob::dispatch($ticket);
                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine(2);
        $this->info('Bulk classification jobs have been queued successfully!');
        $this->info('Run "php artisan queue:work" to process the jobs.');
    }
} 