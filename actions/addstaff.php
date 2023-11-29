<?php
    require "../include/database.php";

    // Pull POST variables and set name to Title Case
    $fname = ucwords(strtolower($_POST["firstname"]));
    $lname = ucwords(strtolower($_POST["lastname"]));
    $email = $_POST["email"];
    $gender = $_POST["gender"];
    $dobstr = strtotime($_POST["dob"]);
    $dob = date("Y-m-d", $dobstr);
    $role = (int) $_POST["role"];


    // Build prepared statement values array
    $values = [$fname, $lname, $email, $gender, $dob, $role];

    $query = "INSERT INTO staff\n"
    . "(firstname, lastname, email, gender, dob, stafftype)\n"
    . "VALUES (?, ?, ?, ?, ?, ?)";

    // Connect to DB, prepare statement and execute the INSERT
    $db = connect_to_db();
    $stmt = $db->prepare($query);
    try{
        $stmt->execute($values);
    } catch (Exception $e) {
        throw $e;
    }    

    // Return back to Staff page filtering to the new student
    header("Location: ../staff.php?q=$fname+$lname&added=success");
?>