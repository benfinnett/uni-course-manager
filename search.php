<?php require './include/database.php'?>

<html lang="en">
    <head>
        <!--Title and favicon-->
        <title>Search</title>
        <link rel="icon" type="image/x-icon" href="./assets/favicon.ico">
    </head>

    <body>
        <h1><u>Search Results</u></h1>
        <!--Back to menu button-->
        <form action="index.php" style="display:inline;">
            <button type="submit" id="back">Back to Menu</button>
        </form>
        <!--Search box-->
        <form action="search.php" method="get" style="display:inline;">
            <input type="text" id="q" name="q" placeholder="Search again..." onkeyup="checkForm('q', 'UserSearch')">
            <button type="submit" id="UserSearch" disabled>Search</button>
        </form>
        <span id="error"></span>
        <hr>

        <!--Students results-->
        <h2><u>Students</u></h2>
        <table border="1">
            <!--Table Caption-->
            <?php 
                // Get preliminary data
                $q = $_GET["q"];
                $page = $_GET["page"];
                $query = "SELECT COUNT(*)\n"
                . "FROM students\n"
                . "WHERE CONCAT(firstname, ' ', lastname) LIKE '%$q%'";
                $rows = count_rows($query); // Number of rows matching query

                echo "<caption>$rows students match the query \"$q\"</caption>";
            ?>
            <tr>
                <th>Row</th>
                <th>Student Name</th>
                <th>Student ID</th>
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
                $query = "SELECT studentid, firstname, lastname\n"
                . "FROM students\n"
                . "WHERE CONCAT(firstname, ' ', lastname) LIKE '%$q%'\n"
                . "ORDER BY lastname\n"
                . "LIMIT $offset, 25"; // We only care about the relevant 25 rows corresponding to the page number
                $stmt = get_statement(connect_to_db(), $query); // Get query result
                
                // iterate through each row of the statement
                $i = 0; 
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    $i += 1;
                    // Add row to the table
                    echo "<tr>";
                        echo "<td>" . $i+$offset . "</td>";
                        $href = "<a href='student.php?id=" . $row["studentid"] . "'>";
                        echo "<td>$href" . $row["lastname"] . ", " . $row["firstname"] . "</a></td>";
                        echo "<td>" . $row["studentid"] . "</td>";
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
                        echo "<form action='search.php' method='get' style='display:inline;'>";
                        echo "<button type='submit' id='allprev'><<</button>";
                        echo "<input type='hidden' name='q' id='q' value=$q />";
                        echo "<input type='hidden' name='page' id='page' value='1' />";
                        echo "</form>";
                        $page -= 1;
                        echo "<form action='search.php' method='get' style='display:inline;'>";
                        echo "<button type='submit' id='prev'>< Prev Page</button>";
                        echo "<input type='hidden' name='q' id='q' value=$q />";
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
                        echo "<form action='search.php' method='get' style='display:inline;'>";
                        echo "<button type='submit' id='next'>Next Page ></button>";
                        echo "<input type='hidden' name='q' id='q' value=$q />";
                        echo "<input type='hidden' name='page' id='page' value=$page />";
                        echo "</form>";
                        $page -= 1;
                        echo "<form action='search.php' method='get' style='display:inline;'>";
                        echo "<button type='submit' id='allnext'>>></button>";
                        echo "<input type='hidden' name='q' id='q' value=$q />";
                        echo "<input type='hidden' name='page' id='page' value=$totalpages />";
                        echo "</form>";
                    }
                }
            ?>
        <hr>
        <!--Staff results-->
        <h2><u>Staff</u></h2>
        <table border="1">
            <!--Table Caption-->
            <?php 
                // Get preliminary data
                $q = $_GET["q"];
                $page = $_GET["page"];
                $query = "SELECT COUNT(*)\n"
                . "FROM staff\n"
                . "WHERE CONCAT(firstname, ' ', lastname) LIKE '%$q%'";
                $rows = count_rows($query); // Number of rows matching query

                echo "<caption>$rows staff match the query \"$q\"</caption>";
            ?>
            <tr>
                <th>Row</th>
                <th>Staff Name</th>
                <th>Staff ID</th>
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
                $query = "SELECT staffid, firstname, lastname\n"
                . "FROM staff\n"
                . "WHERE CONCAT(firstname, ' ', lastname) LIKE '%$q%'\n"
                . "ORDER BY lastname\n"
                . "LIMIT $offset, 25"; // We only care about the relevant 25 rows corresponding to the page number
                $stmt = get_statement(connect_to_db(), $query); // Get query result
                
                // iterate through each row of the statement
                $i = 0; 
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    $i += 1;
                    // Add row to the table
                    echo "<tr>";
                        echo "<td>" . $i+$offset . "</td>";
                        $href = "<a href='staff.php?id=" . $row["staffid"] . "'>";
                        echo "<td>$href" . $row["lastname"] . ", " . $row["firstname"] . "</a></td>";
                        echo "<td>" . $row["staffid"] . "</td>";
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
                        echo "<form action='search.php' method='get' style='display:inline;'>";
                        echo "<button type='submit' id='allprev'><<</button>";
                        echo "<input type='hidden' name='q' id='q' value=$q />";
                        echo "<input type='hidden' name='page' id='page' value='1' />";
                        echo "</form>";
                        $page -= 1;
                        echo "<form action='search.php' method='get' style='display:inline;'>";
                        echo "<button type='submit' id='prev'>< Prev Page</button>";
                        echo "<input type='hidden' name='q' id='q' value=$q />";
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
                        echo "<form action='search.php' method='get' style='display:inline;'>";
                        echo "<button type='submit' id='next'>Next Page ></button>";
                        echo "<input type='hidden' name='q' id='q' value=$q />";
                        echo "<input type='hidden' name='page' id='page' value=$page />";
                        echo "</form>";
                        $page -= 1;
                        echo "<form action='search.php' method='get' style='display:inline;'>";
                        echo "<button type='submit' id='allnext'>>></button>";
                        echo "<input type='hidden' name='q' id='q' value=$q />";
                        echo "<input type='hidden' name='page' id='page' value=$totalpages />";
                        echo "</form>";
                    }
                }
            ?>
            <hr>
        <!--Modules results-->
        <h2><u>Modules</u></h2>
        <table border="1">
            <!--Table Caption-->
            <?php 
                // Get preliminary data
                $q = $_GET["q"];
                $page = $_GET["page"];
                $query = "SELECT COUNT(*)\n"
                . "FROM modules\n"
                . "WHERE name LIKE '%$q%'";
                $rows = count_rows($query); // Number of rows matching query

                echo "<caption>$rows modules match the query \"$q\"</caption>";
            ?>
            <tr>
                <th>Row</th>
                <th>Module Name</th>
                <th>Module ID</th>
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
                $query = "SELECT moduleid, name\n"
                . "FROM modules\n"
                . "WHERE name LIKE '%$q%'\n"
                . "ORDER BY name\n"
                . "LIMIT $offset, 25"; // We only care about the relevant 25 rows corresponding to the page number
                $stmt = get_statement(connect_to_db(), $query); // Get query result
                
                // iterate through each row of the statement
                $i = 0; 
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    $i += 1;
                    // Add row to the table
                    echo "<tr>";
                        echo "<td>" . $i+$offset . "</td>";
                        $href = "<a href='module.php?id=" . $row["moduleid"] . "'>";
                        echo "<td>$href" . $row["name"] . "</a></td>";
                        echo "<td>" . $row["moduleid"] . "</td>";
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
                        echo "<form action='search.php' method='get' style='display:inline;'>";
                        echo "<button type='submit' id='allprev'><<</button>";
                        echo "<input type='hidden' name='q' id='q' value=$q />";
                        echo "<input type='hidden' name='page' id='page' value='1' />";
                        echo "</form>";
                        $page -= 1;
                        echo "<form action='search.php' method='get' style='display:inline;'>";
                        echo "<button type='submit' id='prev'>< Prev Page</button>";
                        echo "<input type='hidden' name='q' id='q' value=$q />";
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
                        echo "<form action='search.php' method='get' style='display:inline;'>";
                        echo "<button type='submit' id='next'>Next Page ></button>";
                        echo "<input type='hidden' name='q' id='q' value=$q />";
                        echo "<input type='hidden' name='page' id='page' value=$page />";
                        echo "</form>";
                        $page -= 1;
                        echo "<form action='search.php' method='get' style='display:inline;'>";
                        echo "<button type='submit' id='allnext'>>></button>";
                        echo "<input type='hidden' name='q' id='q' value=$q />";
                        echo "<input type='hidden' name='page' id='page' value=$totalpages />";
                        echo "</form>";
                    }
                }
            ?>
             <hr>
        <!--Courses results-->
        <h2><u>Courses</u></h2>
        <table border="1">
            <!--Table Caption-->
            <?php 
                // Get preliminary data
                $q = $_GET["q"];
                $page = $_GET["page"];
                $query = "SELECT COUNT(*)\n"
                . "FROM courses\n"
                . "WHERE name LIKE '%$q%'";
                $rows = count_rows($query); // Number of rows matching query

                echo "<caption>$rows courses match the query \"$q\"</caption>";
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
                $query = "SELECT courseid, name\n"
                . "FROM courses\n"
                . "WHERE name LIKE '%$q%'\n"
                . "ORDER BY name\n"
                . "LIMIT $offset, 25"; // We only care about the relevant 25 rows corresponding to the page number
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
                        echo "<form action='search.php' method='get' style='display:inline;'>";
                        echo "<button type='submit' id='allprev'><<</button>";
                        echo "<input type='hidden' name='q' id='q' value=$q />";
                        echo "<input type='hidden' name='page' id='page' value='1' />";
                        echo "</form>";
                        $page -= 1;
                        echo "<form action='search.php' method='get' style='display:inline;'>";
                        echo "<button type='submit' id='prev'>< Prev Page</button>";
                        echo "<input type='hidden' name='q' id='q' value=$q />";
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
                        echo "<form action='search.php' method='get' style='display:inline;'>";
                        echo "<button type='submit' id='next'>Next Page ></button>";
                        echo "<input type='hidden' name='q' id='q' value=$q />";
                        echo "<input type='hidden' name='page' id='page' value=$page />";
                        echo "</form>";
                        $page -= 1;
                        echo "<form action='search.php' method='get' style='display:inline;'>";
                        echo "<button type='submit' id='allnext'>>></button>";
                        echo "<input type='hidden' name='q' id='q' value=$q />";
                        echo "<input type='hidden' name='page' id='page' value=$totalpages />";
                        echo "</form>";
                    }
                }
            ?>
    </body>
    <!--JS validate search query-->
    <script type="text/javascript" src="./include/func.js"></script>
</html>