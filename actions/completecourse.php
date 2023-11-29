<?php
    require "../include/database.php";

    // Pull POST variables
    $studentid = $_POST['studentid'];
    $courseid = $_POST['courseid'];

    $db = connect_to_db();
    $query = "SELECT modules.moduleid, modules.name, b_course_module.courseid\n"
            . "FROM modules\n"
            . "INNER JOIN b_course_module ON b_course_module.moduleid = modules.moduleid\n"
            . "WHERE b_course_module.courseid = $courseid";
    $stmt = get_statement($db, $query);
    $modules = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $mid = $row["moduleid"];
        $module_result = $_POST["$mid"];
        if (isset($module_result)) {
            array_push($modules, $module_result);
        }
    }
    foreach ($modules as $moduleid) {
        $query = "INSERT INTO b_student_module\n"
        . "(studentid, moduleid)"
        . "VALUES (?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$studentid, $moduleid]);
    }
    // Update students course column 
    $query = "UPDATE students\n"
    . "SET courseid=?\n"
    . "WHERE studentid=?";
    $stmt = $db->prepare($query);
    $stmt->execute([$courseid, $studentid]);

    // Return back to student page
    header("Location: ../student.php?id=$studentid&added=success");
?>