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

// Get current day of week
$currentDay = date('l');
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
            'slide-up': 'slideUp 0.5s ease-in-out',
            'slide-in-right': 'slideInRight 0.3s ease-out'
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
            },
            slideInRight: {
              '0%': {
                transform: 'translateX(20px)',
                opacity: '0'
              },
              '100%': {
                transform: 'translateX(0)',
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

    .card {
      background-color: #1E293B;
      border-radius: 12px;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
      transition: all 0.3s ease;
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .card:hover {
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      transform: translateY(-3px);
    }

    .card-header {
      padding: 1rem 1.5rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      background-color: rgba(15, 23, 42, 0.5);
    }

    .card-title {
      font-size: 1.25rem;
      font-weight: 600;
      color: #F8FAFC;
    }

    .card-body {
      padding: 1.5rem;
    }

    .form-input {
      width: 100%;
      padding: 0.75rem 1rem;
      background-color: rgba(15, 23, 42, 0.7);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 0.5rem;
      color: #F8FAFC;
      transition: all 0.3s;
    }

    .form-input:focus {
      outline: none;
      border-color: #F59E0B;
      box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.25);
    }

    .form-input::placeholder {
      color: #64748B;
    }

    .form-select {
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23F8FAFC'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 0.75rem center;
      background-size: 1.5em 1.5em;
      padding-right: 2.5rem;
    }

    .form-label {
      display: block;
      margin-bottom: 0.5rem;
      color: #CBD5E1;
      font-weight: 500;
    }

    .nav-link {
      position: relative;
      padding-bottom: 4px;
      font-weight: 500;
      transition: all 0.3s;
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

    .time-slot {
      transition: all 0.3s ease;
      border-left: 3px solid transparent;
    }

    .time-slot:hover {
      background-color: rgba(15, 23, 42, 0.7);
      border-left: 3px solid #F59E0B;
      transform: translateX(2px);
    }

    .delete-btn {
      transition: all 0.2s ease;
    }

    .delete-btn:hover {
      transform: scale(1.1);
    }

    .highlight-day {
      position: relative;
    }

    .highlight-day::before {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 50%;
      transform: translateX(-50%);
      width: 40%;
      height: 3px;
      background-color: #F59E0B;
      border-radius: 2px;
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

    .schedule-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 1px;
      background-color: rgba(255, 255, 255, 0.05);
      border-radius: 8px;
      overflow: hidden;
    }

    .day-column {
      min-height: 300px;
      background-color: rgba(15, 23, 42, 0.5);
    }

    .day-header {
      padding: 10px;
      text-align: center;
      font-weight: 600;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      background-color: rgba(15, 23, 42, 0.7);
    }

    .day-content {
      padding: 10px;
      height: 100%;
    }

    .schedule-item {
      margin-bottom: 8px;
      padding: 10px;
      border-radius: 6px;
      background-color: rgba(30, 41, 59, 0.9);
      border-left: 3px solid #F59E0B;
      transition: all 0.3s ease;
    }

    .schedule-item:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .current-day {
      background-color: rgba(245, 158, 11, 0.1);
    }

    .current-day .day-header {
      background-color: rgba(245, 158, 11, 0.3);
    }

    .btn-primary {
      background: linear-gradient(135deg, #F59E0B, #D97706);
      color: #0F172A;
      font-weight: 600;
      padding: 0.75rem 1.5rem;
      border-radius: 0.5rem;
      transition: all 0.3s;
      box-shadow: 0 4px 6px rgba(245, 158, 11, 0.25);
    }

    .btn-primary:hover {
      background: linear-gradient(135deg, #FBBF24, #F59E0B);
      transform: translateY(-2px);
      box-shadow: 0 6px 8px rgba(245, 158, 11, 0.3);
    }

    .tooltip {
      position: relative;
    }

    .tooltip-text {
      visibility: hidden;
      width: 120px;
      background-color: #0F172A;
      color: #F8FAFC;
      text-align: center;
      border-radius: 6px;
      padding: 5px;
      position: absolute;
      z-index: 1;
      bottom: 125%;
      left: 50%;
      transform: translateX(-50%);
      opacity: 0;
      transition: opacity 0.3s;
      font-size: 0.75rem;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .tooltip:hover .tooltip-text {
      visibility: visible;
      opacity: 1;
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
    <nav class="bg-secondary shadow-lg sticky top-0 z-40">
      <div class="container mx-auto px-4 py-4 flex items-center justify-between">
        <a href="index.html" class="text-2xl font-bold text-accent flex items-center">
          <svg class="w-8 h-8 mr-2" viewBox="0 0 100 100">
            <path d="M50 20 L65 45 L50 80 L35 45 Z" fill="#F59E0B" />
          </svg>
          ELEVATE
        </a>
        <div class="hidden md:flex items-center space-x-8">
          <a href="dashboard.php" class="nav-link text-text-primary hover:text-accent transition-colors">Dashboard</a>
          <a href="tasks.php" class="nav-link text-text-primary hover:text-accent transition-colors">Tasks</a>
          <a href="timetable.php" class="nav-link text-text-primary hover:text-accent transition-colors active">Timetable</a>
          <a href="quotes.php" class="nav-link text-text-primary hover:text-accent transition-colors">Quotes</a>
          <a href="thoughts.php" class="nav-link text-text-primary hover:text-accent transition-colors">Thoughts</a>
          <a href="learnings.php" class="nav-link text-text-primary hover:text-accent transition-colors">Learnings</a>
        </div>
        <button id="mobile-menu-button" class="md:hidden text-text-primary focus:outline-none">
          <i class="fas fa-bars text-xl"></i>
        </button>
      </div>
      <!-- Mobile Menu -->
      <div id="mobile-menu" class="hidden md:hidden bg-secondary py-3 shadow-inner animate-fade-in">
        <div class="container mx-auto px-4 flex flex-col space-y-4">
          <a href="dashboard.php" class="py-2 text-text-primary hover:text-accent hover:bg-primary bg-opacity-30 px-4 rounded-lg transition-colors">Dashboard</a>
          <a href="tasks.php" class="py-2 text-text-primary hover:text-accent hover:bg-primary bg-opacity-30 px-4 rounded-lg transition-colors">Tasks</a>
          <a href="timetable.php" class="py-2 text-accent bg-primary bg-opacity-30 px-4 rounded-lg transition-colors">Timetable</a>
          <a href="quotes.php" class="py-2 text-text-primary hover:text-accent hover:bg-primary bg-opacity-30 px-4 rounded-lg transition-colors">Quotes</a>
          <a href="thoughts.php" class="py-2 text-text-primary hover:text-accent hover:bg-primary bg-opacity-30 px-4 rounded-lg transition-colors">Thoughts</a>
          <a href="learnings.php" class="py-2 text-text-primary hover:text-accent hover:bg-primary bg-opacity-30 px-4 rounded-lg transition-colors">Learnings</a>
        </div>
      </div>
    </nav>

    <!-- Page Header with Breadcrumbs -->
    <div class="bg-secondary bg-opacity-50 border-b border-gray-700">
      <div class="container mx-auto px-4 py-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
          <div>
            <h1 class="text-3xl font-bold flex items-center mb-2">
              <i class="fas fa-calendar-alt text-accent mr-3"></i>
              <span class="bg-clip-text text-transparent bg-gradient-to-r from-accent to-accent-light">Weekly Timetable</span>
            </h1>
            <div class="flex items-center text-sm text-text-secondary">
              <a href="dashboard.php" class="hover:text-accent transition-colors">Dashboard</a>
              <i class="fas fa-chevron-right mx-2 text-xs"></i>
              <span class="text-accent">Timetable</span>
            </div>
          </div>
          <div class="mt-4 md:mt-0">
            <div class="inline-flex rounded-md shadow-sm">
              <button id="day-view-btn" class="px-4 py-2 text-sm font-medium rounded-l-lg border border-gray-700 bg-gray-800 text-text-primary hover:bg-accent hover:text-primary transition-colors">
                <i class="fas fa-list mr-2"></i> Day View
              </button>
              <button id="week-view-btn" class="px-4 py-2 text-sm font-medium rounded-r-lg border border-gray-700 bg-accent text-primary hover:bg-accent-light transition-colors">
                <i class="fas fa-calendar-week mr-2"></i> Week View
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Timetable Content -->
    <div class="container mx-auto px-4 py-8 flex-grow">
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Add New Schedule Form -->
        <div class="card animate-fade-in">
          <div class="card-header">
            <h3 class="card-title flex items-center">
              <i class="fas fa-plus-circle text-accent mr-2"></i> Add New Schedule
            </h3>
          </div>
          <div class="card-body">
            <form id="timetable-form" method="post">
              <div class="mb-4">
                <label for="day" class="form-label flex items-center">
                  <i class="fas fa-calendar-day text-accent mr-2"></i> Day
                </label>
                <select id="day" name="day" class="form-input form-select" required>
                  <?php foreach ($days as $day): ?>
                    <option value="<?php echo $day; ?>" <?php echo ($day == $currentDay) ? 'selected' : ''; ?>>
                      <?php echo $day; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="mb-4">
                <label for="time" class="form-label flex items-center">
                  <i class="fas fa-clock text-accent mr-2"></i> Time
                </label>
                <input type="time" id="time" name="time" class="form-input" required>
              </div>
              <div class="mb-6">
                <label for="task" class="form-label flex items-center">
                  <i class="fas fa-tasks text-accent mr-2"></i> Activity
                </label>
                <input type="text" id="task" name="task" class="form-input" placeholder="Enter your activity" required>
              </div>
              <button type="submit" id="timetable-submit-btn" class="w-full py-3 bg-accent hover:bg-accent-light text-primary font-medium rounded-lg transition-colors flex items-center justify-center">
                <i class="fas fa-plus-circle mr-2"></i> Add Schedule
              </button>
            </form>
          </div>
        </div>

        <!-- Weekly Timetable -->
        <div class="card lg:col-span-3 animate-slide-up" id="week-view">
          <div class="card-header flex justify-between items-center">
            <h3 class="card-title flex items-center">
              <i class="fas fa-calendar-week text-accent mr-2"></i> Your Weekly Schedule
            </h3>
            <div class="text-sm text-text-secondary">
              <span class="bg-accent px-2 py-1 rounded text-primary">Today: <?php echo date('l, F j'); ?></span>
            </div>
          </div>
          <div class="card-body p-0 overflow-x-auto">
            <div class="schedule-grid">
              <?php foreach ($days as $day): ?>
                <div class="day-column <?php echo ($day == $currentDay) ? 'current-day' : ''; ?>">
                  <div class="day-header">
                    <?php echo $day; ?>
                    <?php if ($day == $currentDay): ?>
                      <span class="ml-1 inline-block">
                        <i class="fas fa-star text-accent text-xs"></i>
                      </span>
                    <?php endif; ?>
                  </div>
                  <div class="day-content">
                    <?php if (count($timetableByDay[$day]) > 0): ?>
                      <?php foreach ($timetableByDay[$day] as $entry): ?>
                        <div class="schedule-item group">
                          <div class="flex justify-between items-start">
                            <span class="font-medium text-accent">
                              <?php echo date('g:i A', strtotime($entry['time'])); ?>
                            </span>
                            <button onclick="deleteItem('timetable', <?php echo $entry['id']; ?>)"
                              class="opacity-0 group-hover:opacity-100 text-red-500 hover:text-red-400 transition-opacity delete-btn tooltip">
                              <i class="fas fa-trash-alt"></i>
                              <span class="tooltip-text">Delete</span>
                            </button>
                          </div>
                          <p class="mt-1 text-text-primary">
                            <?php echo htmlspecialchars($entry['task']); ?>
                          </p>
                        </div>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <div class="h-full flex items-center justify-center text-center">
                        <p class="text-text-secondary text-sm italic">No activities scheduled</p>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Daily Schedule List View -->
      <div class="mt-8" id="day-view-section">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold flex items-center">
            <i class="fas fa-list-alt text-accent mr-2"></i>
            <span class="bg-clip-text text-transparent bg-gradient-to-r from-accent to-accent-light">Schedule by Day</span>
          </h2>
          <div class="flex items-center text-sm bg-secondary rounded-lg p-1">
            <button id="collapse-all" class="px-3 py-1 rounded hover:bg-primary transition-colors">
              <i class="fas fa-compress-alt mr-1"></i> Collapse All
            </button>
            <button id="expand-all" class="px-3 py-1 rounded bg-accent text-primary">
              <i class="fas fa-expand-alt mr-1"></i> Expand All
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php foreach ($days as $index => $day): ?>
            <div class="card day-card animate-fade-in" style="animation-delay: <?php echo $index * 0.05; ?>s">
              <div class="card-header flex items-center justify-between cursor-pointer day-header-toggle">
                <h3 class="card-title flex items-center">
                  <?php if ($day == $currentDay): ?>
                    <span class="mr-2 text-accent"><i class="fas fa-star"></i></span>
                  <?php else: ?>
                    <span class="mr-2 text-text-secondary"><i class="far fa-calendar-alt"></i></span>
                  <?php endif; ?>
                  <?php echo $day; ?>
                </h3>
                <div class="flex items-center">
                  <span class="mr-3 text-sm <?php echo count($timetableByDay[$day]) > 0 ? 'text-accent' : 'text-text-secondary'; ?>">
                    <?php echo count($timetableByDay[$day]); ?> activities
                  </span>
                  <i class="fas fa-chevron-down text-accent transition-transform duration-300 toggle-icon"></i>
                </div>
              </div>
              <div class="card-body p-0 day-content-toggle">
                <?php if (count($timetableByDay[$day]) > 0): ?>
                  <div class="divide-y divide-gray-700">
                    <?php foreach ($timetableByDay[$day] as $entry): ?>
                      <div class="p-4 time-slot group hover:bg-opacity-70">
                        <div class="flex justify-between items-start">
                          <div>
                            <span class="text-accent font-medium"><?php echo date('g:i A', strtotime($entry['time'])); ?></span>
                            <p class="text-text-primary mt-1"><?php echo htmlspecialchars($entry['task']); ?></p>
                          </div>
                          <div class="flex space-x-2">
                            <button onclick="editItem('timetable', <?php echo $entry['id']; ?>, '<?php echo addslashes($entry['task']); ?>', '<?php echo $entry['time']; ?>', '<?php echo $day; ?>')"
                              class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition-colors tooltip">
                              <i class="fas fa-edit"></i>
                              <span class="tooltip-text">Edit</span>
                            </button>
                            <button onclick="deleteItem('timetable', <?php echo $entry['id']; ?>)"
                              class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded transition-colors tooltip">
                              <i class="fas fa-trash-alt"></i>
                              <span class="tooltip-text">Delete</span>
                            </button>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php else: ?>
                  <div class="py-8 text-center text-text-secondary">
                    <i class="far fa-calendar-times text-3xl mb-2 text-accent opacity-50"></i>
                    <p>No activities scheduled for <?php echo $day; ?></p>
                    <button onclick="document.getElementById('day').value='<?php echo $day; ?>'; document.getElementById('time').focus();"
                      class="mt-3 text-accent hover:text-accent-light transition-colors text-sm">
                      <i class="fas fa-plus-circle mr-1"></i> Add activity
                    </button>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Quick Tips Section -->
      <div class="mt-10 bg-secondary rounded-xl p-6 shadow-lg animate-fade-in">
        <div class="flex flex-col lg:flex-row items-start lg:items-center">
          <div class="bg-accent rounded-full p-4 flex-shrink-0 mb-4 lg:mb-0 lg:mr-6">
            <i class="fas fa-lightbulb text-primary text-2xl"></i>
          </div>
          <div>
            <h3 class="text-xl font-semibold mb-2 text-accent">Tips for Effective Scheduling</h3>
            <div class="text-text-secondary space-y-2">
              <p class="flex items-start">
                <i class="fas fa-check-circle text-accent mt-1 mr-2"></i>
                Group similar activities together to improve focus and reduce context switching.
              </p>
              <p class="flex items-start">
                <i class="fas fa-check-circle text-accent mt-1 mr-2"></i>
                Schedule your most challenging tasks during your peak energy hours.
              </p>
              <p class="flex items-start">
                <i class="fas fa-check-circle text-accent mt-1 mr-2"></i>
                Include buffer time between activities to prevent burnout and handle unexpected situations.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Modal -->
    <div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
      <div class="bg-secondary rounded-lg shadow-xl max-w-md w-full p-6 animate-slide-up">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-semibold text-accent">Edit Schedule</h3>
          <button id="close-modal" class="text-text-secondary hover:text-accent">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <form id="edit-form">
          <input type="hidden" id="edit-id" name="id">
          <div class="mb-4">
            <label for="edit-day" class="form-label flex items-center">
              <i class="fas fa-calendar-day text-accent mr-2"></i> Day
            </label>
            <select id="edit-day" name="day" class="form-input form-select" required>
              <?php foreach ($days as $day): ?>
                <option value="<?php echo $day; ?>"><?php echo $day; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-4">
            <label for="edit-time" class="form-label flex items-center">
              <i class="fas fa-clock text-accent mr-2"></i> Time
            </label>
            <input type="time" id="edit-time" name="time" class="form-input" required>
          </div>
          <div class="mb-6">
            <label for="edit-task" class="form-label flex items-center">
              <i class="fas fa-tasks text-accent mr-2"></i> Activity
            </label>
            <input type="text" id="edit-task" name="task" class="form-input" placeholder="Enter your activity" required>
          </div>
          <div class="flex space-x-3">
            <button type="button" id="cancel-edit" class="flex-1 py-3 bg-gray-600 hover:bg-gray-700 text-text-primary font-medium rounded-lg transition-colors">
              Cancel
            </button>
            <button type="submit" class="flex-1 py-3 bg-accent hover:bg-accent-light text-primary font-medium rounded-lg transition-colors">
              Save Changes
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
      <div class="bg-secondary rounded-lg shadow-xl max-w-md w-full p-6 animate-slide-up">
        <div class="text-center mb-6">
          <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
            <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-text-primary mb-2">Delete Schedule Item</h3>
          <p class="text-text-secondary">Are you sure you want to delete this schedule item? This action cannot be undone.</p>
        </div>
        <div class="flex space-x-3">
          <button id="cancel-delete" class="flex-1 py-3 bg-gray-600 hover:bg-gray-700 text-text-primary font-medium rounded-lg transition-colors">
            Cancel
          </button>
          <button id="confirm-delete" class="flex-1 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
            Delete
          </button>
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
            <a href="timetable.php" class="text-accent transition-colors">Timetable</a>
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
    // Store the delete item ID
    let deleteItemId = null;
    let deleteItemType = null;

    // Refresh timetable function
    function refreshTimetable() {
      // Reload the page to refresh timetable
      window.location.reload();
    }

    // Edit item function
    function editItem(type, id, task, time, day) {
      // Populate the edit form
      document.getElementById('edit-id').value = id;
      document.getElementById('edit-task').value = task;
      document.getElementById('edit-time').value = time;
      document.getElementById('edit-day').value = day;

      // Show the modal
      document.getElementById('edit-modal').classList.remove('hidden');
    }

    // Delete item function (modified to show confirmation)
    function deleteItem(type, id) {
      // Store the item details
      deleteItemId = id;
      deleteItemType = type;

      // Show the confirmation modal
      document.getElementById('delete-confirm-modal').classList.remove('hidden');
    }

    // Handle actual deletion after confirmation
    function performDelete() {
      if (!deleteItemId || !deleteItemType) return;

      // Use the actual API endpoint for timetable
      const apiEndpoint = deleteItemType === 'timetable' ? 'timetable' : `${deleteItemType}s`;

      fetch(`api/${apiEndpoint}.php?action=delete&id=${deleteItemId}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showAlert('success', 'Item deleted successfully!');
            // Refresh after a short delay
            setTimeout(refreshTimetable, 1000);
          } else {
            showAlert('error', data.message || 'Error deleting item');
          }
        })
        .catch(error => {
          showAlert('error', 'An error occurred. Please try again.');
          console.error('Error:', error);
        });

      // Hide the confirmation modal
      document.getElementById('delete-confirm-modal').classList.add('hidden');
    }

    // Custom alert function
    function showAlert(type, message) {
      // Create notification element
      const notification = document.createElement('div');
      notification.className = `fixed top-20 right-4 max-w-xs p-4 rounded-lg shadow-lg z-50 flex items-center animate-slide-in-right`;

      // Set background color based on type
      let bgColor, icon;
      switch (type) {
        case 'success':
          bgColor = 'bg-green-600';
          icon = '<i class="fas fa-check-circle mr-3 text-xl"></i>';
          break;
        case 'error':
          bgColor = 'bg-red-600';
          icon = '<i class="fas fa-exclamation-circle mr-3 text-xl"></i>';
          break;
        default:
          bgColor = 'bg-blue-600';
          icon = '<i class="fas fa-info-circle mr-3 text-xl"></i>';
      }

      notification.classList.add(bgColor, 'text-white');
      notification.innerHTML = `${icon}<span>${message}</span>`;

      document.body.appendChild(notification);

      // Remove after 3 seconds
      setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(20px)';

        setTimeout(() => {
          notification.remove();
        }, 300);
      }, 3000);
    }

    // Initialize day/week view toggle
    function initViewToggle() {
      const dayViewBtn = document.getElementById('day-view-btn');
      const weekViewBtn = document.getElementById('week-view-btn');
      const dayViewSection = document.getElementById('day-view-section');
      const weekView = document.getElementById('week-view');

      dayViewBtn.addEventListener('click', function() {
        dayViewSection.style.display = 'block';
        weekView.style.display = 'none';
        dayViewBtn.classList.add('bg-accent', 'text-primary');
        dayViewBtn.classList.remove('bg-gray-800', 'text-text-primary');
        weekViewBtn.classList.add('bg-gray-800', 'text-text-primary');
        weekViewBtn.classList.remove('bg-accent', 'text-primary');
      });

      weekViewBtn.addEventListener('click', function() {
        dayViewSection.style.display = 'none';
        weekView.style.display = 'block';
        weekViewBtn.classList.add('bg-accent', 'text-primary');
        weekViewBtn.classList.remove('bg-gray-800', 'text-text-primary');
        dayViewBtn.classList.add('bg-gray-800', 'text-text-primary');
        dayViewBtn.classList.remove('bg-accent', 'text-primary');
      });
    }

    // Initialize expandable day cards
    function initDayCards() {
      const dayHeaderToggles = document.querySelectorAll('.day-header-toggle');
      const collapseAllBtn = document.getElementById('collapse-all');
      const expandAllBtn = document.getElementById('expand-all');

      dayHeaderToggles.forEach(header => {
        header.addEventListener('click', function() {
          const content = this.nextElementSibling;
          const icon = this.querySelector('.toggle-icon');

          if (content.style.display === 'none') {
            content.style.display = 'block';
            icon.style.transform = 'rotate(0)';
          } else {
            content.style.display = 'none';
            icon.style.transform = 'rotate(-90deg)';
          }
        });
      });

      collapseAllBtn.addEventListener('click', function() {
        document.querySelectorAll('.day-content-toggle').forEach(content => {
          content.style.display = 'none';
        });
        document.querySelectorAll('.toggle-icon').forEach(icon => {
          icon.style.transform = 'rotate(-90deg)';
        });

        collapseAllBtn.classList.add('bg-accent', 'text-primary');
        expandAllBtn.classList.remove('bg-accent', 'text-primary');
      });

      expandAllBtn.addEventListener('click', function() {
        document.querySelectorAll('.day-content-toggle').forEach(content => {
          content.style.display = 'block';
        });
        document.querySelectorAll('.toggle-icon').forEach(icon => {
          icon.style.transform = 'rotate(0)';
        });

        expandAllBtn.classList.add('bg-accent', 'text-primary');
        collapseAllBtn.classList.remove('bg-accent', 'text-primary');
      });
    }

    // Initialize modals
    function initModals() {
      // Edit modal
      const editModal = document.getElementById('edit-modal');
      const closeModalBtn = document.getElementById('close-modal');
      const cancelEditBtn = document.getElementById('cancel-edit');
      const editForm = document.getElementById('edit-form');

      closeModalBtn.addEventListener('click', () => {
        editModal.classList.add('hidden');
      });

      cancelEditBtn.addEventListener('click', () => {
        editModal.classList.add('hidden');
      });

      editForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const id = document.getElementById('edit-id').value;
        const task = document.getElementById('edit-task').value;
        const time = document.getElementById('edit-time').value;
        const day = document.getElementById('edit-day').value;

        const formData = new FormData();
        formData.append('id', id);
        formData.append('task', task);
        formData.append('time', time);
        formData.append('day', day);

        fetch('api/timetable.php?action=update', {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              showAlert('success', 'Schedule updated successfully!');
              editModal.classList.add('hidden');
              setTimeout(refreshTimetable, 1000);
            } else {
              showAlert('error', data.message || 'Error updating schedule');
            }
          })
          .catch(error => {
            showAlert('error', 'An error occurred. Please try again.');
            console.error('Error:', error);
          });
      });

      // Delete confirmation modal
      const deleteModal = document.getElementById('delete-confirm-modal');
      const cancelDeleteBtn = document.getElementById('cancel-delete');
      const confirmDeleteBtn = document.getElementById('confirm-delete');

      cancelDeleteBtn.addEventListener('click', () => {
        deleteModal.classList.add('hidden');
      });

      confirmDeleteBtn.addEventListener('click', performDelete);
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

    // Mobile menu toggle
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
      const mobileMenu = document.getElementById('mobile-menu');
      mobileMenu.classList.toggle('hidden');
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

          // Initialize components after content is visible
          initViewToggle();
          initDayCards();
          initModals();
        }, 500);
      }, 500);
    }, 1);
  </script>
</body>

</html>