<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Ticket;
use App\Models\Category;
use OpenAI\Laravel\Facades\OpenAI;

class ClassifyTicketJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $enabled = config('openai.classify_enabled', true);

        $categoryName = 'General';
        $confidence = 0.0;

        if ($enabled) {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a ticket classifier. Classify each ticket into one of these: Billing, Bug, Feature Request, or General. Respond with JSON: {"category":"...", "confidence":0.xx}'],
                    ['role' => 'user', 'content' => "Subject: {$this->ticket->subject}\n\nBody: {$this->ticket->body}"],
                ],
            ]);

            $result = json_decode($response['choices'][0]['message']['content'], true);
            $categoryName = $result['category'] ?? 'General';
            $confidence = $result['confidence'] ?? 0.7;
        } else {
            $categories = ['Billing', 'Bug', 'Feature Request', 'General'];
            $categoryName = $categories[array_rand($categories)];
            $confidence = rand(70, 100) / 100;
        }

        $category = Category::where('name', $categoryName)->first();

        $this->ticket->update([
            'category_id' => $category?->id,
            'confidence' => $confidence,
            'status' => 'classified',
        ]);
    }
}
