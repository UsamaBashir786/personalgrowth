<?php
// Include database configuration
require_once 'config.php';

// Get all tasks
$tasksQuery = "SELECT * FROM tasks ORDER BY date ASC, priority ASC";
$tasksResult = $conn->query($tasksQuery);

// Get today's tasks
$today = date('Y-m-d');
$todayTasksQuery = "SELECT * FROM tasks WHERE date = '$today' ORDER BY priority ASC";
$todayTasksResult = $conn->query($todayTasksQuery);

// Get completed tasks
$completedTasksQuery = "SELECT * FROM tasks WHERE status = 'completed' ORDER BY date DESC LIMIT 10";
$completedTasksResult = $conn->query($completedTasksQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Task Management | Elevate - Personal Growth Tracker</title>
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

    <!-- Task Management Content -->
    <div class="container mx-auto px-4 py-8 flex-grow">
      <h1 class="text-3xl font-bold mb-8 flex items-center">
        <i class="fas fa-tasks text-accent mr-3"></i>
        Task Management
      </h1>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add New Task Form -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Add New Task</h3>
          </div>
          <div class="card-body">
            <form id="task-form" method="post">
              <div class="mb-4">
                <label for="title" class="form-label">Task Title</label>
                <input type="text" id="title" name="title" class="form-input" placeholder="Enter task title" required>
              </div>
              <div class="mb-4">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-input" rows="3" placeholder="Enter task description"></textarea>
              </div>
              <div class="mb-4">
                <label for="priority" class="form-label">Priority</label>
                <select id="priority" name="priority" class="form-input form-select" required>
                  <option value="high">High</option>
                  <option value="medium" selected>Medium</option>
                  <option value="low">Low</option>
                </select>
              </div>
              <div class="mb-4">
                <label for="duration" class="form-label">Duration (minutes)</label>
                <input type="number" id="duration" name="duration" class="form-input" min="1" value="30" required>
              </div>
              <div class="mb-6">
                <label for="date" class="form-label">Date</label>
                <input type="date" id="date" name="date" class="form-input" value="<?php echo date('Y-m-d'); ?>" required>
              </div>
              <button type="submit" id="task-submit-btn" class="w-full py-3 bg-accent hover:bg-accent-light text-primary font-medium rounded-lg transition-colors">
                Add Task
              </button>
            </form>
          </div>
        </div>

        <!-- Today's Tasks -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Today's Tasks</h3>
          </div>
          <div class="card-body p-0">
            <div id="today-tasks-container">
              <?php if ($todayTasksResult->num_rows > 0): ?>
                <div class="divide-y divide-gray-700">
                  <?php while ($task = $todayTasksResult->fetch_assoc()): ?>
                    <div class="p-4 flex items-start justify-between">
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
                            <span class="flex items-center">
                              <i class="far fa-clock mr-1"></i>
                              <?php echo $task['duration']; ?> min
                            </span>
                          </div>
                        </div>
                      </div>
                      <div class="flex space-x-2">
                        <button onclick="completeTask(<?php echo $task['id']; ?>)"
                          class="px-3 py-1 bg-accent hover:bg-accent-light text-primary text-sm rounded transition-colors">
                          Complete
                        </button>
                        <button onclick="deleteItem('task', <?php echo $task['id']; ?>)"
                          class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded transition-colors">
                          <i class="fas fa-trash-alt"></i>
                        </button>
                      </div>
                    </div>
                  <?php endwhile; ?>
                </div>
              <?php else: ?>
                <div class="py-8 text-center text-text-secondary">
                  <i class="fas fa-check-circle text-3xl mb-3"></i>
                  <p>No tasks scheduled for today.</p>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Recently Completed Tasks -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Recently Completed</h3>
          </div>
          <div class="card-body p-0">
            <div id="completed-tasks-container">
              <?php if ($completedTasksResult->num_rows > 0): ?>
                <div class="divide-y divide-gray-700">
                  <?php while ($task = $completedTasksResult->fetch_assoc()): ?>
                    <div class="p-4">
                      <div class="flex items-center mb-1">
                        <h4 class="font-medium line-through text-text-secondary"><?php echo htmlspecialchars($task['title']); ?></h4>
                        <span class="ml-2 px-2 py-0.5 bg-green-100 text-green-800 text-xs rounded-full">
                          Completed
                        </span>
                      </div>
                      <div class="flex items-center text-xs text-text-secondary">
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
                  <?php endwhile; ?>
                </div>
              <?php else: ?>
                <div class="py-8 text-center text-text-secondary">
                  <p>No completed tasks yet.</p>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- All Tasks (Future Tasks) -->
      <div class="card mt-8">
        <div class="card-header">
          <h3 class="card-title">All Upcoming Tasks</h3>
        </div>
        <div class="card-body p-0">
          <div id="all-tasks-container">
            <?php
            if ($tasksResult->num_rows > 0):
              // Group tasks by date
              $tasksByDate = [];
              while ($task = $tasksResult->fetch_assoc()) {
                if ($task['status'] == 'pending' && $task['date'] >= date('Y-m-d')) {
                  $dateKey = $task['date'];
                  if (!isset($tasksByDate[$dateKey])) {
                    $tasksByDate[$dateKey] = [];
                  }
                  $tasksByDate[$dateKey][] = $task;
                }
              }

              if (count($tasksByDate) > 0):
                foreach ($tasksByDate as $date => $tasks):
                  $formattedDate = date('l, F j, Y', strtotime($date));
                  $isToday = (date('Y-m-d') == $date) ? true : false;
            ?>
                  <div class="border-b border-gray-700">
                    <div class="px-6 py-3 bg-gray-800">
                      <h4 class="font-medium">
                        <?php echo $formattedDate; ?>
                        <?php if ($isToday): ?>
                          <span class="ml-2 px-2 py-0.5 bg-accent text-xs text-primary rounded-full">Today</span>
                        <?php endif; ?>
                      </h4>
                    </div>
                    <div class="divide-y divide-gray-700">
                      <?php foreach ($tasks as $task): ?>
                        <div class="p-4 flex items-start justify-between">
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
                                <span class="flex items-center">
                                  <i class="far fa-clock mr-1"></i>
                                  <?php echo $task['duration']; ?> min
                                </span>
                              </div>
                            </div>
                          </div>
                          <div class="flex space-x-2">
                            <button onclick="completeTask(<?php echo $task['id']; ?>)"
                              class="px-3 py-1 bg-accent hover:bg-accent-light text-primary text-sm rounded transition-colors">
                              Complete
                            </button>
                            <button onclick="deleteItem('task', <?php echo $task['id']; ?>)"
                              class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded transition-colors">
                              <i class="fas fa-trash-alt"></i>
                            </button>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                <?php
                endforeach;
              else:
                ?>
                <div class="py-8 text-center text-text-secondary">
                  <p>No upcoming tasks scheduled.</p>
                </div>
              <?php
              endif;
            else:
              ?>
              <div class="py-8 text-center text-text-secondary">
                <p>No tasks found. Add your first task!</p>
              </div>
            <?php endif; ?>
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
    // Refresh tasks function
    function refreshTasks() {
      // Reload the page to refresh tasks
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