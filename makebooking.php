<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make a booking</title>

    <!-- Jquery Datepicker -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <!-- <script>
    $( function() {
      $( "#checkinDate" ).datepicker({numberOfMonths:2});
      $( "#checkoutDate" ).datepicker({numberOfMonths:2});
    } );
    </script> -->

    <script>
        //insert datepicker jQuery

        $(document).ready(function() {
            $.datepicker.setDefaults({
                dateFormat: 'yy-mm-dd'
            });
            $(function() {
                checkinDate = $("#checkinDate").datepicker()
                checkoutDate = $("#checkoutDate").datepicker()

                function getDate(element) {
                    var date;
                    try {
                        date = $.datepicker.parseDate(dateFormat, element.value);
                    } catch (error) {
                        date = null;
                    }
                    return date;
                }
            });
        });
    </script>


   
</head>

<body>

    <?php

    // include "checksession.php";

    //take the details about server and database
    include "config.php"; //load in any variables
    $DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

    $searchresult = '';
    echo "<pre>";

    var_dump($_POST);
    var_dump($_GET);

    echo "</pre>";
    //insert DB code from here onwards
    //check if the connection was good
    if (mysqli_connect_errno()) {
        echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
        exit; //stop processing the page further
    }


    //function to clean input but not validate type and content
    function cleanInput($data)
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }




    //on submit check if empty or not string and is submited by POST
    if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Add')) {

        $room = cleanInput($_POST['room']);
        //     $customerID = $_POST['customerID'];

        $customer = $_POST['customers'];
        $checkinDate = $_POST['checkinDate'];
        $checkoutDate = $_POST['checkoutDate'];
        $contactnumber = cleanInput($_POST['contactnumber']);
        $extras = cleanInput($_POST['extras']);
        // $review = cleanInput($_POST['review']);

        $error = 0; //clear our error flag
        $msg = 'Error: ';
        $in = new DateTime($checkinDate);
        $out = new DateTime($checkoutDate);

        if ($in >= $out) {
            $error++;
            $msg .= "Checkin date cannot be earlier or equal to Checkout date";
            $checkoutDate = '';
        }

        if ($error == 0) {
            //save the booking data if the error flag is still clean
            //   $query = "INSERT INTO `ticket` (flightcode, customerID, 
            //   departureDate,
            //   arrivalDate,price,seat_options) VALUES (?,?,?,?,?,?)";

            $query = "INSERT INTO `booking` (customerID, roomID, 
      checkinDate, checkoutDate,contactNumber,extras) VALUES (?,?,?,?,?,?)";

            $stmt = mysqli_prepare($DBC, $query); //prepare the query
            mysqli_stmt_bind_param($stmt, 'iissss' ,$customer, $room,$checkinDate, $checkoutDate, $contactnumber, $extras);

            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            //print message
            echo "<h5>Booking added successfully</h5>";
        } else {
            //print error 
            echo "<h5>$msg</h5>" . PHP_EOL;
        }
    }


    $query1 = 'SELECT customerID, firstname, lastname, email FROM customer ORDER BY customerID';
    $result1 = mysqli_query($DBC, $query1);
    $rowcount1 = mysqli_num_rows($result1);


    $query = 'SELECT roomID, roomname, description, roomtype,beds FROM room ORDER BY roomID';
    $result = mysqli_query($DBC, $query);
    $rowcount = mysqli_num_rows($result);

    ?>

    <h1>Make a booking</h1>
    <h2>
        <a href="current-bookings.php">[Return to Booking Listing]</a>
        <a href="index.php">[Return to Main Page]</a>
    </h2>
    <h3>Booking for test</h3>

    <form method="post">
        <p>
            <label for="roomname">Room (name, types, beds):</label>
            <select name="room" id="room">
                        <?php
                        if ($rowcount > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $id = $row['roomID']; ?>

                                <option value="<?php echo $row['roomID']; ?>">
                                    <?php echo $row['roomname'] . ' '
                                      
                                        . $row['roomtype'] . ' '
                                        . $row['beds'] ?>
                                </option>
                        <?php }
                        } else echo "<option>No rooms found</option>";
                        mysqli_free_result($result);
                        ?>
                    </select>
        </p>
        <div>
                     
                     <input type="hidden" name="customers" id="customers"  value="<?php echo $id;?>"> 
                
            
                </div>
        <p>Checkin date: <input type="text" id="checkinDate" name="checkinDate" placeholder="yyyy-mm-dd" required></p>
        <p>Checkout date: <input type="text" id="checkoutDate" name="checkoutDate" placeholder="yyyy-mm-dd" required></p>
        <p>
            <labe for="contactnumber">Contact number:</label>
                <input type="text" placeholder="(XXX) XXX XXXX" pattern="\(\d{3}\) \d{3} \d{4}" id="contactnumber" name="contactnumber" required>
        </p>
        <p>
            <labe for="extras">Booking extras:</label>
                <textarea id="extras" name="extras" rows="4" cols="25" maxlength="250"></textarea>
        </p>
       
        <input type="submit" name="submit" value="Add">
        <a href="index.php">[Cancel]</a>
    </form>
    <br>
    <hr>
    <div class="container">
        <h3>Search for room availability</h3>
        <p>
        <form id="searchForm" method="post" name="searching">
 
            <label for="">From Date:</label> 
            <input type="text" id="fromDate" name="sqa" required >
            <label for="">To Date:</label> 
            <input type="text" id="toDate" name="sqb" required >
            <input type="submit" name="search" id="search" value="Search availability">
      
        </form>
        </p>
        <script>
        $(document).ready(function() {
            $('#fromDate').datepicker();
            $('#toDate').datepicker();
            
            $('#searchForm').submit(function(event) {
                var formData = {
                    sqa: $('#fromDate').val(),
                    sqb: $('#toDate').val()
                };
                $.ajax({
                    type: "POST",
                    url: "bookingsearch.php",
                    data: formData,
                    dataType: "json",
                    encode: true,

                }).done(function(data) {
                    var tbl = document.getElementById("tblbookings"); //find the table in the HTML  
                    var rowCount = tbl.rows.length;

                    for (var i = 1; i < rowCount; i++) {
                        
                        tbl.deleteRow(1);
                    }

                 

                    for (var i = 0; i < data.length; i++) {
                        var rid = data[i]['roomID'];
                        var rn = data[i]['roomname'];
                        var rt = data[i]['roomtype'];
                        var bd = data[i]['beds'];
                     
                        tr = tbl.insertRow(-1);
                        var tabCell = tr.insertCell(-1);
                        tabCell.innerHTML = rid; //roomID
                        var tabCell = tr.insertCell(-1);
                        tabCell.innerHTML = rn; //room name  
                        var tabCell = tr.insertCell(-1);
                        tabCell.innerHTML = rt; //room type       
                        var tabCell = tr.insertCell(-1);
                        tabCell.innerHTML = bd; //beds          
                    }
                });
                event.preventDefault();
            })
        })
    </script>
    <div class="row">
        <table id="tblbookings" border="1">
            <thead>
                <tr>
                    <th>Room#</th>
                    <th>Room name</th>
                    <th>Room Type</th>
                    <th>Beds</th>
                </tr>
            </thead>
        </table>
    </div>
    </div>

    <?php
    mysqli_close($DBC); //close the connection once done  // Displaying Selected Value
    ?>
</body>

</html>