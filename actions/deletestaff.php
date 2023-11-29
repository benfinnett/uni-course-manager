<?php
    require "../include/database.php";
    
    // Pull POST variables
    $staffid = $_POST['staffid'];
    $stafftype = (int) $_POST['stafftype'];

    $db = connect_to_db();
    $db->beginTransaction();
    try {
        if ($stafftype == 1) {
            // Remove all modules connected to that tutor
            $query = "DELETE FROM b_tutor_module WHERE staffid=?";
    
            // Connect to DB, prepare statement and execute the DELETE
            $stmt = $db->prepare($query);
            $stmt->execute([$staffid]);
        }
    
        $query = "DELETE FROM staff WHERE staffid=?";
        $stmt = $db->prepare($query);
        $stmt->execute([$staffid]);
        $db->commit();
    } catch (\PDOException $e) {
        // If the SQL query fails, rollback to a safe state
        $db->rollBack();
        die($e->getMessage());
    }

    // Return back to Staff page
    header("Location: ../staff.php");
?>