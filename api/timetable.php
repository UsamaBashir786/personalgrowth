<?php

/**
 * Timetable API
 * 
 * Handles CRUD operations for weekly timetable
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
    addSchedule();
    break;

  case 'get':
    getSchedule();
    break;

  case 'getAll':
    getAllSchedules();
    break;

  case 'getByDay':
    getSchedulesByDay();
    break;

  case 'update':
    updateSchedule();
    break;

  case 'delete':
    deleteSchedule();
    break;

  default:
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    break;
}

/**
 * Add a new schedule
 */
function addSchedule()
{
  global $conn;

  // Check if required fields are set
  if (!isset($_POST['day']) || !isset($_POST['time']) || !isset($_POST['task'])) {
    echo json_encode(['success' => false, 'message' => 'All fields (day, time, task) are required']);
    return;
  }

  // Sanitize input
  $day = sanitize($_POST['day']);
  $time = sanitize($_POST['time']);
  $task = sanitize($_POST['task']);

  // Validate data
  $validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
  if (!in_array($day, $validDays)) {
    echo json_encode(['success' => false, 'message' => 'Invalid day']);
    return;
  }

  if (empty($time)) {
    echo json_encode(['success' => false, 'message' => 'Time cannot be empty']);
    return;
  }

  if (empty($task)) {
    echo json_encode(['success' => false, 'message' => 'Task cannot be empty']);
    return;
  }

  // Insert the schedule
  $query = "INSERT INTO timetable (day, time, task) VALUES (?, ?, ?)";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('sss', $day, $time, $task);

  if ($stmt->execute()) {
    $scheduleId = $stmt->insert_id;
    echo json_encode(['success' => true, 'message' => 'Schedule added successfully', 'scheduleId' => $scheduleId]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Error adding schedule: ' . $stmt->error]);
  }

  $stmt->close();
}

/**
 * Get a specific schedule
 */
function getSchedule()
{
  global $conn;

  // Check if ID is set
  if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Schedule ID is required']);
    return;
  }

  $id = (int) $_GET['id'];

  // Get the schedule
  $query = "SELECT * FROM timetable WHERE id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $schedule = $result->fetch_assoc();
    echo json_encode(['success' => true, 'schedule' => $schedule]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Schedule not found']);
  }

  $stmt->close();
}

/**
 * Get all schedules
 */
function getAllSchedules()
{
  global $conn;

  // Get all schedules
  $query = "SELECT * FROM timetable ORDER BY day, time ASC";
  $result = $conn->query($query);

  $schedules = [];
  while ($row = $result->fetch_assoc()) {
    $schedules[] = $row;
  }

  echo json_encode(['success' => true, 'schedules' => $schedules]);
}

/**
 * Get schedules by day
 */
function getSchedulesByDay()
{
  global $conn;

  // Check if day is set
  if (!isset($_GET['day'])) {
    echo json_encode(['success' => false, 'message' => 'Day is required']);
    return;
  }

  $day = sanitize($_GET['day']);

  // Validate day
  $validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
  if (!in_array($day, $validDays)) {
    echo json_encode(['success' => false, 'message' => 'Invalid day']);
    return;
  }

  // Get schedules for the specified day
  $query = "SELECT * FROM timetable WHERE day = ? ORDER BY time ASC";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('s', $day);
  $stmt->execute();
  $result = $stmt->get_result();

  $schedules = [];
  while ($row = $result->fetch_assoc()) {
    $schedules[] = $row;
  }

  echo json_encode(['success' => true, 'schedules' => $schedules]);

  $stmt->close();
}

/**
 * Update a schedule
 */
function updateSchedule()
{
  global $conn;

  // Check if ID is set
  if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Schedule ID is required']);
    return;
  }

  $id = (int) $_POST['id'];

  // Check which fields are being updated
  $fields = [];
  $types = '';
  $values = [];

  if (isset($_POST['day'])) {
    $day = sanitize($_POST['day']);
    $validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    if (!in_array($day, $validDays)) {
      echo json_encode(['success' => false, 'message' => 'Invalid day']);
      return;
    }
    $fields[] = 'day = ?';
    $types .= 's';
    $values[] = $day;
  }

  if (isset($_POST['time'])) {
    $time = sanitize($_POST['time']);
    if (empty($time)) {
      echo json_encode(['success' => false, 'message' => 'Time cannot be empty']);
      return;
    }
    $fields[] = 'time = ?';
    $types .= 's';
    $values[] = $time;
  }

  if (isset($_POST['task'])) {
    $task = sanitize($_POST['task']);
    if (empty($task)) {
      echo json_encode(['success' => false, 'message' => 'Task cannot be empty']);
      return;
    }
    $fields[] = 'task = ?';
    $types .= 's';
    $values[] = $task;
  }

  // If no fields are being updated
  if (empty($fields)) {
    echo json_encode(['success' => false, 'message' => 'No fields to update']);
    return;
  }

  // Create the update query
  $query = "UPDATE timetable SET " . implode(', ', $fields) . " WHERE id = ?";
  $types .= 'i';
  $values[] = $id;

  $stmt = $conn->prepare($query);
  $stmt->bind_param($types, ...$values);

  if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Schedule updated successfully']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Error updating schedule: ' . $stmt->error]);
  }

  $stmt->close();
}

/**
 * Delete a schedule
 */
function deleteSchedule()
{
  global $conn;

  // Check if ID is set
  if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Schedule ID is required']);
    return;
  }

  $id = (int) $_GET['id'];

  // Delete the schedule
  $query = "DELETE FROM timetable WHERE id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('i', $id);

  if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Schedule deleted successfully']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Error deleting schedule: ' . $stmt->error]);
  }

  $stmt->close();
}
