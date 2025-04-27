<?php

/**
 * Database Configuration File
 * 
 * This file establishes connection to the MySQL database and handles errors
 */

// Database credentials
$servername = "localhost";
$username = "root";  // replace with your MySQL username
$password = "";      // replace with your MySQL password
$dbname = "personal_growth_tracker";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Set charset to ensure proper encoding
$conn->set_charset("utf8mb4");

/**
 * Function to sanitize input data
 * 
 * @param string $data The data to be sanitized
 * @return string Sanitized data
 */
function sanitize($data)
{
  global $conn;
  return $conn->real_escape_string(trim($data));
}

/**
 * Function to format date for display
 * 
 * @param string $date The date to format
 * @return string Formatted date
 */
function formatDate($date)
{
  return date("F j, Y, g:i a", strtotime($date));
}

/**
 * Function to log errors
 * 
 * @param string $message Error message
 * @return void
 */
function logError($message)
{
  // Log error to file
  error_log(date('[Y-m-d H:i:s] ') . $message . PHP_EOL, 3, "error.log");
}
