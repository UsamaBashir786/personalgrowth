<?php
// Include database configuration
require_once 'config.php';

// Get all timetable entries
$timetableQuery = "SELECT * FROM timetable ORDER BY day, time ASC";
$timetableResult = $conn->query($timetableQuery);

// Organize entries by day
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
$timetableByDay = [];

foreach ($days as $day) {
    $timetableByDay[$day] = [];
}

if ($timetableResult->num_rows > 0) {
    while ($entry = $timetableResult->fetch_assoc()) {
        $timetableByDay[$entry['day']][] = $entry;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Timetable | Elevate - Personal Growth Tracker</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
    <!-- Configure Tailwind theme -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#111827', // dark background
                        secondary: '#1F2937', // slightly lighter dark
                        accent: '#F59E0B', // gold accent
                        'accent-light': '#FBBF24', // lighter gold
                        'text-primary': '#F3F4F6', // light text
                        'text-secondary': '#D1D5DB' // slightly darker light text
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif']
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-primary text-text-primary dark min-h-screen flex flex-col">
    <!-- Loader -->
    <div id="loader" class="fixed inset-0 flex flex-col items-center justify-center bg-primary z-50 transition-opacity duration-500">
        <div class="w-24 h-24 mb-8">
            <svg class="w-full h-full animate-pulse-slow" viewBox="0 0 100 100">
                <circle cx="50" cy="50" r="40" stroke="#F59E0B" stroke-width="3" fill="none" stroke-dasharray="160" stroke-dashoffset="0" transform="rotate(-90 50 50)">
                    <animate attributeName="stroke-dashoffset" from="160" to="0" dur="2s" repeatCount="indefinite" />
                </circle>
                <path d="M50 20 L65 45 L50 80 L35 45 Z" fill="#F59E0B" opacity="0.8">
                    <animate attributeName="opacity" values="0.8;1;0.8" dur="2s" repeatCount="indefinite" />
                </path>
            </svg>
        </div>
        <h1 class="text-4xl font-bold mb-4 text-accent animate-pulse-slow">ELEVATE</h1>
        <div class="quote-container w-full max-w-md px-8 text-center">
            <p id="quote" class="text-xl italic"></p>
            <p id="author" class="text-accent mt-2 text-right"></p>
        </div>
    </div>

    <!-- Main Content -->
    <div id="main-content" class="opacity-0 transition-opacity duration-500 flex flex-col min-h-screen">
        <!-- Navigation -->
        <nav class="bg-secondary shadow-md z-40">
            <div class="container mx-auto px-4 py-3 flex items-center justify-between">
                <a href="index.html" class="text-2xl font-bold text-accent">ELEVATE</a>
                <div class="hidden md: