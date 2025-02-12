<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$servername = "localhost";
$username = "u961960612_arkadenestuser";
$password = "2YB5qE5^~tK";
$database = "u961960612_arkadenest";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Form data validation
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['fname']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $state = trim($_POST['state'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $budget = trim($_POST['budget'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $project = trim($_POST['project'] ?? '');
    $property = trim($_POST['property'] ?? '');
    $leadExpectedBudget = trim($_POST['leadExpectedBudget'] ?? '');
    $propertyType = trim($_POST['propertyType'] ?? '');
    $propertySubType = trim($_POST['propertySubType'] ?? '');
    $leadStatus = trim($_POST['leadStatus'] ?? '');
    $bhkType = trim($_POST['bhkType'] ?? '');
    $primaryUser = trim($_POST['primaryUser'] ?? '');
    $secondaryUser = trim($_POST['secondaryUser'] ?? '');
    
    // Validate name (only letters and spaces allowed)
    if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
        die("Invalid name. Only letters and spaces are allowed.");
    }

    // Validate phone (only numbers allowed)
    if (!preg_match("/^[0-9]{10,15}$/", $phone)) {
        die("Invalid phone number. Only numbers with 10 to 15 digits are allowed.");
    }

    // Validate email
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email address.");
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO arkadenest (name, phone, email) VALUES (?, ?, ?)");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("sss", $name, $phone, $email);

    if ($stmt->execute()) {
        // Send data to LeadRat CRM
        $crm_url = "https://connect.leadrat.com/api/v1/integration/GoogleAds";
        $api_key = "YjBlYTgzYjAtNTc0OS00NGZhLTk3ZGEtMjg0ZjRkZDU3MzNh"; // Replace with your actual API key

        $crm_data = [
            "name" => $name,
            "state" => $state,
            "city" => $city,
            "location" => $location,
            "budget" => $budget,
            "notes" => $notes,
            "email" => $email,
            "countryCode" => "91",
            "mobile" => $phone,
            "project" => $project,
            "property" => $property,
            "leadExpectedBudget" => $leadExpectedBudget,
            "propertyType" => $propertyType,
            "propertySubType" => $propertySubType,
            "submittedDate" => date("d-m-y"),
            "submittedTime" => date("H:i:s"),
            "leadStatus" => $leadStatus,
            "bhkType" => $bhkType,
            "leadScheduledDate" => date("d-m-y"),
            "leadScheduleTime" => date("H:i:s"),
            "leadBookedDate" => date("d-m-y"),
            "leadBookedTime" => date("H:i:s"),
            "additionalProperties" => [
                "EnquiredFor" => "Buy/Sale/Rent",
                "BHKType" => $bhkType,
                "NoOfBHK" => "0",
                "key1" => "value1",
                "key2" => "value2"
            ],
            "primaryUser" => $primaryUser,
            "secondaryUser" => $secondaryUser
        ];

        $headers = [
            "Content-Type: application/json",
            "API-Key: $api_key"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $crm_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($crm_data));

        $crm_response = curl_exec($ch);
        $crm_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($crm_http_code === 200) {
            // Send email notification
            $to = "rohitwarke08@gmail.com, iammihirkarande@gmail.com";
            $subject = "Arkade Nest Form Submission";
            $message = "Name: $name\nPhone: $phone\nEmail: $email";
            $headers = "From: noreply@arkadenest.com";

            if (mail($to, $subject, $message, $headers)) {
                header("Location: thankyou.php");
                exit();
            } else {
                echo "Error: Email not sent.";
            }
        } else {
            echo "Error sending data to CRM. Response: " . $crm_response;
        }
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>