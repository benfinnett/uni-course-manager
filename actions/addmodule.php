<?php
    require "../include/database.php";

    // Pull POST variables
    $moduleid = $_POST["moduleid"];
    $name = $_POST["name"];
    $duration = intval($_POST["duration"]);

    $db = connect_to_db();
    $query = "SELECT moduleid FROM modules WHERE moduleid='$moduleid'";
    $stmt = get_statement($db, $query);
    $matches = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $matches++;
    }
    if ($matches > 0) {
        header("Location: ../modules.php?q=$name&added=failed");
        exit();
    }

    // Build prepared statement values array
    $values = [$moduleid, $name, $duration];

    $query = "INSERT INTO modules\n"
    . "(moduleid, name, duration)\n"
    . "VALUES (?, ?, ?)";

    // Connect to DB, prepare statement and execute the INSERT
    $stmt = $db->prepare($query);
    try{
       $stmt->execute($values);
    } catch (Exception $e) {
        throw $e;
    }    

    // Return back to Staff page filtering to the new student
    header("Location: ../modules.php?q=$name&added=success");
?>