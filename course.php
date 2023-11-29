<?php 
    require './include/database.php';

    // Find previous page to create working back button across the site. Defaults to homepage if cannot be found
    if (strpos($_SERVER['HTTP_REFERER'], "courses.php") !== false) {
        $return_url = "courses.php";
    } else {
        $return_url = "index.php";
    }

    // Get student details
    $courseid = $_GET['id'];
    $db = connect_to_db();
    
    $query = "SELECT *\n"
    . "FROM courses\n"
    . "WHERE courseid='$courseid'";

    $stmt = get_statement($db, $query);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<html lang="en">
    <head>
        <title>Course: <?php echo $course["courseid"]?></title>
        <link rel="icon" type="image/x-icon" href="./assets/favicon.ico">
    </head>
    
    <body>
    <h1><u>Course Information</u></h1>
        <!--Back to menu button-->
        <form action='<?php echo $return_url?>' method='GET' style='display:inline;'>
            <button type='submit' id='back'>Return</button>
        </form>
        <hr>
        
        <h2><u><?php echo $course["name"]?></u></h3>
        <p>Details about this course are the following:</p>
        
        <!--Course ID-->
        <h3>Course ID:</h3>
        <?php echo $course["courseid"]?>

        <!--Duration-->
        <h3>Years to complete course:</h3>
        <?php echo $course["duration"]?> years

        <!--Department-->
        <h3>Department</h3>
        <?php 
            $department = $course["departmentid"];
            $query = "SELECT departmentid, name FROM departments\n"
            . "WHERE departmentid = $department";
            $stmt = get_statement($db, $query);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo $row["name"];
            }
        ?>
        
        <!--Courses-->
        <h3>Modules on this course:</h3>
        <?php
            $query = "SELECT modules.moduleid, modules.name FROM modules\n"
            . "INNER JOIN b_course_module ON b_course_module.moduleid = modules.moduleid\n"
            . "WHERE b_course_module.courseid = '$courseid'";
        
            $stmt = get_statement($db, $query);
            $i = 0;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $i++;
                echo "<a href='module.php?id=" . $row["moduleid"] . "'>";
                echo $row["name"] . "</a> (" . $row["moduleid"];
                echo ")<br>";
            }
            if ($i === 0) {
                echo "There are no modules on this course!";
            }
        ?>


    <hr>
    <form onsubmit="return confirm('Do you really want to delete this course?');" action='actions/deletecourse.php' method='POST'>
        <?php echo "<input type='hidden' name='courseid' id='courseid' value=$courseid />";?>
        <button type="submit" id="DelCourse">Delete this course</button>
    </form>
    </body>
</html>