<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage reviews</title>
</head>
<body>
<?php
include "config.php"; //load in any variables
$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

if (mysqli_connect_errno()) {
  echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
  exit; //stop processing the page further
};

//function to clean input but not validate type and content
function cleanInput($data) {  
  return htmlspecialchars(stripslashes(trim($data)));
}

//retrieve the bookingID from the URL
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    if (empty($id) or !is_numeric($id)) {
        echo "<h2>Invalid booking ID</h2>"; //simple error feedback
        exit;
    } 
}

if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Update')) {     
  $error = 0; //clear our error flag
  $msg = 'Error: ';  

    if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
       $id = cleanInput($_POST['id']); 
    } else {
       $error++; //bump the error flag
       $msg .= 'Invalid booking ID '; //append error message
       $id = 0;  
    }   
//roomreview
        $roomreview = cleanInput($_POST['review']);         
    
//save the booking data if the error flag is still clear and booking id is > 0
    if ($error == 0 and $id > 0) {
        $query = "UPDATE booking SET Review=? WHERE bookingID=?";
        $stmt = mysqli_prepare($DBC,$query); //prepare the query
        mysqli_stmt_bind_param($stmt,'si', $roomreview, $id); 
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);    
        echo "<h2>Room review updated.</h2>";     
          
    } else { 
      echo "<h2>$msg</h2>".PHP_EOL;
    }      
}

$query = 'SELECT * FROM booking WHERE bookingID='.$id;
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result);

if ($rowcount > 0) {
  $row = mysqli_fetch_assoc($result);
?>
   <h1>Edit/add room review</h1> 
   <h2>
     <a href="current-bookings.php">[Return to Booking Listing]</a>
     <a href="index.php">[Return to Main Page]</a>
   </h2>
   <h3>Review made by test</h3>
    
   <form action="manage-reviews.php" method="post">
   <input type="hidden" name="id" value="<?php echo $id;?>">
     <p>
        <labe for="review">Room review:</label>
        <textarea id="review" name="review" rows="4" cols="25" maxlength="250"><?php echo $row['Review']; ?></textarea>
     </p>
     <input type="submit" name="submit" value="Update">
   </form> 
   <?php 
} else { 
  echo "<h2>booking not found with that ID</h2>"; //simple error feedback
}
mysqli_close($DBC); //close the connection once done
?>  
</body>
</html>