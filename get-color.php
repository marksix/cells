<?php
error_reporting(0);

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

$stmt = $conn->prepare("SELECT * FROM colors");
$stmt->execute();
$result = $stmt->get_result();

$colors = array();
if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    $colors[] = array(
      "cell" => $row["cell"],
      "color" => $row["color"]
    );
  }
}

$conn->close();

echo json_encode($colors);
?>
