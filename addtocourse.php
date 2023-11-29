<?php require './include/database.php'?>

<html lang="en">
    <head>
        <title>Add Student to Course</title>
        <link rel="icon" type="image/x-icon" href="./assets/favicon.ico">
    </head>
    
    <body>
        <script>
            function get_checked(total_boxes) {
                checked = document.querySelectorAll('input[type="checkbox"]:checked').length
                if (total_boxes < 15) {
                    document.getElementById("complete").disabled = false;
                } else if (checked < 15 && total_boxes >= 15) { 
                    // Disable search button
                    document.getElementById("complete").disabled = true;
                    var error = document.getElementById("error");
                    error.textContent = " Student must be signed up to at least 15 modules!";
                    error.style.color = "red";
                } else {
                    // When the box has text made up of allowed chars, enable search button and hide error message
                    document.getElementById("complete").disabled = false;
                    var error = document.getElementById("error");
                    error.textContent = "";
                }
            }
        </script>
        <h1><u>Add Student to Course</u></h1>
        <h2>Course: <?php 
            $courseid = $_GET['course'];
            $query = "SELECT courseid, name FROM courses WHERE courseid = $courseid";
            $db = connect_to_db();
            $stmt = get_statement($db, $query);
            echo $stmt->fetch(PDO::FETCH_ASSOC)["name"];
            echo " ($courseid)"
        ?></h2>
        <!--Back to menu button-->
        <form action='students.php' style='display:inline;'>
            <button type='submit' id='back'>Cancel</button>
        </form>
        <hr>
        <h2>Select modules</h2>
        <?php
            $studentid = $_GET["studentid"];
            $query = "SELECT modules.moduleid, modules.name, b_course_module.courseid\n"
            . "FROM modules\n"
            . "INNER JOIN b_course_module ON b_course_module.moduleid = modules.moduleid\n"
            . "WHERE b_course_module.courseid = $courseid";
            $stmt = get_statement($db, $query);
            $cquery = "SELECT count(modules.moduleid)\n"
            . "FROM modules INNER JOIN b_course_module ON b_course_module.moduleid = modules.moduleid\n"
            . "WHERE b_course_module.courseid = $courseid;";
            $num_of_modules = count_rows($cquery);
            echo "<form action='actions/completecourse.php' method='POST'>";
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    $moduleid = $row["moduleid"];
                    $modulename = $row["name"];
                    echo "<div>";
                    if ($num_of_modules < 15) {
                        echo "<input type='checkbox' id=$moduleid name=$moduleid value=$moduleid checked disabled>";
                    } else {
                        echo "<input type='checkbox' onclick='get_checked($num_of_modules);' id=$moduleid name=$moduleid value=$moduleid checked>";
                    }
                    echo "<label for=$moduleid>$modulename</label>";
                    echo "</div>";
                }
                echo "<br>";
                echo "<input type='hidden' name='studentid' id='studentid' value=$studentid />";
                echo "<input type='hidden' name='courseid' id='courseid' value=$courseid />";
                echo "<button type='submit' id='complete' disabled>Complete</button>";
                echo "<span id='error'></span>";
                echo "<script type='text/javascript'>get_checked($num_of_modules);</script>";
            echo "</form>";
        ?>
    </body>
</html>