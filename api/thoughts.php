<?php

/**
 * Thoughts API
 * 
 * Handles CRUD operations for personal thoughts
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
    addThought();
    break;

  case 'get':
    getThought();
    break;

  case 'getAll':
    getAllThoughts();
    break;

  case 'getRecent':
    getRecentThoughts();
    break;

  case 'update':
    updateThought();
    break;

  case 'delete':
    deleteThought();
    break;

  default:
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    break;
}

/**
 * Add a new thought
 */
function addThought()
{
  global $conn;

  // Check if required fields are set
  if (!isset($_POST['thought'])) {
    echo json_encode(['success' => false, 'message' => 'Thought text is required']);
    return;
  }

  // Sanitize input
  $thought = sanitize($_POST['thought']);
  $date = date('Y-m-d H:i:s');

  // Validate data
  if (empty($thought)) {
    echo json_encode(['success' => false, 'message' => 'Thought cannot be empty']);
    return;
  }

  // Insert the thought
  $query = "INSERT INTO thoughts (thought, date) VALUES (?, ?)";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('ss', $thought, $date);

  if ($stmt->execute()) {
    $thoughtId = $stmt->insert_id;
    echo json_encode(['success' => true, 'message' => 'Thought added successfully', 'thoughtId' => $thoughtId]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Error adding thought: ' . $stmt->error]);
  }

  $stmt->close();
}

/**
 * Get a specific thought
 */
function getThought()
{
  global $conn;

  // Check if ID is set
  if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Thought ID is required']);
    return;
  }

  $id = (int) $_GET['id'];

  // Get the thought
  $query = "SELECT * FROM thoughts WHERE id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $thought = $result->fetch_assoc();
    echo json_encode(['success' => true, 'thought' => $thought]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Thought not found']);
  }

  $stmt->close();
}

/**
 * Get all thoughts
 */
function getAllThoughts()
{
  global $conn;

  // Get all thoughts
  $query = "SELECT * FROM thoughts ORDER BY date DESC";
  $result = $conn->query($query);

  $thoughts = [];
  while ($row = $result->fetch_assoc()) {
    $thoughts[] = $row;
  }

  echo json_encode(['success' => true, 'thoughts' => $thoughts]);
}

/**
 * Get recent thoughts
 */
function getRecentThoughts()
{
  global $conn;

  // Get limit parameter or use default
  $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;

  // Get recent thoughts
  $query = "SELECT * FROM thoughts ORDER BY date DESC LIMIT ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('i', $limit);
  $stmt->execute();
  $result = $stmt->get_result();

  $thoughts = [];
  while ($row = $result->fetch_assoc()) {
    $thoughts[] = $row;
  }

  echo json_encode(['success' => true, 'thoughts' => $thoughts]);

  $stmt->close();
}

/**
 * Update a thought
 */
function updateThought()
{
  global $conn;

  // Check if ID is set
  if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Thought ID is required']);
    return;
  }

  $id = (int) $_POST['id'];

  // Check if thought text is set
  if (!isset($_POST['thought'])) {
    echo json_encode(['success' => false, 'message' => 'Thought text is required']);
    return;
  }

  // Sanitize input
  $thought = sanitize($_POST['thought']);

  // Validate data
  if (empty($thought)) {
    echo json_encode(['success' => false, 'message' => 'Thought cannot be empty']);
    return;
  }

  // Update the thought
  $query = "UPDATE thoughts SET thought = ? WHERE id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('si', $thought, $id);

  if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Thought updated successfully']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Error updating thought: ' . $stmt->error]);
  }

  $stmt->close();
}

/**
 * Delete a thought
 */
function deleteThought()
{
  global $conn;

  // Check if ID is set
  if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Thought ID is required']);
    return;
  }

  $id = (int) $_GET['id'];

  // Delete the thought
  $query = "DELETE FROM thoughts WHERE id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('i', $id);

  if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Thought deleted successfully']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Error deleting thought: ' . $stmt->error]);
  }

  $stmt->close();
}
