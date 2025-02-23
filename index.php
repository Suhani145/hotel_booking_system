<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    echo "<a href='login.php'>Login</a> or <a href='register.php'>Register</a> to book a room.";
    exit;
}
?>

<form method="POST" action="book_room.php">
    User ID: <input type="number" name="user_id" required><br>
    Room ID: <input type="text" name="room_id" required><br>
    Check-In: <input type="date" name="check_in" required><br>
    Check-Out: <input type="date" name="check_out" required><br>
    <input type="submit" value="Book Room">
</form>
