<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run()
    {
        $user = User::first() ?? User::factory()->create();

        $tickets = [
            [
                'subject' => 'Billing Issue: Incorrect Invoice Amount',
                'body' => 'I received an invoice for $500, but I believe it should be $450 based on our agreement. Can you please review this?',
                'status' => 'open',
                'category_id' => Category::where('name', 'Billing')->first()->id,
                'confidence' => 0.95
            ],
            [
                'subject' => 'Bug: Login Page Not Working',
                'body' => 'When I try to log in, I get a 500 error. This started happening after the last update.',
                'status' => 'open',
                'category_id' => Category::where('name', 'Bug')->first()->id,
                'confidence' => 0.98
            ],
            [
                'subject' => 'Feature Request: Dark Mode',
                'body' => 'It would be great to have a dark mode option for the dashboard. This would help reduce eye strain during night-time usage.',
                'status' => 'open',
                'category_id' => Category::where('name', 'Feature Request')->first()->id,
                'confidence' => 0.92
            ],
            [
                'subject' => 'General Question About API Usage',
                'body' => 'I have a question about the rate limits for the API. What are the current limits and how can I monitor my usage?',
                'status' => 'open',
                'category_id' => Category::where('name', 'General')->first()->id,
                'confidence' => 0.88
            ],
            [
                'subject' => 'Payment Method Update',
                'body' => 'I need to update my credit card information for future payments.',
                'status' => 'open',
                'category_id' => Category::where('name', 'Billing')->first()->id,
                'confidence' => 0.94
            ],
            [
                'subject' => 'Error in Report Generation',
                'body' => 'The monthly report is showing incorrect data in the revenue section. The numbers don\'t match our records.',
                'status' => 'open',
                'category_id' => Category::where('name', 'Bug')->first()->id,
                'confidence' => 0.96
            ],
            [
                'subject' => 'Request for Bulk Export Feature',
                'body' => 'We need the ability to export multiple reports at once. Currently, we have to do it one by one which is time-consuming.',
                'status' => 'open',
                'category_id' => Category::where('name', 'Feature Request')->first()->id,
                'confidence' => 0.91
            ],
            [
                'subject' => 'Documentation Update Needed',
                'body' => 'The API documentation seems to be outdated. Some endpoints are missing and others have changed.',
                'status' => 'open',
                'category_id' => Category::where('name', 'General')->first()->id,
                'confidence' => 0.87
            ]
        ];

        foreach ($tickets as $ticket) {
            Ticket::create(array_merge($ticket, ['user_id' => $user->id]));
        }
    }
} 