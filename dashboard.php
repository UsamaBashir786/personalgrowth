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
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="styles.css">
  <!-- Configure Tailwind theme -->
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            primary: '#0F172A', // darker blue background
            secondary: '#1E293B', // slightly lighter dark blue
            accent: '#F59E0B', // gold accent
            'accent-light': '#FBBF24', // lighter gold
            'accent-dark': '#D97706', // darker gold
            'text-primary': '#F8FAFC', // light text
            'text-secondary': '#CBD5E1' // slightly darker light text
          },
          fontFamily: {
            sans: ['Poppins', 'sans-serif']
          },
          boxShadow: {
            'card': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
            'card-hover': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
          },
          animation: {
            'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            'fade-in': 'fadeIn 0.5s ease-in-out',
            'slide-up': 'slideUp 0.5s ease-in-out'
          },
          keyframes: {
            fadeIn: {
              '0%': {
                opacity: '0'
              },
              '100%': {
                opacity: '1'
              }
            },
            slideUp: {
              '0%': {
                transform: 'translateY(20px)',
                opacity: '0'
              },
              '100%': {
                transform: 'translateY(0)',
                opacity: '1'
              }
            }
          }
        }
      }
    }
  </script>
  <style>
    /* Custom Scrollbar */
    ::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }

    ::-webkit-scrollbar-track {
      background: #1E293B;
      border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb {
      background: #F59E0B;
      border-radius: 4px;
      transition: all 0.3s;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: #FBBF24;
    }

    .glass-card {
      background: rgba(30, 41, 59, 0.7);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .stat-card {
      transition: all 0.3s ease;
      overflow: hidden;
      position: relative;
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.05), transparent);
      transition: all 0.6s;
    }

    .stat-card:hover::before {
      left: 100%;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .card {
      border-radius: 12px;
      transition: all 0.3s ease;
    }

    .card:hover {
      transform: translateY(-3px);
    }

    .loader-animation {
      animation: loader 2s linear infinite;
    }

    @keyframes loader {
      0% {
        stroke-dasharray: 1, 150;
        stroke-dashoffset: 0;
      }

      50% {
        stroke-dasharray: 90, 150;
        stroke-dashoffset: -35;
      }

      100% {
        stroke-dasharray: 90, 150;
        stroke-dashoffset: -124;
      }
    }

    .task-item {
      transition: all 0.3s ease;
    }

    .task-item:hover {
      background-color: rgba(30, 41, 59, 0.7);
    }

    .slide-in {
      animation: slideIn 0.5s ease forwards;
    }

    @keyframes slideIn {
      0% {
        opacity: 0;
        transform: translateX(-20px);
      }

      100% {
        opacity: 1;
        transform: translateX(0);
      }
    }

    .nav-link {
      position: relative;
      padding-bottom: 4px;
    }

    .nav-link::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 0;
      height: 2px;
      background-color: #F59E0B;
      transition: width 0.3s ease;
    }

    .nav-link:hover::after,
    .nav-link.active::after {
      width: 100%;
    }

    .btn-action {
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      z-index: 1;
    }

    .btn-action::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.1);
      border-radius: inherit;
      z-index: -1;
      transform: scale(0, 0);
      transform-origin: center;
      transition: transform 0.3s ease;
    }

    .btn-action:hover::after {
      transform: scale(1, 1);
    }
  </style>
</head>

