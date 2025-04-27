<?php

/**
 * Learnings API
 * 
 * Handles CRUD operations for important learnings
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
    addLearning();
    break;

  case 'get':
    getLearning();
    break;

  case 'getAll':
    getAllLearnings();
    break;

  case 'getRecent':
    getRecentLearnings();
    break;

  case 'update':
    updateLearning();
    break;

  case 'delete':
    deleteLearning();
    break;

  default:
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    break;
}

/**
 * Add a new learning
 */
function addLearning()
{
  global $conn;

  // Check if required fields are set
  if (!isset($_POST['learning'])) {
    echo json_encode(['success' => false, 'message' => 'Learning text is required']);
    return;
  }

  // Sanitize input
  $learning = sanitize($_POST['learning']);
  $date = date('Y-m-d H:i:s');

  // Validate data
  if (empty($learning)) {
    echo json_encode(['success' => false, 'message' => 'Learning cannot be empty']);
    return;
  }

  // Insert the learning
  $query = "INSERT INTO learnings (learning, date) VALUES (?, ?)";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('ss', $learning, $date);

  if ($stmt->execute()) {
    $learningId = $stmt->insert_id;
    echo json_encode(['success' => true, 'message' => 'Learning added successfully', 'learningId' => $learningId]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Error adding learning: ' . $stmt->error]);
  }

  $stmt->close();
}

/**
 * Get a specific learning
 */
function getLearning()
{
  global $conn;

  // Check if ID is set
  if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Learning ID is required']);
    return;
  }

  $id = (int) $_GET['id'];

  // Get the learning
  $query = "SELECT * FROM learnings WHERE id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $learning = $result->fetch_assoc();
    echo json_encode(['success' => true, 'learning' => $learning]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Learning not found']);
  }

  $stmt->close();
}

/**
 * Get all learnings
 */
function getAllLearnings()
{
  global $conn;

  // Get all learnings
  $query = "SELECT * FROM learnings ORDER BY date DESC";
  $result = $conn->query($query);

  $learnings = [];
  while ($row = $result->fetch_assoc()) {
    $learnings[] = $row;
  }

  echo json_encode(['success' => true, 'learnings' => $learnings]);
}

/**
 * Get recent learnings
 */
function getRecentLearnings()
{
  global $conn;

  // Get limit parameter or use default
  $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;

  // Get recent learnings
  $query = "SELECT * FROM learnings ORDER BY date DESC LIMIT ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('i', $limit);
  $stmt->execute();
  $result = $stmt->get_result();

  $learnings = [];
  while ($row = $result->fetch_assoc()) {
    $learnings[] = $row;
  }

  echo json_encode(['success' => true, 'learnings' => $learnings]);

  $stmt->close();
}

/**
 * Update a learning
 */
function updateLearning()
{
  global $conn;

  // Check if ID is set
  if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Learning ID is required']);
    return;
  }

  $id = (int) $_POST['id'];

  // Check if learning text is set
  if (!isset($_POST['learning'])) {
    echo json_encode(['success' => false, 'message' => 'Learning text is required']);
    return;
  }

  // Sanitize input
  $learning = sanitize($_POST['learning']);

  // Validate data
  if (empty($learning)) {
    echo json_encode(['success' => false, 'message' => 'Learning cannot be empty']);
    return;
  }

  // Update the learning
  $query = "UPDATE learnings SET learning = ? WHERE id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('si', $learning, $id);

  if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Learning updated successfully']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Error updating learning: ' . $stmt->error]);
  }

  $stmt->close();
}

/**
 * Delete a learning
 */
function deleteLearning()
{
  global $conn;

  // Check if ID is set
  if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Learning ID is required']);
    return;
  }

  $id = (int) $_GET['id'];

  // Delete the learning
  $query = "DELETE FROM learnings WHERE id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('i', $id);

  if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Learning deleted successfully']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Error deleting learning: ' . $stmt->error]);
  }

  $stmt->close();
}
