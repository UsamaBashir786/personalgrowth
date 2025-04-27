<?php
// Include database configuration
require_once 'config.php';

// Get today's date
$today = date('Y-m-d');

// Count total tasks
$totalTasksQuery = "SELECT COUNT(*) as total FROM tasks";
$totalTasksResult = $conn->query($totalTasksQuery);
$totalTasks = $totalTasksResult->fetch_assoc()['total'];

// Count completed tasks
$completedTasksQuery = "SELECT COUNT(*) as completed FROM tasks WHERE status = 'completed'";
$completedTasksResult = $conn->query($completedTasksQuery);
$completedTasks = $completedTasksResult->fetch_assoc()['completed'];

// Count today's tasks
$todayTasksQuery = "SELECT COUNT(*) as today FROM tasks WHERE date = '$today'";
$todayTasksResult = $conn->query($todayTasksQuery);
$todayTasks = $todayTasksResult->fetch_assoc()['today'];

// Get pending tasks
$pendingTasksQuery = "SELECT * FROM tasks WHERE status = 'pending' ORDER BY date ASC, priority ASC LIMIT 5";
$pendingTasksResult = $conn->query($pendingTasksQuery);

// Get recent quotes
$quotesQuery = "SELECT * FROM quotes ORDER BY RAND() LIMIT 1";
$quotesResult = $conn->query($quotesQuery);
$randomQuote = $quotesResult->fetch_assoc();

// Get recent thoughts
$thoughtsQuery = "SELECT * FROM thoughts ORDER BY date DESC LIMIT 3";
$thoughtsResult = $conn->query($thoughtsQuery);

