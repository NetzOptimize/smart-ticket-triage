<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\TicketClassifier;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    protected $classifier;

    public function __construct(TicketClassifier $classifier)
    {
        $this->classifier = $classifier;
    }

    /**
     * Display a listing of the tickets.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Ticket::with('category');

        // Apply search filter
        if ($request->has('search') && $request->get('search') !== '') {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        // Apply category filter only if a specific category is selected
        if ($request->has('category') && $request->get('category') !== '' && $request->get('category') !== null) {
            $category = $request->get('category');
            if ($category === 'Unclassified') {
                $query->whereNull('category_id');
            } else {
                $query->where('category_id', $category);
            }
        }

        // Apply sorting
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Get paginated results
        $tickets = $query->paginate(10)->withQueryString();

        return response()->json([
            'data' => $tickets->items(),
            'current_page' => $tickets->currentPage(),
            'last_page' => $tickets->lastPage(),
            'per_page' => $tickets->perPage(),
            'total' => $tickets->total(),
            'next_page_url' => $tickets->nextPageUrl(),
            'prev_page_url' => $tickets->previousPageUrl(),
        ]);
    }

    /**
     * Store a newly created ticket in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'category_id' => 'nullable|exists:categories,id'
        ]);

        $ticket = Ticket::create([
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'user_id' => Auth::id(),
            'category_id' => $validated['category_id'] ?? null,
            'status' => 'open'
        ]);

        return response()->json($ticket->load('category'), 201);
    }

    /**
     * Classify a ticket.
     *
     * @param Ticket $ticket
     * @return \Illuminate\Http\JsonResponse
     */
    public function classify(Ticket $ticket)
    {
        $result = $this->classifier->classify($ticket);

        $ticket->update([
            'category_id' => $result['category_id'],
            'confidence' => $result['confidence']
        ]);

        return response()->json($ticket->load('category'));
    }
}
