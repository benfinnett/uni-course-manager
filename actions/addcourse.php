<?php
    require "../include/database.php";

    // Pull POST variables
    $name = $_POST["name"];
    $duration = intval($_POST["duration"]);
    $department = $_POST["department"];
    
    // Build prepared statement values array
    $values = [$name, $duration, $department];
    
    $query = "INSERT INTO courses\n"
    . "(name, duration, departmentid)\n"
    . "VALUES (?, ?, ?)";
    
    // Connect to DB, prepare statement and execute the INSERT
    $db = connect_to_db();
    $stmt = $db->prepare($query);
    try{
       $stmt->execute($values);
    } catch (Exception $e) {
        throw $e;
    }    

    // Return back to Staff page filtering to the new student
    header("Location: ../courses.php?q=$name&added=success");
?>