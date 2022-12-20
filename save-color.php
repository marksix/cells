<?php
error_reporting(0);

session_start();

$servername = "localhost";
$username = "cms_eas_plus";
$password = "7281213";
$dbname = "cms_eas_plus";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
  // not logged in
  header("HTTP/1.1 401 Unauthorized");
  exit;
}

// get the color and cell from the request
$color = $_POST['color'];
$cell = $_POST['cell'];

// check if a row with the same cell already exists in the database
$stmt = $conn->prepare("SELECT * FROM colors WHERE cell = ?");
$stmt->bind_param("s", $cell);
$stmt->execute();
$result = $stmt->get_result();
if (mysqli_num_rows($result) > 0) {
  // row already exists, update the color
  $stmt = $conn->prepare("UPDATE colors SET color = ? WHERE cell = ?");
  $stmt->bind_param("ss", $color, $cell);
  $stmt->execute();
} else {
  // row does not exist, insert a new row
  $stmt = $conn->prepare("INSERT INTO colors (color, cell) VALUES (?, ?)");
  $stmt->bind_param("ss", $color, $cell);
  $stmt->execute();
}

$conn->close();

// show a notification
echo "<div class='notification'>$cell 坐标格子已保存。</div>";

// return the updated color to the client
echo $color;
?>
