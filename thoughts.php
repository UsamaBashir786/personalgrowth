<?php
// Include database configuration
require_once 'config.php';

// Get all thoughts
$thoughtsQuery = "SELECT * FROM thoughts ORDER BY date DESC";
$thoughtsResult = $conn->query($thoughtsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Thoughts | Elevate - Personal Growth Tracker</title>
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
                <div class="hidden md:flex space-x-6">
                    <a href="dashboard.php" class="nav-link">Dashboard</a>
                    <a href="tasks.php" class="nav-link">Tasks</a>
                    <a href="timetable.php" class="nav-link">Timetable</a>
                    <a href="quotes.php" class="nav-link">Quotes</a>
                    <a href="thoughts.php" class="nav-link">Thoughts</a>
                    <a href="learnings.php" class="nav-link">Learnings</a>
                </div>
                <button id="mobile-menu-button" class="md:hidden text-text-primary">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden bg-secondary py-2 shadow-inner animate-fade-in">
                <div class="container mx-auto px-4 flex flex-col space-y-3">
                    <a href="dashboard.php" class="nav-link-mobile">Dashboard</a>
                    <a href="tasks.php" class="nav-link-mobile">Tasks</a>
                    <a href="timetable.php" class="nav-link-mobile">Timetable</a>
                    <a href="quotes.php" class="nav-link-mobile">Quotes</a>
                    <a href="thoughts.php" class="nav-link-mobile">Thoughts</a>
                    <a href="learnings.php" class="nav-link-mobile">Learnings</a>
                </div>
            </div>
        </nav>

        <!-- Thoughts Content -->
        <div class="container mx-auto px-4 py-8 flex-grow">
            <h1 class="text-3xl font-bold mb-8 flex items-center">
                <i class="fas fa-brain text-accent mr-3"></i>
                Personal Thoughts Journal
            </h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Add New Thought Form -->
                <div class="card lg:col-span-1">
                    <div class="card-header">
                        <h3 class="card-title">Capture a Thought</h3>
                    </div>
                    <div class="card-body">
                        <form id="thought-form" method="post">
                            <div class="mb-6">
                                <label for="thought" class="form-label">Your Thought</label>
                                <textarea id="thought" name="thought" class="form-input" rows="6" placeholder="What's on your mind today?" required></textarea>
                            </div>
                            <button type="submit" id="thought-submit-btn" class="w-full py-3 bg-accent hover:bg-accent-light text-primary font-medium rounded-lg transition-colors">
                                Save Thought
                            </button>
                        </form>
                    </div>
                </div>

                <!-- All Thoughts -->
                <div class="card lg:col-span-2">
                    <div class="card-header">
                        <h3 class="card-title">Your Thoughts</h3>
                    </div>
                    <div class="card-body p-0">
                        <div id="thoughts-container">
                            <?php if ($thoughtsResult->num_rows > 0): ?>
                                <div class="divide-y divide-gray-700">
                                    <?php while ($thought = $thoughtsResult->fetch_assoc()): ?>
                                        <div class="p-5">
                                            <div class="flex justify-between items-start mb-3">
                                                <span class="text-sm text-text-secondary">
                                                    <i class="far fa-calendar-alt mr-1"></i>
                                                    <?php echo date('F j, Y, g:i a', strtotime($thought['date'])); ?>
                                                </span>
                                                <button onclick="deleteItem('thought', <?php echo $thought['id']; ?>)" 
                                                        class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded transition-colors">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                            <p class="text-text-primary whitespace-pre-line">
                                                <?php echo nl2br(htmlspecialchars($thought['thought'])); ?>
                                            </p>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="py-12 text-center text-text-secondary">
                                    <i class="fas fa-brain text-4xl mb-4 text-accent opacity-50"></i>
                                    <p class="text-xl mb-2">Your thoughts journal is empty</p>
                                    <p>Begin capturing your reflections and insights</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-secondary py-6 mt-auto">
            <div class="container mx-auto px-4 text-center text-text-secondary">
                <p>&copy; 2025 Elevate. All rights reserved.</p>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="scripts.js"></script>
    <script>
        // Refresh thoughts function
        function refreshThoughts() {
            // Reload the page to refresh thoughts
            window.location.reload();
        }
        
        // Fetch a random quote for the loader
        fetch('api/quotes.php?action=random')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('quote').textContent = `"${data.quote.quote}"`;
                    document.getElementById('author').textContent = `— ${data.quote.author}`;
                } else {
                    document.getElementById('quote').textContent = `"The future belongs to those who believe in the beauty of their dreams."`;
                    document.getElementById('author').textContent = `— Eleanor Roosevelt`;
                }
            })
            .catch(error => {
                // Fallback quote in case of error
                document.getElementById('quote').textContent = `"The future belongs to those who believe in the beauty of their dreams."`;
                document.getElementById('author').textContent = `— Eleanor Roosevelt`;
            });
        
        // Show main content after loader
        setTimeout(() => {
            const loader = document.getElementById('loader');
            const mainContent = document.getElementById('main-content');
            
            // Fade out loader
            loader.classList.add('opacity-0');
            
            // Show main content
            setTimeout(() => {
                mainContent.classList.remove('opacity-0');
                // Remove loader completely after fade out
                setTimeout(() => {
                    loader.classList.add('hidden');
                }, 500);
            }, 500);
        }, 1); // 3 seconds
    </script>
</body>
</html>