<body class="bg-primary text-text-primary min-h-screen flex flex-col font-sans">
  <!-- Loader -->
  <div id="loader" class="fixed inset-0 flex flex-col items-center justify-center bg-primary z-50 transition-opacity duration-500">
    <div class="w-32 h-32 mb-8">
      <svg class="w-full h-full" viewBox="0 0 100 100">
        <circle class="opacity-20" cx="50" cy="50" r="40" stroke="#F59E0B" stroke-width="3" fill="none" />
        <circle class="loader-animation" cx="50" cy="50" r="40" stroke="#F59E0B" stroke-width="3" fill="none" stroke-linecap="round" />
        <path d="M50 20 L65 45 L50 80 L35 45 Z" fill="#F59E0B" opacity="0.8">
          <animate attributeName="opacity" values="0.8;1;0.8" dur="2s" repeatCount="indefinite" />
        </path>
      </svg>
    </div>
    <h1 class="text-5xl font-bold mb-6 text-accent animate-pulse-slow tracking-widest">ELEVATE</h1>
    <div class="quote-container w-full max-w-md px-8 text-center">
      <p id="quote" class="text-xl italic text-text-secondary"></p>
      <p id="author" class="text-accent mt-3 text-right font-medium"></p>
    </div>
  </div>

  <!-- Main Content -->
  <div id="main-content" class="opacity-0 transition-opacity duration-500 flex flex-col min-h-screen">
    <!-- Navigation -->
    <nav class="bg-secondary shadow-lg z-40 sticky top-0">
      <div class="container mx-auto px-4 py-4 flex items-center justify-between">
        <a href="index.php" class="text-2xl font-bold text-accent flex items-center">
          <svg class="w-8 h-8 mr-2" viewBox="0 0 100 100">
            <path d="M50 20 L65 45 L50 80 L35 45 Z" fill="#F59E0B" />
          </svg>
          ELEVATE
        </a>
        <div class="hidden md:flex items-center space-x-8">
          <a href="dashboard.php" class="nav-link text-text-primary hover:text-accent transition-colors font-medium">Dashboard</a>
          <a href="tasks.php" class="nav-link text-text-primary hover:text-accent transition-colors font-medium">Tasks</a>
          <a href="timetable.php" class="nav-link text-text-primary hover:text-accent transition-colors font-medium">Timetable</a>
          <a href="quotes.php" class="nav-link text-text-primary hover:text-accent transition-colors font-medium">Quotes</a>
          <a href="thoughts.php" class="nav-link text-text-primary hover:text-accent transition-colors font-medium">Thoughts</a>
          <a href="learnings.php" class="nav-link text-text-primary hover:text-accent transition-colors font-medium">Learnings</a>
        </div>
        <button id="mobile-menu-button" class="md:hidden text-text-primary focus:outline-none">
          <i class="fas fa-bars text-xl"></i>
        </button>
      </div>
      <!-- Mobile Menu -->
      <div id="mobile-menu" class="hidden md:hidden bg-secondary py-3 shadow-inner animate-fade-in">
        <div class="container mx-auto px-4 flex flex-col space-y-4">
          <a href="dashboard.php" class="nav-link-mobile py-2 text-text-primary hover:text-accent hover:bg-primary bg-opacity-30 px-4 rounded-lg transition-colors">Dashboard</a>
          <a href="tasks.php" class="nav-link-mobile py-2 text-text-primary hover:text-accent hover:bg-primary bg-opacity-30 px-4 rounded-lg transition-colors">Tasks</a>
          <a href="timetable.php" class="nav-link-mobile py-2 text-text-primary hover:text-accent hover:bg-primary bg-opacity-30 px-4 rounded-lg transition-colors">Timetable</a>
          <a href="quotes.php" class="nav-link-mobile py-2 text-text-primary hover:text-accent hover:bg-primary bg-opacity-30 px-4 rounded-lg transition-colors">Quotes</a>
          <a href="thoughts.php" class="nav-link-mobile py-2 text-text-primary hover:text-accent hover:bg-primary bg-opacity-30 px-4 rounded-lg transition-colors">Thoughts</a>
          <a href="learnings.php" class="nav-link-mobile py-2 text-text-primary hover:text-accent hover:bg-primary bg-opacity-30 px-4 rounded-lg transition-colors">Learnings</a>
        </div>
      </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="container mx-auto px-4 py-8 flex-grow">
      <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
        <h1 class="text-3xl font-bold flex items-center mb-4 md:mb-0">
          <i class="fas fa-chart-line text-accent mr-3"></i>
          <span class="bg-clip-text text-transparent bg-gradient-to-r from-accent to-accent-light">Dashboard Overview</span>
        </h1>
        <div class="flex space-x-4">
          <a href="tasks.php" class="btn-action bg-accent hover:bg-accent-dark text-primary px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
            <i class="fas fa-plus mr-2"></i> New Task
          </a>
          <button id="refresh-btn" class="btn-action bg-secondary hover:bg-gray-700 text-text-primary px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
            <i class="fas fa-sync-alt mr-2"></i> Refresh
          </button>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Total Tasks -->
        <div class="stat-card bg-gradient-to-br from-blue-600 to-blue-900 rounded-xl shadow-lg p-6">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-blue-200 text-sm font-medium mb-1">Total Tasks</p>
              <h3 class="text-4xl font-bold text-white mt-2 flex items-end">
                <?php echo $totalTasks; ?>
                <span class="text-sm text-blue-200 ml-1 mb-1">tasks</span>
              </h3>
            </div>
            <div class="p-3 rounded-full bg-blue-500 bg-opacity-30">
              <i class="fas fa-tasks text-blue-100 text-xl"></i>
            </div>
          </div>
          <div class="mt-4 text-blue-200 text-xs font-medium">
            <span class="inline-flex items-center">
              <i class="fas fa-info-circle mr-1"></i> All tasks in your system
            </span>
          </div>
        </div>

        <!-- Completed Tasks -->
        <div class="stat-card bg-gradient-to-br from-emerald-600 to-emerald-900 rounded-xl shadow-lg p-6">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-emerald-200 text-sm font-medium mb-1">Completed Tasks</p>
              <h3 class="text-4xl font-bold text-white mt-2 flex items-end">
                <?php echo $completedTasks; ?>
                <span class="text-sm text-emerald-200 ml-1 mb-1">tasks</span>
              </h3>
            </div>
            <div class="p-3 rounded-full bg-emerald-500 bg-opacity-30">
              <i class="fas fa-check-circle text-emerald-100 text-xl"></i>
            </div>
          </div>
          <div class="mt-4 text-emerald-200 text-xs font-medium">
            <span class="inline-flex items-center">
              <i class="fas fa-info-circle mr-1"></i> Tasks marked as done
            </span>
          </div>
        </div>

        <!-- Today's Tasks -->
        <div class="stat-card bg-gradient-to-br from-amber-500 to-amber-700 rounded-xl shadow-lg p-6">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-amber-100 text-sm font-medium mb-1">Today's Tasks</p>
              <h3 class="text-4xl font-bold text-white mt-2 flex items-end">
                <?php echo $todayTasks; ?>
                <span class="text-sm text-amber-100 ml-1 mb-1">tasks</span>
              </h3>
            </div>
            <div class="p-3 rounded-full bg-amber-400 bg-opacity-30">
              <i class="fas fa-calendar-day text-amber-100 text-xl"></i>
            </div>
          </div>
          <div class="mt-4 text-amber-100 text-xs font-medium">
            <span class="inline-flex items-center">
              <i class="fas fa-info-circle mr-1"></i> Tasks scheduled for today
            </span>
          </div>
        </div>

        <!-- Completion Rate -->
        <div class="stat-card bg-gradient-to-br from-purple-600 to-purple-900 rounded-xl shadow-lg p-6">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-purple-200 text-sm font-medium mb-1">Completion Rate</p>
              <h3 class="text-4xl font-bold text-white mt-2 flex items-end">
                <?php
                echo $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                ?>%
                <span class="text-sm text-purple-200 ml-1 mb-1">success</span>
              </h3>
            </div>
            <div class="p-3 rounded-full bg-purple-500 bg-opacity-30">
              <i class="fas fa-chart-pie text-purple-100 text-xl"></i>
            </div>
          </div>
          <div class="mt-4 text-purple-200 text-xs font-medium">
            <span class="inline-flex items-center">
              <i class="fas fa-info-circle mr-1"></i> Overall task completion rate
            </span>
          </div>
        </div>
      </div>

      <!-- Charts Row -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
        <!-- Task Completion Chart -->
        <div class="card bg-secondary rounded-xl shadow-lg overflow-hidden">
          <div class="p-4 border-b border-gray-700 flex justify-between items-center">
            <h3 class="font-semibold text-lg text-text-primary">Tasks Completed</h3>
            <div class="text-accent">
              <i class="fas fa-chart-bar"></i>
            </div>
          </div>
          <div class="p-4 h-64">
            <canvas id="taskCompletionChart"></canvas>
          </div>
        </div>

        <!-- Time Spent Chart -->
        <div class="card bg-secondary rounded-xl shadow-lg overflow-hidden">
          <div class="p-4 border-b border-gray-700 flex justify-between items-center">
            <h3 class="font-semibold text-lg text-text-primary">Time Allocation</h3>
            <div class="text-accent">
              <i class="fas fa-clock"></i>
            </div>
          </div>
          <div class="p-4 h-64">
            <canvas id="timeSpentChart"></canvas>
          </div>
        </div>

        <!-- Progress Chart -->
        <div class="card bg-secondary rounded-xl shadow-lg overflow-hidden">
          <div class="p-4 border-b border-gray-700 flex justify-between items-center">
            <h3 class="font-semibold text-lg text-text-primary">Progress Timeline</h3>
            <div class="text-accent">
              <i class="fas fa-chart-line"></i>
            </div>
          </div>
          <div class="p-4 h-64">
            <canvas id="progressChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Two-column layout for task list and other info -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Task List -->
        <div class="card lg:col-span-2 bg-secondary rounded-xl shadow-lg overflow-hidden">
          <div class="p-4 border-b border-gray-700 flex justify-between items-center">
            <h3 class="font-semibold text-lg text-text-primary">Upcoming Tasks</h3>
            <a href="tasks.php" class="text-accent hover:text-accent-light transition-colors text-sm flex items-center">
              View All <i class="fas fa-arrow-right ml-2"></i>
            </a>
          </div>
          <div class="p-4">
            <?php if ($pendingTasksResult->num_rows > 0): ?>
              <div class="space-y-4">
                <?php while ($task = $pendingTasksResult->fetch_assoc()): ?>
                  <div class="task-item p-4 bg-primary bg-opacity-50 rounded-lg flex items-start justify-between">
                    <div class="flex items-start space-x-4">
                      <div class="mt-1">
                        <?php
                        $priorityClass = "";
                        $priorityIcon = "";
                        $priorityBg = "";
                        switch ($task['priority']) {
                          case 'high':
                            $priorityClass = "text-red-100";
                            $priorityIcon = "exclamation-circle";
                            $priorityBg = "bg-red-500";
                            break;
                          case 'medium':
                            $priorityClass = "text-yellow-100";
                            $priorityIcon = "exclamation";
                            $priorityBg = "bg-yellow-500";
                            break;
                          case 'low':
                            $priorityClass = "text-green-100";
                            $priorityIcon = "check";
                            $priorityBg = "bg-green-500";
                            break;
                        }
                        ?>
                        <div class="<?php echo $priorityBg; ?> p-2 rounded-full">
                          <i class="fas fa-<?php echo $priorityIcon; ?> <?php echo $priorityClass; ?>"></i>
                        </div>
                      </div>
                      <div>
                        <h4 class="font-medium text-lg"><?php echo htmlspecialchars($task['title']); ?></h4>
                        <p class="text-text-secondary mt-1">
                          <?php echo htmlspecialchars($task['description']); ?>
                        </p>
                        <div class="flex items-center mt-3 text-xs text-text-secondary">
                          <span class="inline-flex items-center mr-4 bg-secondary bg-opacity-50 px-2 py-1 rounded">
                            <i class="far fa-calendar-alt mr-1"></i>
                            <?php echo date('M j, Y', strtotime($task['date'])); ?>
                          </span>
                          <span class="inline-flex items-center bg-secondary bg-opacity-50 px-2 py-1 rounded">
                            <i class="far fa-clock mr-1"></i>
                            <?php echo $task['duration']; ?> min
                          </span>
                        </div>
                      </div>
                    </div>
                    <button onclick="completeTask(<?php echo $task['id']; ?>)"
                      class="btn-action px-3 py-2 bg-accent hover:bg-accent-dark text-primary text-sm rounded-lg transition-colors font-medium ml-2">
                      <i class="fas fa-check mr-1"></i> Complete
                    </button>
                  </div>
                <?php endwhile; ?>
              </div>
            <?php else: ?>
              <div class="py-12 text-center text-text-secondary bg-primary bg-opacity-30 rounded-lg">
                <i class="fas fa-check-circle text-4xl mb-3 text-accent"></i>
                <p class="text-lg font-medium">No pending tasks. Great job!</p>
                <a href="tasks.php" class="text-accent hover:text-accent-light transition-colors mt-3 inline-block font-medium">
                  <i class="fas fa-plus mr-1"></i> Add new tasks
                </a>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Sidebar with Quote and Recent Activity -->
        <div class="space-y-6">
          <!-- Quote of the Day -->
          <div class="card bg-gradient-to-br from-accent to-accent-dark rounded-xl shadow-lg overflow-hidden">
            <div class="p-4 border-b border-amber-700 flex justify-between items-center">
              <h3 class="font-semibold text-lg text-primary">Quote of the Day</h3>
              <div class="text-primary">
                <i class="fas fa-quote-right"></i>
              </div>
            </div>
            <div class="p-5">
              <?php if ($randomQuote): ?>
                <blockquote class="italic text-primary font-medium">
                  "<?php echo htmlspecialchars($randomQuote['quote']); ?>"
                </blockquote>
                <p class="text-primary text-right mt-4 font-semibold">— <?php echo htmlspecialchars($randomQuote['author']); ?></p>
              <?php else: ?>
                <p class="text-center text-primary font-medium">No quotes available</p>
              <?php endif; ?>
            </div>
          </div>

          <!-- Recent Activity -->
          <div class="card bg-secondary rounded-xl shadow-lg overflow-hidden">
            <div class="p-4 border-b border-gray-700 flex justify-between items-center">
              <h3 class="font-semibold text-lg text-text-primary">Recent Activity</h3>
              <div class="text-accent">
                <i class="fas fa-bell"></i>
              </div>
            </div>
            <div class="p-0">
              <div class="divide-y divide-gray-700">
                <!-- Recent Thoughts -->
                <?php
                if ($thoughtsResult->num_rows > 0):
                  while ($thought = $thoughtsResult->fetch_assoc()):
                ?>
                    <div class="p-4 hover:bg-primary hover:bg-opacity-30 transition-colors">
                      <div class="flex items-center mb-2">
                        <div class="bg-purple-600 bg-opacity-20 p-2 rounded-full mr-3">
                          <i class="fas fa-brain text-purple-400"></i>
                        </div>
                        <span class="text-sm text-text-secondary">
                          <?php echo date('M j, g:i a', strtotime($thought['date'])); ?>
                        </span>
                      </div>
                      <p class="text-sm text-text-primary">
                        <?php echo htmlspecialchars(substr($thought['thought'], 0, 100)) . (strlen($thought['thought']) > 100 ? '...' : ''); ?>
                      </p>
                      <a href="thoughts.php" class="text-xs text-accent hover:text-accent-light transition-colors mt-2 inline-block font-medium">
                        View all thoughts <i class="fas fa-arrow-right ml-1"></i>
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
                    <div class="p-4 hover:bg-primary hover:bg-opacity-30 transition-colors">
                      <div class="flex items-center mb-2">
                        <div class="bg-yellow-600 bg-opacity-20 p-2 rounded-full mr-3">
                          <i class="fas fa-lightbulb text-yellow-400"></i>
                        </div>
                        <span class="text-sm text-text-secondary">
                          <?php echo date('M j, g:i a', strtotime($learning['date'])); ?>
                        </span>
                      </div>
                      <p class="text-sm text-text-primary">
                        <?php echo htmlspecialchars(substr($learning['learning'], 0, 100)) . (strlen($learning['learning']) > 100 ? '...' : ''); ?>
                      </p>
                      <a href="learnings.php" class="text-xs text-accent hover:text-accent-light transition-colors mt-2 inline-block font-medium">
                        View all learnings <i class="fas fa-arrow-right ml-1"></i>
                      </a>
                    </div>
                <?php
                  endwhile;
                endif;
                ?>

                <?php if ($thoughtsResult->num_rows == 0 && $learningsResult->num_rows == 0): ?>
                  <div class="p-6 text-center text-text-secondary">
                    <div class="bg-primary bg-opacity-30 p-6 rounded-lg">
                      <i class="fas fa-history text-3xl mb-3 text-accent"></i>
                      <p>No recent activity</p>
                      <div class="flex justify-center space-x-3 mt-4">
                        <a href="thoughts.php" class="text-xs text-accent hover:text-accent-light transition-colors font-medium">
                          Add thought
                        </a>
                        <a href="learnings.php" class="text-xs text-accent hover:text-accent-light transition-colors font-medium">
                          Add learning
                        </a>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <!-- Quick Add Button -->
          <div class="card bg-secondary rounded-xl shadow-lg overflow-hidden">
            <div class="p-4">
              <div class="grid grid-cols-2 gap-4">
                <a href="tasks.php" class="btn-action flex flex-col items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-medium p-4 rounded-lg transition-colors">
                  <i class="fas fa-tasks text-2xl mb-2"></i>
                  <span>Add Task</span>
                </a>
                <a href="thoughts.php" class="btn-action flex flex-col items-center justify-center bg-purple-600 hover:bg-purple-700 text-white font-medium p-4 rounded-lg transition-colors">
                  <i class="fas fa-brain text-2xl mb-2"></i>
                  <span>Add Thought</span>
                </a>
                <a href="learnings.php" class="btn-action flex flex-col items-center justify-center bg-yellow-600 hover:bg-yellow-700 text-white font-medium p-4 rounded-lg transition-colors">
                  <i class="fas fa-lightbulb text-2xl mb-2"></i>
                  <span>Add Learning</span>
                </a>
                <a href="timetable.php" class="btn-action flex flex-col items-center justify-center bg-green-600 hover:bg-green-700 text-white font-medium p-4 rounded-lg transition-colors">
                  <i class="fas fa-calendar-alt text-2xl mb-2"></i>
                  <span>Timetable</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="bg-secondary py-8 mt-auto">
      <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-between items-center">
          <div class="mb-6 md:mb-0 flex items-center">
            <svg class="w-8 h-8 mr-2" viewBox="0 0 100 100">
              <path d="M50 20 L65 45 L50 80 L35 45 Z" fill="#F59E0B" />
            </svg>
            <div>
              <h2 class="text-xl font-bold text-accent">ELEVATE</h2>
              <p class="text-text-secondary text-sm">Your personal growth companion</p>
            </div>
          </div>
          <div class="flex flex-wrap justify-center gap-y-4 gap-x-6">
            <a href="dashboard.php" class="text-text-secondary hover:text-accent transition-colors">Dashboard</a>
            <a href="tasks.php" class="text-text-secondary hover:text-accent transition-colors">Tasks</a>
            <a href="timetable.php" class="text-text-secondary hover:text-accent transition-colors">Timetable</a>
            <a href="quotes.php" class="text-text-secondary hover:text-accent transition-colors">Quotes</a>
            <a href="thoughts.php" class="text-text-secondary hover:text-accent transition-colors">Thoughts</a>
            <a href="learnings.php" class="text-text-secondary hover:text-accent transition-colors">Learnings</a>
          </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-text-secondary text-sm">
          <p>&copy; 2025 Elevate. All rights reserved.</p>
        </div>
      </div>
    </footer>
  </div>

  <!-- Scripts -->
  <script src="scripts.js"></script>
  <script>
    // Refresh button functionality
    document.getElementById('refresh-btn').addEventListener('click', function() {
      location.reload();
    });

    // Enhanced chart styles
    Chart.defaults.color = '#CBD5E1';
    Chart.defaults.font.family = "'Poppins', sans-serif";

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

    // Mobile menu toggle
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
      const mobileMenu = document.getElementById('mobile-menu');
      mobileMenu.classList.toggle('hidden');
    });

    // Show main content after loader with smoother animations
    setTimeout(() => {
      const loader = document.getElementById('loader');
      const mainContent = document.getElementById('main-content');

      // Fade out loader
      loader.classList.add('opacity-0');

      // Show main content
      setTimeout(() => {
        mainContent.classList.remove('opacity-0');
        mainContent.classList.add('animate-fade-in');

        // Remove loader completely after fade out
        setTimeout(() => {
          loader.classList.add('hidden');
          initCharts(); // Initialize charts after content is visible
        }, 500);
      }, 500);
    }, 0.000005); // 1.5 seconds for loader

    // Initialize all charts
    function initCharts() {
      // Task Completion Chart
      fetch('api/tasks.php?action=stats')
        .then(response => response.json())
        .then(data => {
          const ctx = document.getElementById('taskCompletionChart').getContext('2d');
          new Chart(ctx, {
            type: 'bar',
            data: {
              labels: data.success ? data.labels : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
              datasets: [{
                label: 'Tasks Completed',
                data: data.success ? data.values : [3, 5, 2, 4, 6, 1, 3],
                backgroundColor: [
                  'rgba(245, 158, 11, 0.8)',
                  'rgba(245, 158, 11, 0.7)',
                  'rgba(245, 158, 11, 0.6)',
                  'rgba(245, 158, 11, 0.5)',
                  'rgba(245, 158, 11, 0.6)',
                  'rgba(245, 158, 11, 0.7)',
                  'rgba(245, 158, 11, 0.8)'
                ],
                borderColor: 'rgba(245, 158, 11, 1)',
                borderWidth: 1,
                borderRadius: 4,
                maxBarThickness: 25
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  display: false
                },
                tooltip: {
                  backgroundColor: '#1E293B',
                  padding: 10,
                  titleColor: '#F59E0B',
                  titleFont: {
                    weight: 'bold'
                  },
                  bodyFont: {
                    size: 13
                  },
                  usePointStyle: true,
                  boxPadding: 6
                }
              },
              scales: {
                y: {
                  beginAtZero: true,
                  grid: {
                    color: 'rgba(255, 255, 255, 0.05)',
                    drawBorder: false
                  },
                  ticks: {
                    font: {
                      size: 11
                    },
                    padding: 10
                  }
                },
                x: {
                  grid: {
                    display: false
                  },
                  ticks: {
                    font: {
                      size: 11
                    }
                  }
                }
              }
            }
          });
        })
        .catch(error => console.error('Error loading task stats:', error));

      // Time Spent Chart
      fetch('api/tasks.php?action=timeStats')
        .then(response => response.json())
        .then(data => {
          const ctx = document.getElementById('timeSpentChart').getContext('2d');
          new Chart(ctx, {
            type: 'doughnut',
            data: {
              labels: data.success ? data.labels : ['High Priority', 'Medium Priority', 'Low Priority'],
              datasets: [{
                data: data.success ? data.values : [45, 30, 25],
                backgroundColor: [
                  'rgba(239, 68, 68, 0.8)',
                  'rgba(245, 158, 11, 0.8)',
                  'rgba(34, 197, 94, 0.8)'
                ],
                borderColor: [
                  'rgba(239, 68, 68, 1)',
                  'rgba(245, 158, 11, 1)',
                  'rgba(34, 197, 94, 1)'
                ],
                borderWidth: 1,
                hoverOffset: 4
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              cutout: '65%',
              plugins: {
                legend: {
                  position: 'bottom',
                  labels: {
                    boxWidth: 12,
                    padding: 15,
                    usePointStyle: true,
                    pointStyle: 'circle'
                  }
                },
                tooltip: {
                  backgroundColor: '#1E293B',
                  padding: 10,
                  titleColor: '#F8FAFC',
                  bodyColor: '#F8FAFC',
                  borderColor: 'rgba(255, 255, 255, 0.1)',
                  borderWidth: 1,
                  usePointStyle: true,
                  boxPadding: 6,
                  callbacks: {
                    label: function(context) {
                      const label = context.label || '';
                      const value = context.parsed;
                      const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                      const percentage = Math.round((value / total) * 100);
                      return `${label}: ${value} mins (${percentage}%)`;
                    }
                  }
                }
              }
            }
          });
        })
        .catch(error => console.error('Error loading time stats:', error));

      // Progress Chart
      fetch('api/tasks.php?action=progressStats')
        .then(response => response.json())
        .then(data => {
          const ctx = document.getElementById('progressChart').getContext('2d');

          // Create gradient
          const gradient = ctx.createLinearGradient(0, 0, 0, 300);
          gradient.addColorStop(0, 'rgba(245, 158, 11, 0.5)');
          gradient.addColorStop(1, 'rgba(245, 158, 11, 0.0)');

          new Chart(ctx, {
            type: 'line',
            data: {
              labels: data.success ? data.labels : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
              datasets: [{
                label: 'Tasks Completed',
                data: data.success ? data.values : [2, 4, 3, 5, 4, 6, 8],
                borderColor: 'rgba(245, 158, 11, 1)',
                backgroundColor: gradient,
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#F59E0B',
                pointBorderColor: '#FFF',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  display: false
                },
                tooltip: {
                  backgroundColor: '#1E293B',
                  padding: 10,
                  titleColor: '#F59E0B',
                  titleFont: {
                    weight: 'bold'
                  },
                  bodyFont: {
                    size: 13
                  },
                  usePointStyle: true,
                  boxPadding: 6
                }
              },
              scales: {
                y: {
                  beginAtZero: true,
                  grid: {
                    color: 'rgba(255, 255, 255, 0.05)',
                    drawBorder: false
                  },
                  ticks: {
                    precision: 0,
                    font: {
                      size: 11
                    },
                    padding: 10
                  }
                },
                x: {
                  grid: {
                    display: false
                  },
                  ticks: {
                    font: {
                      size: 11
                    }
                  }
                }
              }
            }
          });
        })
        .catch(error => console.error('Error loading progress stats:', error));
    }

    // Function to complete a task
    function completeTask(taskId) {
      // Add a subtle animation
      const taskElement = event.target.closest('.task-item');
      if (taskElement) {
        taskElement.style.opacity = '0.5';
        taskElement.style.transform = 'translateX(20px)';
        taskElement.style.transition = 'all 0.3s ease';
      }

      fetch(`api/tasks.php?action=complete&id=${taskId}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Show success message
            showNotification('Task completed successfully!', 'success');

            // Reload the page after a delay to show updated data
            setTimeout(() => {
              location.reload();
            }, 1000);
          } else {
            showNotification('Error completing task: ' + data.message, 'error');
            if (taskElement) {
              taskElement.style.opacity = '1';
              taskElement.style.transform = 'translateX(0)';
            }
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showNotification('An error occurred. Please try again.', 'error');
          if (taskElement) {
            taskElement.style.opacity = '1';
            taskElement.style.transform = 'translateX(0)';
          }
        });
    }

    // Show notification function
    function showNotification(message, type = 'info') {
      // Create notification element
      const notification = document.createElement('div');

      // Set classes based on type
      let bgColor, icon;
      switch (type) {
        case 'success':
          bgColor = 'bg-green-600';
          icon = '<i class="fas fa-check-circle mr-2"></i>';
          break;
        case 'error':
          bgColor = 'bg-red-600';
          icon = '<i class="fas fa-exclamation-circle mr-2"></i>';
          break;
        default:
          bgColor = 'bg-blue-600';
          icon = '<i class="fas fa-info-circle mr-2"></i>';
      }

      // Style the notification
      notification.className = `fixed top-20 right-4 z-50 ${bgColor} text-white p-4 rounded-lg shadow-lg max-w-xs`;
      notification.innerHTML = `
                <div class="flex items-center">
                    ${icon}
                    <span>${message}</span>
                </div>
            `;

      // Add to DOM
      document.body.appendChild(notification);

      // Animate in
      setTimeout(() => {
        notification.style.transform = 'translateX(0)';
        notification.style.opacity = '1';
      }, 10);

      // Remove after 3 seconds
      setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(20px)';

        setTimeout(() => {
          notification.remove();
        }, 300);
      }, 1000);
    }
  </script>
</body>

</html>