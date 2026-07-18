<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bang — Open-Source Firearm Inventory</title>
    <meta name="description" content="An open-source app for tracking firearms, ammunition, magazines, accessories, training, and range activity.">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Bang">
    <meta property="og:title" content="Bang — Open-Source Firearm Inventory">
    <meta property="og:description" content="An open-source app for tracking firearms, ammunition, magazines, accessories, training, and range activity.">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/social-preview.png') }}">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Bang — Open-source firearm inventory">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Bang — Open-Source Firearm Inventory">
    <meta name="twitter:description" content="An open-source app for tracking firearms, ammunition, magazines, accessories, training, and range activity.">
    <meta name="twitter:image" content="{{ asset('images/social-preview.png') }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Schibsted+Grotesk:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=Hanken+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    @vite('resources/front-end/src/main.js')
</head>
<body>
    <div id="app"></div>
</body>
</html>