// Get recent learnings
$learningsQuery = "SELECT * FROM learnings ORDER BY date DESC LIMIT 3";
$learningsResult = $conn->query($learningsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Elevate - Personal Growth Tracker</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        <!-- Dashboard Content -->
        <div class="container mx-auto px-4 py-8 flex-grow">
            <h1 class="text-3xl font-bold mb-8 flex items-center">
                <i class="fas fa-chart-line text-accent mr-3"></i>
                Dashboard Overview
            </h1>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Tasks -->
                <div class="card bg-gradient-to-br from-secondary to-gray-800 p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-text-secondary text-sm">Total Tasks</p>
                            <h3 class="text-4xl font-bold mt-2"><?php echo $totalTasks; ?></h3>
                        </div>
                        <div class="p-3 rounded-full bg-blue-500 bg-opacity-20">
                            <i class="fas fa-tasks text-blue-400"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Completed Tasks -->
                <div class="card bg-gradient-to-br from-secondary to-gray-800 p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-text-secondary text-sm">Completed Tasks</p>
                            <h3 class="text-4xl font-bold mt-2"><?php echo $completedTasks; ?></h3>
                        </div>
                        <div class="p-3 rounded-full bg-green-500 bg-opacity-20">
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Today's Tasks -->
                <div class="card bg-gradient-to-br from-secondary to-gray-800 p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-text-secondary text-sm">Today's Tasks</p>
                            <h3 class="text-4xl font-bold mt-2"><?php echo $todayTasks; ?></h3>
                        </div>
                        <div class="p-3 rounded-full bg-accent bg-opacity-20">
                            <i class="fas fa-calendar-day text-accent"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Completion Rate -->
                <div class="card bg-gradient-to-br from-secondary to-gray-800 p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-text-secondary text-sm">Completion Rate</p>
                            <h3 class="text-4xl font-bold mt-2">
                                <?php 
                                    echo $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0; 
                                ?>%
                            </h3>
                        </div>
                        <div class="p-3 rounded-full bg-purple-500 bg-opacity-20">
                            <i class="fas fa-chart-pie text-purple-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Task Completion Chart -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tasks Completed</h3>
                    </div>
                    <div class="card-body h-64">
                        <canvas id="taskCompletionChart"></canvas>
                    </div>
                </div>
                
                <!-- Time Spent Chart -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Time Allocation</h3>
                    </div>
                    <div class="card-body h-64">
                        <canvas id="timeSpentChart"></canvas>
                    </div>
                </div>
                
                <!-- Progress Chart -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Progress Timeline</h3>
                    </div>
                    <div class="card-body h-64">
                        <canvas id="progressChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Two-column layout for task list and other info -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Task List -->
                <div class="card lg:col-span-2">
                    <div class="card-header flex justify-between items-center">
                        <h3 class="card-title">Upcoming Tasks</h3>
                        <a href="tasks.php" class="text-accent hover:text-accent-light transition-colors text-sm">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if ($pendingTasksResult->num_rows > 0): ?>
                            <div class="divide-y divide-gray-700">
                                <?php while ($task = $pendingTasksResult->fetch_assoc()): ?>
                                    <div class="py-4 flex items-start justify-between">
                                        <div class="flex items-start space-x-3">
                                            <div class="mt-1">
                                                <?php
                                                    $priorityClass = "";
                                                    $priorityIcon = "";
                                                    switch ($task['priority']) {
                                                        case 'high':
                                                            $priorityClass = "text-red-500";
                                                            $priorityIcon = "exclamation-circle";
                                                            break;
                                                        case 'medium':
                                                            $priorityClass = "text-yellow-500";
                                                            $priorityIcon = "exclamation";
                                                            break;
                                                        case 'low':
                                                            $priorityClass = "text-green-500";
                                                            $priorityIcon = "check";
                                                            break;
                                                    }
                                                ?>
                                                <i class="fas fa-<?php echo $priorityIcon; ?> <?php echo $priorityClass; ?>"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-medium"><?php echo htmlspecialchars($task['title']); ?></h4>
                                                <p class="text-text-secondary text-sm mt-1">
                                                    <?php echo htmlspecialchars($task['description']); ?>
                                                </p>
                                                <div class="flex items-center mt-2 text-xs text-text-secondary">
                                                    <span class="flex items-center mr-3">
                                                        <i class="far fa-calendar-alt mr-1"></i>
                                                        <?php echo date('M j, Y', strtotime($task['date'])); ?>
                                                    </span>
                                                    <span class="flex items-center">
                                                        <i class="far fa-clock mr-1"></i>
                                                        <?php echo $task['duration']; ?> min
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <button onclick="completeTask(<?php echo $task['id']; ?>)" 
                                                class="px-3 py-1 bg-accent hover:bg-accent-light text-primary text-sm rounded transition-colors">
                                            Complete
                                        </button>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="py-8 text-center text-text-secondary">
                                <i class="fas fa-check-circle text-3xl mb-3"></i>
                                <p>No pending tasks. Great job!</p>
                                <a href="tasks.php" class="text-accent hover:text-accent-light transition-colors mt-2 inline-block">
                                    Add new tasks
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Sidebar with Quote and Recent Activity -->
                <div class="space-y-6">
                    <!-- Quote of the Day -->
                    <div class="card bg-gradient-to-br from-secondary to-gray-800">
                        <div class="card-header">
                            <h3 class="card-title">Quote of the Day</h3>
                        </div>
                        <div class="card-body">
                            <?php if ($randomQuote): ?>
                                <blockquote class="italic text-text-secondary">
                                    "<?php echo htmlspecialchars($randomQuote['quote']); ?>"
                                </blockquote>
                                <p class="text-accent text-right mt-2">— <?php echo htmlspecialchars($randomQuote['author']); ?></p>
                            <?php else: ?>
                                <p class="text-center text-text-secondary">No quotes available</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Recent Activity -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recent Activity</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="divide-y divide-gray-700">
                                <!-- Recent Thoughts -->
                                <?php 
                                if ($thoughtsResult->num_rows > 0): 
                                    while ($thought = $thoughtsResult->fetch_assoc()): 
                                ?>
                                    <div class="p-4">
                                        <div class="flex items-center mb-2">
                                            <i class="fas fa-brain text-purple-400 mr-2"></i>
                                            <span class="text-sm text-text-secondary">
                                                <?php echo date('M j, g:i a', strtotime($thought['date'])); ?>
                                            </span>
                                        </div>
                                        <p class="text-sm">
                                            <?php echo htmlspecialchars(substr($thought['thought'], 0, 100)) . (strlen($thought['thought']) > 100 ? '...' : ''); ?>
                                        </p>
                                        <a href="thoughts.php" class="text-xs text-accent hover:text-accent-light transition-colors mt-1 inline-block">
                                            View all thoughts
                                        </a>
                                    </div>
                                <?php 
                                    endwhile; 
                                endif; 
                                ?>
                                
                                <!-- Recent Learnings -->
                                <?php 
                                if ($learningsResult->num_rows > 0): 
                                    while ($learning = $learningsResult->fetch_assoc()): 
                                ?>
                                    <div class="p-4">
                                        <div class="flex items-center mb-2">
                                            <i class="fas fa-lightbulb text-yellow-400 mr-2"></i>
                                            <span class="text-sm text-text-secondary">
                                                <?php echo date('M j, g:i a', strtotime($learning['date'])); ?>
                                            </span>
                                        </div>
                                        <p class="text-sm">
                                            <?php echo htmlspecialchars(substr($learning['learning'], 0, 100)) . (strlen($learning['learning']) > 100 ? '...' : ''); ?>
                                        </p>
                                        <a href="learnings.php" class="text-xs text-accent hover:text-accent-light transition-colors mt-1 inline-block">
                                            View all learnings
                                        </a>
                                    </div>
                                <?php 
                                    endwhile; 
                                endif; 
                                ?>
                                
                                <?php if ($thoughtsResult->num_rows == 0 && $learningsResult->num_rows == 0): ?>
                                    <div class="p-4 text-center text-text-secondary">
                                        <p>No recent activity</p>
                                    </div>
                                <?php endif; ?>
                            </div>
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
        }, 3000); // 3 seconds
    </script>
</body>
</html>