<?php

/**
 * Quotes API
 * 
 * Handles CRUD operations for motivational quotes
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
    addQuote();
    break;

  case 'get':
    getQuote();
    break;

  case 'getAll':
    getAllQuotes();
    break;

  case 'random':
    getRandomQuote();
    break;

  case 'update':
    updateQuote();
    break;

  case 'delete':
    deleteQuote();
    break;

  default:
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    break;
}

/**
 * Add a new quote
 */
function addQuote()
{
  global $conn;

  // Check if required fields are set
  if (!isset($_POST['quote'])) {
    echo json_encode(['success' => false, 'message' => 'Quote text is required']);
    return;
  }

  // Sanitize input
  $quote = sanitize($_POST['quote']);
  $author = isset($_POST['author']) ? sanitize($_POST['author']) : 'Unknown';

  // Validate data
  if (empty($quote)) {
    echo json_encode(['success' => false, 'message' => 'Quote cannot be empty']);
    return;
  }

  // Insert the quote
  $query = "INSERT INTO quotes (quote, author) VALUES (?, ?)";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('ss', $quote, $author);

  if ($stmt->execute()) {
    $quoteId = $stmt->insert_id;
    echo json_encode(['success' => true, 'message' => 'Quote added successfully', 'quoteId' => $quoteId]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Error adding quote: ' . $stmt->error]);
  }

  $stmt->close();
}

/**
 * Get a specific quote
 */
function getQuote()
{
  global $conn;

  // Check if ID is set
  if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Quote ID is required']);
    return;
  }

  $id = (int) $_GET['id'];

  // Get the quote
  $query = "SELECT * FROM quotes WHERE id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $quote = $result->fetch_assoc();
    echo json_encode(['success' => true, 'quote' => $quote]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Quote not found']);
  }

  $stmt->close();
}

/**
 * Get all quotes
 */
function getAllQuotes()
{
  global $conn;

  // Get all quotes
  $query = "SELECT * FROM quotes ORDER BY id DESC";
  $result = $conn->query($query);

  $quotes = [];
  while ($row = $result->fetch_assoc()) {
    $quotes[] = $row;
  }

  echo json_encode(['success' => true, 'quotes' => $quotes]);
}

/**
 * Get a random quote
 */
function getRandomQuote()
{
  global $conn;

  // Get a random quote
  $query = "SELECT * FROM quotes ORDER BY RAND() LIMIT 1";
  $result = $conn->query($query);

  if ($result->num_rows > 0) {
    $quote = $result->fetch_assoc();
    echo json_encode(['success' => true, 'quote' => $quote]);
  } else {
    echo json_encode(['success' => false, 'message' => 'No quotes found']);
  }
}

/**
 * Update a quote
 */
function updateQuote()
{
  global $conn;

  // Check if ID is set
  if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Quote ID is required']);
    return;
  }

  $id = (int) $_POST['id'];

  // Check which fields are being updated
  $fields = [];
  $types = '';
  $values = [];

  if (isset($_POST['quote'])) {
    $quote = sanitize($_POST['quote']);
    if (empty($quote)) {
      echo json_encode(['success' => false, 'message' => 'Quote cannot be empty']);
      return;
    }
    $fields[] = 'quote = ?';
    $types .= 's';
    $values[] = $quote;
  }

  if (isset($_POST['author'])) {
    $fields[] = 'author = ?';
    $types .= 's';
    $values[] = sanitize($_POST['author']);
  }

  // If no fields are being updated
  if (empty($fields)) {
    echo json_encode(['success' => false, 'message' => 'No fields to update']);
    return;
  }

  // Create the update query
  $query = "UPDATE quotes SET " . implode(', ', $fields) . " WHERE id = ?";
  $types .= 'i';
  $values[] = $id;

  $stmt = $conn->prepare($query);
  $stmt->bind_param($types, ...$values);

  if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Quote updated successfully']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Error updating quote: ' . $stmt->error]);
  }

  $stmt->close();
}

/**
 * Delete a quote
 */
function deleteQuote()
{
  global $conn;

  // Check if ID is set
  if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Quote ID is required']);
    return;
  }

  $id = (int) $_GET['id'];

  // Delete the quote
  $query = "DELETE FROM quotes WHERE id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('i', $id);

  if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Quote deleted successfully']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Error deleting quote: ' . $stmt->error]);
  }

  $stmt->close();
}
