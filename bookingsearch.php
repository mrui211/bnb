<?php
//Our booking search/filtering engine
include "config.php"; //load in any variables

$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE) or die();
 
//do some simple validation to check if sq contains a string
$sqa = $_POST['sqa'];
$sqb = $_POST['sqb'];  

$searchresult = '';
if (true) {
    //prepare a query and send it to the server using our search string as a wildcard on roomID
    $query = "SELECT roomID,roomname,roomtype,beds FROM room WHERE roomID NOT IN (SELECT roomID  FROM booking WHERE checkinDate >= '$sqa' AND checkoutDate <='$sqb')";
    $result = mysqli_query($DBC,$query);
    $rowcount = mysqli_num_rows($result); 
    //makes sure we have bookings
    if ($rowcount > 0) {  
        $rows=[]; //start an empty array      
        //append each row in the query result to our empty array until there are no more results                    
        while ($row = mysqli_fetch_assoc($result)) {            
            $rows[] = $row; 
        }
        
        //take the array of our 1 or more bookings and turn it into a JSON text
        $searchresult = json_encode($rows);
        //this line is cruicial for the browser to understand what data is being sent
        header('Content-Type: text/json; charset=utf-8');
    } else echo "<tr><td colspan=3><h5>No Bookings found!</h5></td></tr>";
} else echo "<tr><td colspan=3> <h6>Invalid search query</h6>";
mysqli_free_result($result); //free any memory used by the query
mysqli_close($DBC); //close the connection once done

echo  $searchresult;