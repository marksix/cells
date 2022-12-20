<?php
error_reporting(0);

session_start();

$servername = "localhost";
$username = "cms_eas_plus";
$password = "password";
$dbname = "cms_eas_plus";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];

  // check if user exists
  $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $name = $row['name'];
    $email = $row['email'];
  } else {
    // user not found
    session_destroy();
    header("Location: login.html");
    exit;
  }
} else {
  // not logged in
  header("Location: login.html");
  exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>User</title>
  <style>
  /* 提示的样式 */
  .notification {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    padding: 20px;
    text-align: center;
    font-size: 16px;
    z-index: 1;
    transition: all 0.3s ease-out;
  }
</style>
</head>
<body>
  <div id="header">
    <p>Welcome, <?php echo $name; ?></p>
    <p>Email: <?php echo $email; ?></p>
    <a href="logout.php">Logout</a>
  </div>
  <div id="main">
    <table id="grid">
    </table>
  </div>
  <div id="footer">
    <!-- footer content goes here -->
  </div>
   
   <script>
const grid = document.getElementById("grid");

// send a request to get-color.php to get the colors from the database
fetch("get-color.php").then(function(response) {
  return response.text();
}).then(function(text) {
  // parse the response text into an array of colors
  const results = text.split(",");
  let index = 0;
  for (let i = 0; i < 50; i++) {
    const row = document.createElement("tr");
    for (let j = 0; j < 100; j++) {
      const cell = document.createElement("td");
      cell.style.backgroundColor = results[index];
      cell.style.width = "10px";
      cell.style.height = "10px";
      
      cell.addEventListener("click", function() {
        // generate a random color and update the cell's color
        const randomColor = '#' + Math.floor(Math.random() * 16777215).toString(16);
        cell.style.backgroundColor = randomColor;
        
        // save the new color to the database
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "save-color.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function() {
          if (xhr.status === 200) {
            // show a notification that the color was saved
            const notification = document.createElement("div");
            notification.innerHTML = `<p>${i},${j} 坐标格子已保存。</p>`;
            notification.style.position = "absolute";
            notification.style.top = "0";
            notification.style.left = "50%";
            notification.style.transform = "translate(-50%, 0)";
            notification.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
            notification.style.color = "white";
            notification.style.padding = "10px";
            notification.style.borderRadius = "5px";
            document.body.appendChild(notification);
            // remove the notification after 3 seconds
            setTimeout(function() {
              document.body.removeChild(notification);
            }, 3000);
          } else {
            console.error("Error saving color to the database.");
          }
        };
        xhr.send(`color=${randomColor}&cell=${i},${j}`);
      });
      
      row.appendChild(cell);
      index++;
    }
    grid.appendChild(row);
  }
});
</script>

   
   
   
  <div id="header">
    <p>Welcome, <?php echo $name; ?></p>
    <p>Email: <?php echo $email; ?></p>
    <a href="logout.php">Logout</a>
  </div>
  <div id="main">
    <table id="grid">
    </table>
  </div>
  <div id="footer">
    <!-- footer content goes here -->
  </div>
</body>
</html>
