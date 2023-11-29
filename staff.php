<?php require './include/database.php'?>

<html lang="en">
    <head>
        <!--Title and favicon-->
        <title>Staff</title>
        <link rel="icon" type="image/x-icon" href="./assets/favicon.ico">
    </head>

    <body>
        <h1><u>Staff</u></h1>
        <!--Back to menu button-->
        <form action="index.php" style="display:inline;">
            <button type="submit" id="back">Back to Menu</button>
        </form>
        <!--Search box-->
        <form action="staff.php" method="get" style="display:inline;">
            <input type="text" id="q" name="q" placeholder="Search staff..." onkeyup="checkForm('q', 'UserSearch')">
            <button type="submit" id="UserSearch" disabled>Search</button>
        </form>
        <!--Clear search button-->
        <?php 
            $q = $_GET["q"];
            // Only show clear search button if query is active
            if (isset($q)) {
                echo "<form action='staff.php' style='display:inline;'>";
                echo "<button type='submit' id='clear' onclick='clearSession()'>Clear search</button>";
                echo "</form>";
            }
        ?>
        <span id="error"></span>
        <hr>

        <!--Staff results-->
        <h2><u>All Staff</u></h2>

        <!--Add staff-->
        <div id="NewStaffButton">
            <button onclick="showStaffEntry()">Add New Staff Member</button>
        </div>
        <!--Form content (hidden by default)-->
        <div id="NewStaff" style="display:none;">
            <!--Using POST method to hide passed variables rather than GET which places them in the URL-->
            <form action="actions/addstaff.php" method="post">
                <input type="text" id="firstname" name="firstname" placeholder="First name" onkeyup="checkAddForm()">
                <input type="text" id="lastname" name="lastname" placeholder="Last name" onkeyup="checkAddForm()">
                <!--Gender is selected using dropdown box-->
                <label for="gender">Gender: </label>
                <select name="gender" id="gender" required>
                    <option value="M">Male</option>
                    <option value="F">Female</option>
                </select>
                <label for="dob">DOB: </label>
                <input type="date" id="dob" name="dob" value="1980-01-01" min="1940-01-01" max="1990-12-31">
                <!--Email is formed automatically based on staff name-->
                <input type='hidden' name='email' id='email' value="" />
                <label for="role">Role: </label>
                <select name="role" id="role" required>
                    <option value="1">Tutor</option>
                    <option value="0">Services</option>
                </select>
                
                <button type="submit" id="AddStaff" onclick="addStaff();showStaffEntry()" disabled>Add staff</button>
                <button type="reset" onclick="showStaffEntry()">Cancel</button>
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
                    . "FROM staff\n"
                    . "WHERE CONCAT(firstname, ' ', lastname) LIKE '%$q%'";
                    $rows = count_rows($query); // Number of rows matching query

                    echo "<caption>$rows staff match the query \"$q\"</caption>";
                } else {
                    $rows = count_rows("SELECT COUNT(*) FROM staff");
                    echo "<caption>Showing $rows staff</caption>";
                }
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
                if (isset($q)) {
                    $query = "SELECT staffid, firstname, lastname\n"
                    . "FROM staff\n"
                    . "WHERE CONCAT(firstname, ' ', lastname) LIKE '%$q%'\n"
                    . "ORDER BY lastname\n"
                    . "LIMIT $offset, 25"; // We only care about the relevant 25 rows corresponding to the page number
                } else {
                    $query = "SELECT staffid, firstname, lastname\n"
                    . "FROM staff\n"
                    . "ORDER BY lastname\n"
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
                        $href = "<a href='staffmember.php?id=" . $row["staffid"] . "'>";
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
                        echo "<form action='staff.php' method='get' style='display:inline;'>";
                        echo "<button type='submit' id='allprev'><<</button>";
                        if (isset($q)) {echo "<input type='hidden' name='q' id='q' value=$q />";}
                        echo "<input type='hidden' name='page' id='page' value='1' />";
                        echo "</form>";
                        $page -= 1;
                        echo "<form action='staff.php' method='get' style='display:inline;'>";
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
                        echo "<form action='staff.php' method='get' style='display:inline;'>";
                        echo "<button type='submit' id='next'>Next Page ></button>";
                        if (isset($q)) {echo "<input type='hidden' name='q' id='q' value=$q />";}
                        echo "<input type='hidden' name='page' id='page' value=$page />";
                        echo "</form>";
                        $page -= 1;
                        echo "<form action='staff.php' method='get' style='display:inline;'>";
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
    <!--Toggle showing new staff entry details-->
    <script type="text/javascript">
        function showStaffEntry() {
            var content_div = document.getElementById("NewStaff")
            if (content_div.style.display === "none") {
                content_div.style.display = "block";
            } else {
                content_div.style.display = "none";
            }
            var button_div = document.getElementById("NewStaffButton")
            if (button_div.style.display === "none") {
                button_div.style.display = "block";
            } else {
                button_div.style.display = "none";
            }
        }

        function checkAddForm() {
            var allowed_chars = /^[A-Za-z \-]+$/; // Only letters & spaces allowed
            fname = document.getElementById("firstname").value;
            lname = document.getElementById("lastname").value;
            if (fname.match(allowed_chars) && lname.match(allowed_chars)) {
                document.getElementById("AddStaff").disabled = false;
                // Hide error message
                var error = document.getElementById("AddError");
                error.textContent = "";
            } else if ((fname.match(allowed_chars) && lname === "") || (lname.match(allowed_chars) && fname === "")) {
                document.getElementById("AddStaff").disabled = true;
                // Hide error message
                var error = document.getElementById("AddError");
                error.textContent = "";
            } else if (fname === "" && lname === "") {
                document.getElementById("AddStaff").disabled = true;
                // Hide error message
                var error = document.getElementById("AddError");
                error.textContent = "";
            } else {
                document.getElementById("AddStaff").disabled = true;
                // Show error message
                var error = document.getElementById("AddError");
                error.textContent = "Staff name must only contain letters, spaces and hyphens!";
                error.style.color = "red";
            }
        }

        function addStaff() {
            var fname = document.getElementById("firstname").value;
            var lname = document.getElementById("lastname").value;
            var email = document.getElementById("email");
            var num = Math.floor(Math.random() * 10);
            email.value = fname.charAt(0) + lname.replace(" ", "").replace("-", "") + num.toString() + "@university.edu.uk";
            fname.value = "";
            lname.value = "";
        }
    </script>
</html>