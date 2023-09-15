<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking view</title>
</head>
<body>
<?php

include "config.php";
$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

if (mysqli_connect_errno()) {
    echo "Error:Unable to connect to MySql." . mysqli_connect_error();
    exit; 
}

//check if id exists
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $id = $_GET['id'];
    if (empty($id) or !is_numeric($id)) {
        echo "<h2>Invalid booking id</h2>";
        exit;
    }
}


$query = "SELECT bookingID, r.roomName, checkinDate, checkoutDate, contactNumber, extras, review 
          FROM booking b
          INNER JOIN room r ON b.roomID = r.roomID
          WHERE b.bookingID = " . $id;

$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);
?>


   <h3>Logged in as Test</h3>
   <h1>Booking details View</h1> 
   <h2>
     <a href="current-bookings.php">[Return to Booking Listing]</a>
     <a href="index.php">[Return to Main Page]</a>
   </h2>
 
 <?php
if ($rowcount > 0) { 
    echo "<fieldset><legend>Booking Detail #$id</legend><dl>";
    $row = mysqli_fetch_assoc($result);
    
    echo "<dt>Room name: </dt><dd>" . $row['roomName'] . "</dd>" . PHP_EOL;
    echo "<dt>Checkin date: </dt><dd>" . $row['checkinDate'] . "</dd>" . PHP_EOL;
    echo "<dt>Checkout date: </dt><dd>" . $row['checkoutDate'] . "</dd>" . PHP_EOL;

    echo "<dt>Contact number: </dt><dd>" . $row['contactNumber'] . "</dd>" . PHP_EOL;
    echo "<dt>Extras: </dt><dd>" . $row['extras'] . "</dd>" . PHP_EOL;
    echo "<dt>Room review: </dt><dd>" . $row['review'] . "</dd>" . PHP_EOL;
    echo '</dl></fieldset>' . PHP_EOL;

} else echo "<h5>No booking found! Possbily deleted!</h5>";
mysqli_free_result($result);
mysqli_close($DBC);
?>

</body>
</html>