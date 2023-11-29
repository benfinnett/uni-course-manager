<?php 
    require './include/database.php';

    // Find previous page to create working back button across the site. Defaults to homepage if cannot be found
    if (strpos($_SERVER['HTTP_REFERER'], "modules.php") !== false) {
        $return_url = "modules.php";
    } else {
        $return_url = "index.php";
    }

    // Get student details
    $moduleid = $_GET['id'];
    $db = connect_to_db();
    
    $query = "SELECT *\n"
    . "FROM modules\n"
    . "WHERE moduleid='$moduleid'";

    $stmt = get_statement($db, $query);
    $module = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<html lang="en">
    <head>
        <title>Module: <?php echo $module["moduleid"]?></title>
        <link rel="icon" type="image/x-icon" href="./assets/favicon.ico">
    </head>
    
    <body>
    <h1><u>Module Information</u></h1>
        <!--Back to menu button-->
        <form action='<?php echo $return_url?>' method='GET' style='display:inline;'>
            <button type='submit' id='back'>Return</button>
        </form>
        <hr>
        
        <h2><u><?php echo $module["name"]?></u></h3>
        <p>Details about this module are the following:</p>
        
        <!--Module ID-->
        <h3>Module ID:</h3>
        <?php echo $module["moduleid"]?>

        <!--Duration-->
        <h3>Weekly Hours:</h3>
        <?php echo $module["duration"]?> teaching hours per week
 
        <!--Courses-->
        <h3>Courses containing this module:</h3>
        <?php
            $query = "SELECT courses.courseid, courses.name FROM courses\n"
            . "INNER JOIN b_course_module ON b_course_module.courseid = courses.courseid\n"
            . "WHERE b_course_module.moduleid = '$moduleid'";
        
            $stmt = get_statement($db, $query);
            $i = 0;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $i++;
                echo "<a href='course.php?id=" . $row["courseid"] . "'>";
                echo $row["name"] . "</a> (" . $row["courseid"];
                echo ")<br>";
            }
            if ($i === 0) {
                echo "There are no courses containing this module!";
            }
        ?>

        <!--Tutors-->
        <h3>Tutors teaching this module:</h3>
        <?php
            $query = "SELECT staff.staffid, staff.firstname, staff.lastname FROM staff\n"
            . "INNER JOIN b_tutor_module ON b_tutor_module.staffid = staff.staffid\n"
            . "WHERE b_tutor_module.moduleid = '$moduleid'";
        
            $stmt = get_statement($db, $query);
            $i = 0;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $i++;
                echo "<a href='staffmember.php?id=" . $row["staffid"] . "'>";
                echo $row["firstname"] . " " . $row["lastname"] . "</a> (" . $row["staffid"];
                echo ")<br>";
            }
            if ($i === 0) {
                echo "There are no tutors teaching this module!";
            }
        ?>

    <hr>
    <form onsubmit="return confirm('Do you really want to delete this module?');" action='actions/deletemodule.php' method='POST'>
        <?php echo "<input type='hidden' name='moduleid' id='moduleid' value=$moduleid />";?>
        <button type="submit" id="DelModule">Delete this module</button>
    </form>
    </body>
</html>