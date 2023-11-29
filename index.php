<?php require './include/database.php';?>

<html lang="en">
    <head>
        <title>Admin Control Panel</title>
        <link rel="icon" type="image/x-icon" href="./assets/favicon.ico">
    </head>
    
    <body>
        <h1><u>University Admin Control Panel</u></h1>
        <h4>COMS404 Database Systems</h4>

        <hr>
        <h2><u>Search</u></h3>
        <p>Search for students, staff, modules or courses!</p>
        <div class="search-container">
            <form action="search.php" method="get">
                <input type="text" id="q" name="q" placeholder="Search..." onkeyup="checkForm('q', 'UserSearch')">
                <button type="submit" id="UserSearch" disabled>Search</button>
            </form>
            <span id="error"></span>
        </div>

        <hr>
        <h2><u>View</u></h3>
        <p>View, add and remove various aspects of the database.</p>
        <form action="students.php">
            <button type="submit">View <?php echo count_rows("SELECT COUNT(*) FROM students")?> students</button>
        </form>
        <form action="staff.php">
            <button type="submit">View <?php echo count_rows("SELECT COUNT(*) FROM staff")?> staff</button>
        </form>
        <form action="modules.php">
            <button type="submit">View <?php echo count_rows("SELECT COUNT(*) FROM modules")?> modules</button>
        </form>
        <form action="courses.php">
            <button type="submit">View <?php echo count_rows("SELECT COUNT(*) FROM courses")?> courses</button>
        </form>
    </body>

    <!--JS validate search query-->
    <script type="text/javascript" src="./include/func.js"></script>
</html>