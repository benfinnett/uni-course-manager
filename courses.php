<?php require './include/database.php'?>

<html lang="en">
    <head>
        <!--Title and favicon-->
        <title>Courses</title>
        <link rel="icon" type="image/x-icon" href="./assets/favicon.ico">
    </head>

    <body>
        <h1><u>Courses</u></h1>
        <!--Back to menu button-->
        <form action="index.php" style="display:inline;">
            <button type="submit" id="back">Back to Menu</button>
        </form>
        <!--Search box-->
        <form action="courses.php" method="get" style="display:inline;">
            <input type="text" id="q" name="q" placeholder="Search courses..." onkeyup="checkForm('q', 'UserSearch')">
            <button type="submit" id="UserSearch" disabled>Search</button>
        </form>
        <!--Clear search button-->
        <?php 
            $q = $_GET["q"];
            // Only show clear search button if query is active
            if (isset($q)) {
                echo "<form action='courses.php' style='display:inline;'>";
                echo "<button type='submit' id='clear' onclick='clearSession()'>Clear search</button>";
                echo "</form>";
            }
        ?>
        <span id="error"></span>
        <hr>

        <!--Courses results-->
        <h2><u>All Courses</u></h2>

        <!--Add course-->
        <div id="NewCourseButton">
            <button onclick="showCourseEntry()">Add New Course</button>
        </div>
        <!--Form content (hidden by default)-->
        <div id="NewCourse" style="display:none;">
            <!--Using POST method to hide passed variables rather than GET which places them in the URL-->
            <form action="actions/addcourse.php" method="post">
                <input type="text" id="name" name="name" placeholder="Course name" onkeyup="checkAddForm()">
                <label for="duration">Course length:</label>
                <input type="number" id="duration" name="duration" placeholder="Years" min="1" max="5" onchange="checkAddForm()">
                <label for="department">Department:</label>
                <?php 
                    $query = "SELECT departmentid, name\n"
                    . "FROM departments\n"
                    . "ORDER BY name";
                    $db = connect_to_db();
                    $stmt = get_statement($db, $query); // Get query result
                    echo "<select name='department' id='department'>";
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $cid = $row["departmentid"];
                        echo "<option value=$cid>";
                        echo $row["name"] . "</option>";
                    }
                    echo "</select>";
                ?>

                <button type="submit" id="AddCourse" onclick="showCourseEntry()" disabled>Add course</button>
                <button type="reset" onclick="showCourseEntry()">Cancel</button>
            </form>
            <span id="AddError"></span>
        </div>
        <br>
        <table border="1">
            <!--Table Caption-->
            <?php 
                // Get preliminary data
                $page = $_GET["page"];
                if (isset($q)) {
                    $query = "SELECT COUNT(*)\n"
                    . "FROM courses\n"
                    . "WHERE name LIKE '%$q%'";
                    $rows = count_rows($query); // Number of rows matching query

                    echo "<caption>$rows courses match the query \"$q\"</caption>";
                } else {
                    $rows = count_rows("SELECT COUNT(*) FROM courses");
                    echo "<caption>Showing $rows courses</caption>";
                }
            ?>
            <tr>
                <th>Row</th>
                <th>Course Name</th>
                <th>Course ID</th>
            </tr>
            <!--PHP-->
            <?php 
                $totalpages = ceil($rows / 25); // Total pages needed showing 25 rows per page
                // If there is more than 1 page
                if ($totalpages > 1) {
                    // If querystring page value exists and is a number 
                    if (isset($page) && is_numeric($page)) {
                        // Set $page to integer
                        $page = (int) $page;
                    } else {
                        // Otherwise default page value to 1
                        $page = 1;
                    }
                    if ($page > $totalpages) {
                        // Don't allow page number to go higher than the maximum page
                        $page = $totalpages;
                    } else if ($page < 1) {
                        // Don't allow page number to be less than 1
                        $page = 1;
                    }
                } else {
                    // If there is only 1 page, set $page to 1
                    $page = 1;
                }
                
                $offset = 25 * ($page - 1); // 25 rows per page
                if (isset($q)) {
                    $query = "SELECT courseid, name\n"
                    . "FROM courses\n"
                    . "WHERE name LIKE '%$q%'\n"
                    . "ORDER BY name\n"
                    . "LIMIT $offset, 25"; // We only care about the relevant 25 rows corresponding to the page number
                } else {
                    $query = "SELECT courseid, name\n"
                    . "FROM courses\n"
                    . "ORDER BY name\n"
                    . "LIMIT $offset, 25";
                }
                $stmt = get_statement(connect_to_db(), $query); // Get query result
                
                // iterate through each row of the statement
                $i = 0; 
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    $i += 1;
                    // Add row to the table
                    echo "<tr>";
                        echo "<td>" . $i+$offset . "</td>";
                        $href = "<a href='course.php?id=" . $row["courseid"] . "'>";
                        echo "<td>$href" . $row["name"] . "</a></td>";
                        echo "<td>" . $row["courseid"] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
                echo "<p>Page $page of $totalpages</p>";
                // If there is more than one page, show pagination buttons
                if ($totalpages > 1) {
                    // First page, don't need previous buttons
                    if ($page == 1) {
                        echo "<button type='button' id='allprev' style='display:inline;' disabled><<</button>";
                        echo "<button type='button' id='prev' style='display:inline;' disabled>< Prev Page</button>";
                    // otherwise, enable previous buttons
                    } else {
                        echo "<form action='courses.php' method='get' style='display:inline;'>";
                        echo "<button type='submit' id='allprev'><<</button>";
                        if (isset($q)) {echo "<input type='hidden' name='q' id='q' value=$q />";}
                        echo "<input type='hidden' name='page' id='page' value='1' />";
                        echo "</form>";
                        $page -= 1;
                        echo "<form action='courses.php' method='get' style='display:inline;'>";
                        echo "<button type='submit' id='prev'>< Prev Page</button>";
                        if (isset($q)) {echo "<input type='hidden' name='q' id='q' value=$q />";}
                        echo "<input type='hidden' name='page' id='page' value=$page />";
                        echo "</form>";
                        $page += 1;
                    }
                    // Last page, don't need next buttons
                    if ($page == $totalpages) { 
                        echo "<button type='button' id='next' style='display:inline;' disabled>Next Page ></button>";
                        echo "<button type='button' id='allnext' style='display:inline;' disabled>>></button>";
                    // otherwise, enable next buttons
                    } else {
                        $page += 1;
                        echo "<form action='courses.php' method='get' style='display:inline;'>";
                        echo "<button type='submit' id='next'>Next Page ></button>";
                        if (isset($q)) {echo "<input type='hidden' name='q' id='q' value=$q />";}
                        echo "<input type='hidden' name='page' id='page' value=$page />";
                        echo "</form>";
                        $page -= 1;
                        echo "<form action='courses.php' method='get' style='display:inline;'>";
                        echo "<button type='submit' id='allnext'>>></button>";
                        if (isset($q)) {echo "<input type='hidden' name='q' id='q' value=$q />";}
                        echo "<input type='hidden' name='page' id='page' value=$totalpages />";
                        echo "</form>";
                    }
                }
            ?>
    </body>
    <!--JS validate search query and clearing session data-->
    <script type="text/javascript" src="./include/func.js"></script>
    <!--Toggle showing new course entry details-->
    <script type="text/javascript">
        function showCourseEntry() {
            var content_div = document.getElementById("NewCourse")
            if (content_div.style.display === "none") {
                content_div.style.display = "block";
            } else {
                content_div.style.display = "none";
            }
            var button_div = document.getElementById("NewCourseButton")
            if (button_div.style.display === "none") {
                button_div.style.display = "block";
            } else {
                button_div.style.display = "none";
            }
        }

        function checkAddForm() {
            allowed_chars = /^[A-Za-z \d\-,\/:&.]+$/;
            name = document.getElementById("name").value;
            duration = parseInt(document.getElementById("duration").value);
            error = document.getElementById("AddError");
            error.style.color = "red";
            if (name.match(allowed_chars) && (duration <= 5 && duration >= 1)) {
                document.getElementById("AddCourse").disabled = false; 
                error.textContent = "";
            } else if (!name.match(allowed_chars) && name !== "") {
                document.getElementById("AddCourse").disabled = true;
                error.textContent = "Course name must only contain any of A-z, 0-9, -\/:&. or spaces!";
            } else if (duration > 5 || duration < 1) {
                document.getElementById("AddCourse").disabled = true;
                error.textContent = "Years must be a number between 1 and 5!";
            } else {
                document.getElementById("AddCourse").disabled = true;
                error.textContent = "";
            }
        }
    </script>
</html>