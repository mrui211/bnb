<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete booking</title>
</head>
<body>
<?php
include "config.php"; //load in any variables
$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

//insert DB code from here onwards
//check if the connection was good
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
    exit; //stop processing the page further
}

//function to clean input but not validate type and content
function cleanInput($data) {  
  return htmlspecialchars(stripslashes(trim($data)));
}

//retrieve the BookingID from the URL
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    if (empty($id) or !is_numeric($id)) {
        echo "<h2>Invalid Booking ID</h2>"; //simple error feedback
        exit;
    } 
}


if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Delete')) {     
    $error = 0; //clear our error flag
    $msg = 'Error: ';  
//BookingID (sent via a form it is a string not a number so we try a type conversion!)    
    if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
       $id = cleanInput($_POST['id']); 
    } else {
       $error++; //bump the error flag
       $msg .= 'Invalid Booking ID '; //append error message
       $id = 0;  
    }        
    

    if ($error == 0 and $id > 0) {
        $query = "DELETE FROM booking WHERE bookingID =?";
        $stmt = mysqli_prepare($DBC,$query); //prepare the query
        mysqli_stmt_bind_param($stmt,'i', $id); 
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);    
        echo "<h2>Booking details deleted.</h2>";     
        
    } else { 
      echo "<h2><font color='red'>$msg</font></h2>".PHP_EOL;
    }      

}

$query = 'SELECT *, r.roomname FROM booking b, Room r WHERE b.roomID = r.roomID AND bookingID ='.$id;
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result); 
?>

   <h1>Booking preview before deletion</h1> 
   <h2>
     <a href="current-bookings.php">[Return to Booking Listing]</a>
     <a href="index.php">[Return to Main Page]</a>
   </h2>

   <?php
  

if ($rowcount > 0) {  
    echo "<fieldset><legend>Booking detail #$id</legend><dl>"; 
    $row = mysqli_fetch_assoc($result);
    echo "<dt>Room name:</dt><dd>".$row['roomname']."</dd>".PHP_EOL;
    echo "<dt>Checkin Date:</dt><dd>".$row['checkinDate']."</dd>".PHP_EOL;
    echo "<dt>Checkout Date:</dt><dd>".$row['checkoutDate']."</dd>".PHP_EOL;
    echo '</dl></fieldset>'.PHP_EOL;  
   ?>
   <form method="POST" action="delete-booking.php">
     <h2>Are you sure you want to delete this Booking?</h2>
     <input type="hidden" name="id" value="<?php echo $id; ?>">
     <input type="submit" name="submit" value="Delete">
     <a href="current-bookings.php">[Cancel]</a>
     </form>
<?php    
} else echo "<h2>No Booking found, possbily deleted!</h2>"; 

mysqli_free_result($result); //free any memory used by the query
mysqli_close($DBC); //close the connection once done
?>
</table>
</body>
</html>
