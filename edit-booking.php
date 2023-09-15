<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit a booking</title>

    <!-- Jquery Datepicker -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
         
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
//roomID
       $roomID = cleanInput($_POST['roomID']); 
//checkindate
       $checkindate = date_format(date_create($_POST['checkinDate']),"Y-m-d");        
//checkoutdate
       $checkoutdate = date_format(date_create($_POST['checkoutDate']),"Y-m-d");         
//contactnumber
        $contactnumber = cleanInput($_POST['contactnumber']);         
//extras
        $bookingextras = cleanInput($_POST['extras']);         
//review
        $roomreview = cleanInput($_POST['review']);         
//check check in and check out date periord
        if ($checkindate >=$checkoutdate){
          $error++;
          $msg .="Check-out date cannot be earlier than or equal to check-in date";
      }

  
//save the booking data if the error flag is still clear and booking id is > 0
    if ($error == 0 and $id > 0) {
        $query = "UPDATE booking SET roomID=?,checkinDate=?,checkoutDate=?,contactNumber=?,extras=?,Review=? WHERE bookingID=?";
        $stmt = mysqli_prepare($DBC,$query); //prepare the query
        mysqli_stmt_bind_param($stmt,'isssssi', $roomID, $checkindate, $checkoutdate, $contactnumber, $bookingextras, $roomreview, $id); 
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);    
        echo "<h2>Booking details updated.</h2>";     
//              
    } else { 
      echo "<h2><font color='red'>$msg</font></h2>".PHP_EOL;
    }      
}

$query = 'SELECT b.*, r.roomname, r.roomtype, r.beds 
FROM booking b, room r 
WHERE b.roomID  = r.roomID  AND bookingID ='.$id;

$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result);
if ($rowcount > 0) {
  $row = mysqli_fetch_assoc($result);

  $queryRoom = 'SELECT roomID, roomname, roomtype, beds FROM room ORDER BY roomID';
  $resultRoom = mysqli_query($DBC, $queryRoom);
  $roomcount = mysqli_num_rows($resultRoom);


?>
   <h1>Edit a booking</h1> 
   <h2>
     <a href="current-bookings.php">[Return to Booking Listing]</a>
     <a href="index.php">[Return to Main Page]</a>
   </h2>
   <h3>Booking made for test</h3>
   
   <form action="edit-booking.php" method="post">
     <p>
     <input type="hidden" name="id" value="<?php echo $id;?>">
   <p>
    <label for="roomID">Room (name,type,beds): </label>
    <select id="roomID" name="roomID" required> 
    <?php
      if ($roomcount > 0) {
          while ($rowR = mysqli_fetch_assoc($resultRoom)) {
              $id = $rowR['roomID']; ?>

              <option value="<?php echo $rowR['roomID']; ?>"
                      <?php echo $row['roomID']==$rowR['roomID']?'selected':''; ?> >
                  <?php echo $rowR['roomname'] . ', '
                      . $rowR['roomtype'] . ', '
                      . $rowR['beds'] ?>
              </option>
      <?php }
      } else echo "<option>No rooms found</option>";
      mysqli_free_result($resultRoom);
    ?>
    </select>
     </p>     
     <p>
    <label for="checkinDate">Checkin Date: </label>
    <input type="text" id="checkinDate" name="checkinDate" value="<?php echo date_format(date_create($row['checkinDate']),"d-m-Y"); ?>" required/>
  </p>  
  <p>  
    <label for="checkoutdate">Checkout Date: </label>
    <input type="text" id="checkoutDate" name="checkoutDate" value="<?php echo date_format(date_create($row['checkoutDate']),"d-m-Y"); ?>" required/>
   </p>   
     <p>
        <label for="contactnumber">Contact number:</label>
        <input type="tel"  id="contactnumber" name="contactnumber" 
        value="<?php echo $row['contactNumber']; ?>" required placeholder="(XXX) XXX XXXX" pattern="\(\d{3}\) \d{3} \d{4}" >
     </p>
     <p>
        <label for="extras">Booking extras:</label>
        <textarea id="extras" name="extras" rows="4" cols="25" maxlength="250"><?php echo $row['extras']; ?></textarea>
     </p>
     <p>
        <label for="review">Room review:</label>
        <textarea id="review" name="review" rows="4" cols="25" maxlength="250"><?php echo $row['Review']; ?></textarea>
     </p>
     <input type="submit" name="submit" value="Update">
     <a href="index.php">[Cancel]</a>
   </form>
   <?php 
} else { 
  echo "<h2>booking not found with that ID</h2>"; //simple error feedback
}
mysqli_close($DBC); //close the connection once done
?>
</body>
<script> 
    $("#checkinDate").datepicker({
     
      dateFormat: 'dd-mm-yy',
    });
    $("#checkoutDate").datepicker({
      
      dateFormat: 'dd-mm-yy',
    });
  </script>
</html>