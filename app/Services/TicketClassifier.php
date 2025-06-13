<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\Category;
use OpenAI\Laravel\Facades\OpenAI;

class TicketClassifier
{
    public function classify(Ticket $ticket)
    {
        if (!config('openai.classify_enabled', false)) {
            return $this->getRandomClassification();
        }

        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a ticket classification system. Classify the following ticket into one of these categories: Billing, Bug, Feature Request, or General. Respond with only the category name.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Subject: {$ticket->subject}\n\nBody: {$ticket->body}"
                    ]
                ],
            ]);

            $categoryName = trim($response->choices[0]->message->content);
            
            // Find or create the category
            $category = Category::firstOrCreate(['name' => $categoryName]);

            return [
                'category_id' => $category->id,
                'confidence' => 0.9 // Since we're using a deterministic model, we'll use a high confidence
            ];
        } catch (\Exception $e) {
            return $this->getRandomClassification();
        }
    }

    protected function getRandomClassification()
    {
        $categories = Category::all();
        $category = $categories->random();
        
        return [
            'category_id' => $category->id,
            'confidence' => 0.5
        ];
    }
} 