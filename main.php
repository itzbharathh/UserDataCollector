<!DOCTYPE html>
<html>
<head>
    <title>User Data Collection</title>
</head>
<body>
<?php
// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- Database Configuration ---
    $servername = "192.168.0.151";
    $username = "udc";
    $password = "Bharath@9513";
    $dbname = "project";
    // Create a database connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    // --- Process Form Data (SAFE WAY) ---
    // Collect user data from the form
    $name = $_POST["name"];
    $age = $_POST["age"];
    $country = $_POST["country"];
    // Use PREPARED STATEMENTS to prevent SQL Injection
    $stmt = $conn->prepare("INSERT INTO users (name, age, country) VALUES (?, ?, ?)");
    // 'ssi' means the variables are: String, String, Integer. Adjust if your 'age' is a number.
    // If 'age' is an INT in your database, use 'isi' for Integer, String, Integer.
    // Let's assume name and country are strings, age is an integer.
    $stmt->bind_param("sis", $name, $age, $country);
    if ($stmt->execute()) {
        echo "User data has been successfully stored in the database.<br>";
    } else {
        echo "Error storing data: " . $stmt->error . "<br>";
    }
    // Close the statement and connection
    $stmt->close();
    $conn->close();
    // --- Handle File Upload (SAFER WAY) ---
    // Check if a file was actually uploaded without errors
    if (isset($_FILES['userfile']) && $_FILES['userfile']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = '/var/udc/uploads/';
        // Sanitize the filename to prevent security issues
        $fileName = preg_replace("/[^a-zA-Z0-9\._-]/", "", basename($_FILES['userfile']['name']));
        $uploadFile = $uploadDir . $fileName;
        // Check if upload directory exists and is writable
        if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
             echo "File upload failed: Upload directory does not exist or is not writable.<br>";
        } else if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadFile)) {
            echo "File has been successfully uploaded as " . htmlspecialchars($fileName) . "<br>";
        } else {
            echo "File upload failed. Possible permission error or server issue.<br>";
        }
    } else {
        // Handle different upload errors
        $upload_error = isset($_FILES['userfile']['error']) ? $_FILES['userfile']['error'] : 'No file uploaded';
        echo "File upload error code: " . $upload_error . "<br>";
    }
}
?>
<h2>Enter User Information</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
    Name: <input type="text" name="name" required><br>
    Age: <input type="number" name="age" required><br>
    Country: <input type="text" name="country" required><br>
    File Upload: <input type="file" name="userfile"><br>
    <input type="submit" value="Submit">
</form>
</body>
</html>
