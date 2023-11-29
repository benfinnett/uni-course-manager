<?php require './include/database.php'?>

<html lang="en">
    <head>
        <!--Title and favicon-->
        <title>Modules</title>
        <link rel="icon" type="image/x-icon" href="./assets/favicon.ico">
    </head>

    <body>
        <h1><u>Modules</u></h1>
        <!--Back to menu button-->
        <form action="index.php" style="display:inline;">
            <button type="submit" id="back">Back to Menu</button>
        </form>
        <!--Search box-->
        <form action="modules.php" method="get" style="display:inline;">
            <input type="text" id="q" name="q" placeholder="Search modules..." onkeyup="checkForm('q', 'UserSearch')">
            <button type="submit" id="UserSearch" disabled>Search</button>
        </form>
        <!--Clear search button-->
        <?php 
            $q = $_GET["q"];
            // Only show clear search button if query is active
            if (isset($q)) {
                echo "<form action='modules.php' style='display:inline;'>";
                echo "<button type='submit' id='clear' onclick='clearSession()'>Clear search</button>";
                echo "</form>";
            }
        ?>
        <span id="error"></span>
        <hr>

        <!--Modules results-->
        <h2><u>All Modules</u></h2>

        <!--Add module-->
        <div id="NewModuleButton">
            <button onclick="showModuleEntry()">Add New Module</button>
        </div>
        <!--Form content (hidden by default)-->
        <div id="NewModule" style="display:none;">
            <!--Using POST method to hide passed variables rather than GET which places them in the URL-->
            <form action="actions/addmodule.php" method="post">
                <input type="text" id="name" name="name" placeholder="Module name" onkeyup="checkAddForm()">
                <label for="duration">Weekly hours:</label>
                <input type="number" id="duration" name="duration" min="1" max="20" onchange="checkAddForm()">
                <input type='hidden' name='moduleid' id='moduleid' value="" />
                <button type="submit" id="AddModule" onclick="addModule();showModuleEntry()" disabled>Add module</button>
                <button type="reset" onclick="showModuleEntry()">Cancel</button>
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
                    . "FROM modules\n"
                    . "WHERE name LIKE '%$q%'";
                    $rows = count_rows($query); // Number of rows matching query

                    echo "<caption>$rows modules match the query \"$q\"</caption>";
                } else {
                    $rows = count_rows("SELECT COUNT(*) FROM modules");
                    echo "<caption>Showing $rows modules</caption>";
                }
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
                if (isset($q)) {
                    $query = "SELECT moduleid, name\n"
                    . "FROM modules\n"
                    . "WHERE name LIKE '%$q%'\n"
                    . "ORDER BY name\n"
                    . "LIMIT $offset, 25"; // We only care about the relevant 25 rows corresponding to the page number
                } else {
                    $query = "SELECT moduleid, name\n"
                    . "FROM modules\n"
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
                        echo "<form action='modules.php' method='get' style='display:inline;'>";
                        echo "<button type='submit' id='allprev'><<</button>";
                        if (isset($q)) {echo "<input type='hidden' name='q' id='q' value=$q />";}
                        echo "<input type='hidden' name='page' id='page' value='1' />";
                        echo "</form>";
                        $page -= 1;
                        echo "<form action='modules.php' method='get' style='display:inline;'>";
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
                        echo "<form action='modules.php' method='get' style='display:inline;'>";
                        echo "<button type='submit' id='next'>Next Page ></button>";
                        if (isset($q)) {echo "<input type='hidden' name='q' id='q' value=$q />";}
                        echo "<input type='hidden' name='page' id='page' value=$page />";
                        echo "</form>";
                        $page -= 1;
                        echo "<form action='modules.php' method='get' style='display:inline;'>";
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
    <!--Toggle showing new module entry details-->
    <script type="text/javascript">
        function showModuleEntry() {
            var content_div = document.getElementById("NewModule")
            if (content_div.style.display === "none") {
                content_div.style.display = "block";
            } else {
                content_div.style.display = "none";
            }
            var button_div = document.getElementById("NewModuleButton")
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
            if (name.match(allowed_chars) && (duration <= 20 && duration >= 1)) {
                document.getElementById("AddModule").disabled = false; 
                error.textContent = "";
            } else if (!name.match(allowed_chars) && name !== "") {
                document.getElementById("AddModule").disabled = true;
                error.textContent = "Module name must only contain any of A-z, 0-9, -\/:&. or spaces!";
            } else if (duration > 20 || duration < 1) {
                document.getElementById("AddModule").disabled = true;
                error.textContent = "Weekly hours must be a number between 1 and 20!";
            } else {
                document.getElementById("AddModule").disabled = true;
                error.textContent = "";
            }
        }

        function addModule() {
            chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            output = "";
            for (j = 0; j < 3; j++) {
                output += chars.charAt(Math.floor(Math.random() * chars.length)); 
            }
            output += "-";
            for (j = 0; j < 2; j++) {
                output += chars.charAt(Math.floor(Math.random() * chars.length)); 
            }
            console.log(output);
            document.getElementById("moduleid").value = output;
        }
    </script>
</html>