<?php
    require "../include/database.php";
    
    // Pull POST variables
    $moduleid = $_POST['moduleid'];

    $queries = ["DELETE FROM b_course_module WHERE moduleid=?",
                "DELETE FROM b_student_module WHERE moduleid=?",
                "DELETE FROM b_tutor_module WHERE moduleid=?",
                "DELETE FROM modules WHERE moduleid=?"];
    $db = connect_to_db();
    try {
        // Begin SQL transaction
        $db->beginTransaction();
        foreach ($queries as $query) {
            // Connect to DB, prepare statement and execute the DELETE
            $stmt = $db->prepare($query);
            $stmt->execute([$moduleid]);
        }
        // Commit changes to DB once finished to complete the transaction
        $db->commit();
    } catch (\PDOException $e) {
        // If the SQL query fails, rollback to a safe state
        $db->rollBack();
        die($e->getMessage());
    }
    

    // Return back to Staff page
    header("Location: ../modules.php");
?>