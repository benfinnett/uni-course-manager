<?php 
    require './include/database.php';

    // Find previous page to create working back button across the site. Defaults to homepage if cannot be found
    if (strpos($_SERVER['HTTP_REFERER'], "staff.php") !== false) {
        $return_url = "staff.php";
    } else {
        $return_url = "index.php";
    }

    // Get staff details
    $staffid = $_GET['id'];
    $db = connect_to_db();
    
    $query = "SELECT *\n"
    . "FROM staff\n"
    . "WHERE staffid=$staffid";

    $stmt = get_statement($db, $query);
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<html lang="en">
    <head>
        <title>Staff: <?php echo $staff["firstname"]?></title>
        <link rel="icon" type="image/x-icon" href="./assets/favicon.ico">
    </head>
    
    <body>
        <h1><u>Staff Information</u></h1>
        <!--Back to menu button-->
        <form action='<?php echo $return_url?>' method='GET' style='display:inline;'>
            <button type='submit' id='back'>Return</button>
        </form>
        <hr>
        
        <h2><u><?php echo $staff["firstname"] . " " . $staff["lastname"]?></u></h3>
        <p>Details about <?php echo $staff["firstname"]?> are the following:</p>
        
        <!--Staff ID-->
        <h3>Staff ID:</h3>
        <?php echo $staff["staffid"]?>

        <!--Gender-->
        <h3>Gender:</h3>
        <?php 
            switch ($staff["gender"]) {
                case "M":
                    echo "Male";
                    break;
                case "F":
                    echo "Female";
                    break;
            // Using switch here allows easy implementation of non-binary genders
            }
        ?>

        <!--Email-->
        <h3>Email:</h3>
        <a href="mailto: <?php echo $staff["email"]?>"><?php echo $staff["email"]?></a>

        <!--DOB-->
        <h3>Date of Birth:</h3>
        <?php
            $date = new DateTime($staff["dob"]);
            echo $date->format("d/m/Y");
        ?>
        
        <!--Staff Type-->
        <h3>Role:</h3>
        <?php 
            $stafftype = $staff["stafftype"]; 
            switch ($stafftype) {
                case 0:
                    echo "Professional Services";
                    break;
                case 1:
                    echo "Tutor";
                    break;
            } 
        ?>        
                 
    <hr>
    <form onsubmit="return confirm('Do you really want to delete this staff member?');" action='actions/deletestaff.php' method='POST'>
        <?php echo "<input type='hidden' name='staffid' id='staffid' value=$staffid />";?>
        <?php echo "<input type='hidden' name='stafftype' id='stafftype' value=$stafftype />";?>
        <button type="submit" id="DelStaff">Delete this staff member</button>
    </form>
    </body>
</html>