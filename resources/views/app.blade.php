<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Smart Ticket Triage') }}</title>

    <!-- Scripts and Styles -->
    @vite(['resources/js/app.js'])
</head>
<body>
    <div id="app"></div>
</body>
</html> 