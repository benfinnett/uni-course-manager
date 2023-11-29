<?php
    function connect_to_db() {
        try {
            // connect to db
            $db = new PDO(
                "mysql:host=" . getenv('DB_HOST') . ";dbname=" . getenv('DB_NAME'),
                getenv('DB_USER'),
                getenv('DB_PASS')
            );
        }
        catch(Exception $e) {
            echo "There was an error connecting to the database:<br>";
            echo $e->getMessage();
            return null;
        }
        return $db;
    }

    function get_statement($db, $query) {
        if (!$db instanceof PDO){
            echo "No PDO object provided!<br>";
            return null;
        } elseif ($query == null){
            echo "No query provided!<br>";
            return null;
        }
        // results of query as statement
        return $db->query($query);
    }

    function count_rows($query) {
        $db = connect_to_db();
        $stmt = get_statement($db, $query);
        $count = $stmt->fetchColumn();
        if ($count === null){
            $count = 0;
        }
        return $count;
    }
?>
