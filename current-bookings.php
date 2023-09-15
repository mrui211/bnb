<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current bookings</title>
</head>
<body>

<?php
include "config.php";
$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

if (mysqli_connect_errno()) {
    echo "Error: unable to connect to MYSQL." . mysqli_connect_error();
    exit;
}

$query = 'SELECT b.bookingID, r.roomname, b.checkinDate, b.checkoutDate, c.firstname, c.lastname
FROM `booking` b, room r, customer c 
WHERE b.roomID = r.roomID and b.customerID = c.customerID';
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result);

?>
   <h1>Current bookings</h1> 
    <h2>
        <a href="makebooking.php">[Make a booking]</a>
        <a href="index.php">[Return to main Page]</a>
    </h2>
    <table border="1" >
        <thead>
            <tr>
                <th>Booking (room, dates)</th>
                <th>Customer</th>
                <th>Action</th>
            </tr>
        </thead>
        <?php
        

                if ($rowcount > 0) {  
                    while ($row = mysqli_fetch_assoc($result)) {
                      $id = $row['bookingID'];	
                      echo '<tr><td>'.$row['roomname'].', '.$row['checkinDate'].', '.$row['checkoutDate'].'</td><td>'.$row['firstname'].', '.$row['lastname'].'</td>';
                      echo     '<td><a href="view-booking.php?id='.$id.'">[view]</a>';
                      echo         '<a href="edit-booking.php?id='.$id.'">[edit]</a>';
                      echo         '<a href="manage-reviews.php?id='.$id.'">[manage reviews]</a>';
                      echo         '<a href="delete-booking.php?id='.$id.'">[delete]</a></td>';
                      echo '</tr>'.PHP_EOL;
                   }
                } else echo "<h2>No bookings found!</h2>"; //suitable feedback

        mysqli_free_result($result);
        mysqli_close($DBC);
        ?>
    </table>
</body>
</html>