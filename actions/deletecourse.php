<?php
    require "../include/database.php";
    
    // Pull POST variables
    $courseid = $_POST['courseid'];

    $queries = ["DELETE FROM b_course_module WHERE courseid=?",
                "DELETE FROM courses WHERE courseid=?"];
    $db = connect_to_db();
    $db->beginTransaction();
    try {
        foreach ($queries as $query) {
            // Connect to DB, prepare statement and execute the DELETE
            $stmt = $db->prepare($query);
            $stmt->execute([$courseid]);
        }
        $db->commit();
    } catch (\PDOException $e) {
        // If the SQL query fails, rollback to a safe state
        $db->rollBack();
        die($e->getMessage());
    }

    // Return back to Staff page
    header("Location: ../courses.php");
?>