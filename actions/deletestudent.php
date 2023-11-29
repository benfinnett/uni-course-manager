<?php
    require "../include/database.php";

    // Pull POST variables
    $studentid = $_POST['studentid'];

    $queries = ["DELETE FROM b_student_module WHERE studentid=?",
                "DELETE FROM students WHERE studentid=?"];
    $db = connect_to_db();
    $db->beginTransaction();
    try {
        foreach ($queries as $query) {
            // Connect to DB, prepare statement and execute the DELETE
            $stmt = $db->prepare($query);
            $stmt->execute([$studentid]);
        }
        $db->commit();
    } catch (\PDOException $e) {
        // If the SQL query fails, rollback to a safe state
        $db->rollBack();
        die($e->getMessage());
    }

    // Return back to Students page
    header("Location: ../students.php");
?>