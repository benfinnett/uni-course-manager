<?php 
    require './include/database.php';

    // Find previous page to create working back button across the site. Defaults to homepage if cannot be found
    if (strpos($_SERVER['HTTP_REFERER'], "students.php") !== false) {
        $return_url = "students.php";
    } else {
        $return_url = "index.php";
    }

    // Get student details
    $studentid = $_GET['id'];
    $db = connect_to_db();
    
    $query = "SELECT *\n"
    . "FROM students\n"
    . "WHERE studentid=$studentid";

    $stmt = get_statement($db, $query);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<html lang="en">
    <head>
        <title>Student: <?php echo $student["firstname"]?></title>
        <link rel="icon" type="image/x-icon" href="./assets/favicon.ico">
    </head>
    
    <body>
        <h1><u>Student Information</u></h1>
        <!--Back to menu button-->
        <form action='<?php echo $return_url?>' method='GET' style='display:inline;'>
            <button type='submit' id='back'>Return</button>
        </form>
        <hr>
        
        <h2><u><?php echo $student["firstname"] . " " . $student["lastname"]?></u></h3>
        <p>Details about <?php echo $student["firstname"]?> are the following:</p>
        
        <!--Student ID-->
        <h3>Student ID:</h3>
        <?php echo $student["studentid"]?>

        <!--Gender-->
        <h3>Gender:</h3>
        <?php 
            switch ($student["gender"]) {
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
        <a href="mailto: <?php echo $student["email"]?>"><?php echo $student["email"]?></a>

        <!--DOB-->
        <h3>Date of Birth:</h3>
        <?php
            $date = new DateTime($student["dob"]);
            echo $date->format("d/m/Y");
        ?>
        
        <!--Course-->
        <h3>Course:</h3>
        <?php
            $courseid = $student["courseid"];
            if (is_null($courseid)) {
                echo "<div id='CourseButton' style='display:inline;'>";
                    echo "This student is not on a course! ";
                    echo "<button onclick='showAddToCourse()'>Add student to course</button>";
                echo "</div>";
                echo "<div id='AddToCourse' style='display:none;'>";
                    echo "<form action='addtocourse.php' method='GET' style='display:inline;'>";
                        $query = "SELECT courseid, name\n"
                        . "FROM courses\n"
                        . "ORDER BY name";
                        $stmt = get_statement($db, $query); // Get query result
                        echo "<select name='course' id='course'>";
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $cid = $row["courseid"];
                            echo "<option value=$cid>";
                            echo $row["name"] . "</option>";
                        }
                        echo "</select>";
                        echo "<input type='hidden' name='studentid' id='studentid' value=$studentid />";
                        echo "<button type='submit' id='add'>Add</button>";
                        echo "<button type='reset' onclick='showAddToCourse()'>Cancel</button>";
                    echo "</form></div>";
            } else {
                $query = "SELECT students.courseid, courses.name FROM students\n"
                . "INNER JOIN courses ON courses.courseid = students.courseid\n"
                . "WHERE students.studentid = $studentid";
                $stmt = get_statement($db, $query);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo $result["name"] . " (";
                echo $result["courseid"] . ")";
                echo "<h3>Modules:</h3>";
                $query = "SELECT modules.moduleid, modules.name FROM modules\n"
                . "INNER JOIN b_student_module ON b_student_module.moduleid = modules.moduleid\n"
                . "WHERE b_student_module.studentid = $studentid";
                $stmt = get_statement($db, $query);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<a href='module.php?id=" . $row["moduleid"] . "'>";
                    echo $row["name"] . "</a> (" . $row["moduleid"] . ")";
                    echo "<br>";
                }
                
            }
        ?>
    <hr>
    <form onsubmit="return confirm('Do you really want to delete this student?');" action='actions/deletestudent.php' method='POST'>
        <?php echo "<input type='hidden' name='studentid' id='studentid' value=$studentid />";?>
        <button type="submit" id="DelStudent">Delete this student</button>
    </form>


    <script type="text/javascript">
        function showAddToCourse() {
            content_div = document.getElementById("AddToCourse");
            if (content_div.style.display === "none") {
                content_div.style.display = "block";
            } else {
                content_div.style.display = "none";
            }
            var button_div = document.getElementById("CourseButton");
            if (button_div.style.display === "none") {
                button_div.style.display = "block";
            } else {
                button_div.style.display = "none";
            }
        }
    </script>
    </body>
</html>