<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Error: Invalid request. Use POST method.");
}

// Get POST data
$user_id = $_SESSION['user_id'] ?? $_POST['user_id'] ?? null;
$room_id = $_POST['room_id'] ?? null;
$check_in = $_POST['check_in'] ?? null;
$check_out = $_POST['check_out'] ?? null;

echo "User ID: " . htmlspecialchars($user_id) . "<br>";
echo "Room ID: " . htmlspecialchars($room_id) . "<br>";
echo "Check-In: " . htmlspecialchars($check_in) . "<br>";
echo "Check-Out: " . htmlspecialchars($check_out) . "<br>";

if (!$user_id || !$room_id || !$check_in || !$check_out) {
    die("Error: Missing required fields.");
}


// Step 1: Check if the room exists and is available
$roomQuery = "SELECT id FROM rooms WHERE id = ? AND status = 'available'";
$stmt = $conn->prepare($roomQuery);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    die("Error: Room is not available or does not exist.");
}

$stmt->close();

// Step 2: Check if the room is already booked
$checkQuery = "SELECT id FROM bookings WHERE room_id = ? AND status = 'confirmed' 
               AND ((check_in <= ? AND check_out >= ?) OR (check_in <= ? AND check_out >= ?) 
               OR (check_in >= ? AND check_out <= ?))";

$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("issssss", $room_id, $check_in, $check_in, $check_out, $check_out, $check_in, $check_out);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "Error: Room is already booked for the selected dates!";
} else {
    // Step 3: Book the room
    $stmt->close();

    $insertQuery = "INSERT INTO bookings (user_id, room_id, check_in, check_out, status)
                    VALUES (?, ?, ?, ?, 'confirmed')";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("iiss", $user_id, $room_id, $check_in, $check_out);

    if ($stmt->execute()) {
        echo "Success: Room booked successfully!";
    } else {
        echo "Error: Booking failed! " . $stmt->error;
    }
}

$stmt->close();
$conn->close();
?>
