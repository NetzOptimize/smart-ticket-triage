# Smart Ticket Triage

A Laravel-based ticket management system with AI-powered ticket classification using OpenAI's GPT API.

## Features

- 🎫 Ticket Management
  - Create and manage support tickets
  - View ticket history and status
  - Server-side pagination and search
  - Category-based filtering
  - Sortable columns

- 🤖 AI-Powered Classification
  - Automatic ticket categorization using OpenAI's GPT
  - Categories: Billing, Bug, Feature Request, General
  - Confidence scoring for classifications
  - Fallback to random classification if AI is disabled
  - Bulk classification using job queues

- 🎨 Modern UI
  - Clean and responsive design
  - Real-time search with debouncing
  - Interactive filters and sorting
  - Status indicators and category badges

## Requirements

- PHP 8.1 or higher
- Composer
- Node.js and NPM
- MySQL or PostgreSQL
- OpenAI API key (for AI classification)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/smart-ticket-triage.git
cd smart-ticket-triage
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install JavaScript dependencies:
```bash
npm install
```

4. Copy the environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Configure your database in `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_ticket_triage
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

7. Configure OpenAI API (optional):
```
OPENAI_API_KEY=your_openai_api_key
OPENAI_ENABLED=true
```

8. Run migrations and seeders:
```bash
php artisan migrate --seed
```

9. Start the development server:
```bash
php artisan serve
```

10. In a separate terminal, start Vite:
```bash
npm run dev
```

## Usage

1. Access the application at `http://localhost:8000`
2. Create a new ticket using the "New Ticket" button
3. View and manage tickets in the ticket list
4. Use the search and filter options to find specific tickets
5. Click "Classify" on unclassified tickets to use AI classification

### Bulk Classification

The application includes a command-line tool for bulk classification of tickets using job queues. This is useful for processing large numbers of tickets efficiently.

To use bulk classification:

1. Start the queue worker:
```bash
php artisan queue:work
```

2. Run the bulk classification command:
```bash
# Basic usage (process up to 100 tickets)
php artisan tickets:bulk-classify

# Process a specific number of tickets
php artisan tickets:bulk-classify --limit=50

# Process tickets with a specific status
php artisan tickets:bulk-classify --status=open

# Customize chunk size for better performance
php artisan tickets:bulk-classify --chunk=20

# Combine options
php artisan tickets:bulk-classify --status=open --limit=200 --chunk=25
```

Command Options:
- `--status`: Filter tickets by status (default: open)
- `--limit`: Maximum number of tickets to process (default: 100)
- `--chunk`: Number of tickets to process in each chunk (default: 10)


## API Endpoints

- `GET /api/tickets` - List tickets with pagination, search, and filters
- `POST /api/tickets` - Create a new ticket
- `GET /api/tickets/{id}` - Get ticket details
- `POST /api/tickets/{id}/classify` - Classify a ticket using AI

## Development

### Running Tests
```bash
php artisan test
```

### Building Assets
```bash
npm run build
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/feature-name`)
3. Commit your changes (`git commit -m 'Add some new feature'`)
4. Push to the branch (`git push origin feature/feature-name`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments

- [Laravel](https://laravel.com)
- [Vue.js](https://vuejs.org)
- [OpenAI](https://openai.com)
- [Tailwind CSS](https://tailwindcss.com)
