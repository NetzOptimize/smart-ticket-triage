<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Ticket;
use App\Services\TicketClassifier;

class ClassifyTicketJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60;

    /**
     * The ticket instance.
     *
     * @var \App\Models\Ticket
     */
    protected $ticket;

    /**
     * Create a new job instance.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Execute the job.
     */
    public function handle(TicketClassifier $classifier): void
    {
        // Update ticket status to processing
        $this->ticket->update(['status' => 'processing']);

        try {
            // Get classification result
            $result = $classifier->classify($this->ticket);

            // Update ticket with classification results
            $this->ticket->update([
                'category_id' => $result['category_id'],
                'confidence' => $result['confidence'],
                'status' => 'classified'
            ]);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Failed to classify ticket', [
                'ticket_id' => $this->ticket->id,
                'error' => $e->getMessage()
            ]);

            // Update ticket status to failed
            $this->ticket->update(['status' => 'failed']);

            // Re-throw the exception to trigger job retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        // Log the final failure
        \Log::error('Ticket classification job failed after all retries', [
            'ticket_id' => $this->ticket->id,
            'error' => $exception->getMessage()
        ]);

        // Update ticket status to failed
        $this->ticket->update(['status' => 'failed']);
    }
}
