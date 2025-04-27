<?php
/**
 * Tasks API
 * 
 * Handles CRUD operations for tasks
 */

// Include database configuration
require_once '../config.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get the action
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Handle different actions
switch ($action) {
    case 'add':
        addTask();
        break;
    
    case 'get':
        getTask();
        break;
    
    case 'getAll':
        getAllTasks();
        break;
    
    case 'getTodayTasks':
        getTodayTasks();
        break;
    
    case 'update':
        updateTask();
        break;
    
    case 'complete':
        completeTask();
        break;
    
    case 'delete':
        deleteTask();
        break;
    
    case 'stats':
        getTaskStats();
        break;
    
    case 'timeStats':
        getTimeStats();
        break;
    
    case 'progressStats':
        getProgressStats();
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

/**
 * Add a new task
 */
function addTask() {
    global $conn;
    
    // Check if required fields are set
    if (!isset($_POST['title']) || !isset($_POST['priority']) || !isset($_POST['duration']) || !isset($_POST['date'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        return;
    }
    
    // Sanitize input
    $title = sanitize($_POST['title']);
    $description = isset($_POST['description']) ? sanitize($_POST['description']) : '';
    $priority = sanitize($_POST['priority']);
    $duration = (int) $_POST['duration'];
    $date = sanitize($_POST['date']);
    
    // Validate data
    if (empty($title)) {
        echo json_encode(['success' => false, 'message' => 'Title cannot be empty']);
        return;
    }
    
    if (!in_array($priority, ['high', 'medium', 'low'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid priority']);
        return;
    }
    
    if ($duration <= 0) {
        echo json_encode(['success' => false, 'message' => 'Duration must be greater than 0']);
        return;
    }
    
    // Insert the task
    $query = "INSERT INTO tasks (title, description, priority, duration, status, date) 
              VALUES (?, ?, ?, ?, 'pending', ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssis', $title, $description, $priority, $duration, $date);
    
    if ($stmt->execute()) {
        $taskId = $stmt->insert_id;
        echo json_encode(['success' => true, 'message' => 'Task added successfully', 'taskId' => $taskId]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding task: ' . $stmt->error]);
    }
    
    $stmt->close();
}

/**
 * Get a specific task
 */
function getTask() {
    global $conn;
    
    // Check if ID is set
    if (!isset($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'Task ID is required']);
        return;
    }
    
    $id = (int) $_GET['id'];
    
    // Get the task
    $query = "SELECT * FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $task = $result->fetch_assoc();
        echo json_encode(['success' => true, 'task' => $task]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Task not found']);
    }
    
    $stmt->close();
}

/**
 * Get all tasks
 */
function getAllTasks() {
    global $conn;
    
    // Get all tasks
    $query = "SELECT * FROM tasks ORDER BY date ASC, priority ASC";
    $result = $conn->query($query);
    
    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
    
    echo json_encode(['success' => true, 'tasks' => $tasks]);
}

/**
 * Get today's tasks
 */
function getTodayTasks() {
    global $conn;
    
    $today = date('Y-m-d');
    
    // Get today's tasks
    $query = "SELECT * FROM tasks WHERE date = ? ORDER BY priority ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $today);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
    
    echo json_encode(['success' => true, 'tasks' => $tasks]);
    
    $stmt->close();
}

/**
 * Update a task
 */
function updateTask() {
    global $conn;
    
    // Check if ID is set
    if (!isset($_POST['id'])) {
        echo json_encode(['success' => false, 'message' => 'Task ID is required']);
        return;
    }
    
    $id = (int) $_POST['id'];
    
    // Check which fields are being updated
    $fields = [];
    $types = '';
    $values = [];
    
    if (isset($_POST['title'])) {
        $fields[] = 'title = ?';
        $types .= 's';
        $values[] = sanitize($_POST['title']);
    }
    
    if (isset($_POST['description'])) {
        $fields[] = 'description = ?';
        $types .= 's';
        $values[] = sanitize($_POST['description']);
    }
    
    if (isset($_POST['priority'])) {
        $priority = sanitize($_POST['priority']);
        if (!in_array($priority, ['high', 'medium', 'low'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid priority']);
            return;
        }
        $fields[] = 'priority = ?';
        $types .= 's';
        $values[] = $priority;
    }
    
    if (isset($_POST['duration'])) {
        $duration = (int) $_POST['duration'];
        if ($duration <= 0) {
            echo json_encode(['success' => false, 'message' => 'Duration must be greater than 0']);
            return;
        }
        $fields[] = 'duration = ?';
        $types .= 'i';
        $values[] = $duration;
    }
    
    if (isset($_POST['date'])) {
        $fields[] = 'date = ?';
        $types .= 's';
        $values[] = sanitize($_POST['date']);
    }
    
    if (isset($_POST['status'])) {
        $status = sanitize($_POST['status']);
        if (!in_array($status, ['pending', 'completed'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid status']);
            return;
        }
        $fields[] = 'status = ?';
        $types .= 's';
        $values[] = $status;
    }
    
    // If no fields are being updated
    if (empty($fields)) {
        echo json_encode(['success' => false, 'message' => 'No fields to update']);
        return;
    }
    
    // Create the update query
    $query = "UPDATE tasks SET " . implode(', ', $fields) . " WHERE id = ?";
    $types .= 'i';
    $values[] = $id;
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$values);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Task updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating task: ' . $stmt->error]);
    }
    
    $stmt->close();
}

/**
 * Mark a task as complete
 */
function completeTask() {
    global $conn;
    
    // Check if ID is set
    if (!isset($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'Task ID is required']);
        return;
    }
    
    $id = (int) $_GET['id'];
    
    // Update the task status
    $query = "UPDATE tasks SET status = 'completed' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Task marked as complete']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error completing task: ' . $stmt->error]);
    }
    
    $stmt->close();
}

/**
 * Delete a task
 */
function deleteTask() {
    global $conn;
    
    // Check if ID is set
    if (!isset($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'Task ID is required']);
        return;
    }
    
    $id = (int) $_GET['id'];
    
    // Delete the task
    $query = "DELETE FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting task: ' . $stmt->error]);
    }
    
    $stmt->close();
}

/**
 * Get task completion statistics by day of the week
 */
function getTaskStats() {
    global $conn;
    
    // Get the start and end date for the current week
    $startDate = date('Y-m-d', strtotime('this week Monday'));
    $endDate = date('Y-m-d', strtotime('this week Sunday'));
    
    // Get completed tasks for each day of the week
    $query = "SELECT DATE_FORMAT(date, '%W') as day, COUNT(*) as count 
              FROM tasks 
              WHERE status = 'completed' 
              AND date BETWEEN ? AND ? 
              GROUP BY DATE_FORMAT(date, '%W')
              ORDER BY FIELD(DATE_FORMAT(date, '%W'), 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Initialize data for all days of the week
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    $counts = array_fill_keys($days, 0);
    
    // Fill in actual data
    while ($row = $result->fetch_assoc()) {
        $counts[$row['day']] = (int) $row['count'];
    }
    
    echo json_encode([
        'success' => true, 
        'labels' => $days,
        'values' => array_values($counts)
    ]);
    
    $stmt->close();
}

/**
 * Get time spent statistics by task priority
 */
function getTimeStats() {
    global $conn;
    
    // Get total time spent by priority
    $query = "SELECT priority, SUM(duration) as total 
              FROM tasks 
              WHERE status = 'completed' 
              GROUP BY priority";
    
    $result = $conn->query($query);
    
    $labels = [];
    $values = [];
    
    while ($row = $result->fetch_assoc()) {
        $labels[] = ucfirst($row['priority']) . ' Priority';
        $values[] = (int) $row['total'];
    }
    
    // If there's no data, provide default values
    if (empty($labels)) {
        $labels = ['No Data'];
        $values = [1];
    }
    
    echo json_encode([
        'success' => true, 
        'labels' => $labels,
        'values' => $values
    ]);
}

/**
 * Get progress statistics over time
 */
function getProgressStats() {
    global $conn;
    
    // Get the number of completed tasks for the last 7 days
    $dates = [];
    $counts = [];
    
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $dates[] = date('M j', strtotime($date));
        
        $query = "SELECT COUNT(*) as count FROM tasks WHERE status = 'completed' AND date = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $counts[] = (int) $row['count'];
        
        $stmt->close();
    }
    
    echo json_encode([
        'success' => true, 
        'labels' => $dates,
        'values' => $counts
    ]);
}