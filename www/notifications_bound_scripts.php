<?php 

    session_start();

    if(!isset($_SESSION["user_username"])) {
        header("Location: login.php");
        exit();
    }


    // ------------ connecting to database -----------
    require_once("./connect.php");

    try {
        $connection = connect_to_database();
    }
    catch(Exception $e) {
        die("Failed to connect to database " . $e);
    }
    
    if($connection->connect_error) {
        die("Failed to connect to database: " . $connection->connect_error);
    }
    // -----------------------------------------------


    function get_least_occupied_admin($connectionObject) {
        $query = "SELECT u.id, COUNT(*) 
                    FROM `users` u LEFT JOIN 
                    (SELECT * FROM `notifications` WHERE `high_priority` = true) n 
                    ON n.recipient_id = u.id
                    GROUP BY u.id ORDER BY 2 ASC LIMIT 1;";

        $result = $connectionObject->query($query);

        if($result->num_rows > 0) {
            $ret = $result->fetch_array()[0];
        }
        else {
            $ret = null;
        }

        $result->free_result();
        return $ret;
    }


    // reporting a post
    if(isset($_POST["readPostReport"])) {

        $reportedId = $_POST["readPostReportId"];
        $reportedContent = $_POST["readPostReportContent"];

        // getting interval since last report of that post from that user
        $query = sprintf("SELECT COALESCE(EXTRACT(HOUR FROM NOW() - `date`), 0)
                            FROM `reports` 
                            WHERE  post_id <=> %d AND author_id = %d
                            ORDER BY 1 ASC LIMIT 1;", $reportedId, $_SESSION["user_id"]);

        $result = $connection->query($query);

        if($result->num_rows > 0) {

            if($result->fetch_array()[0] < 24) {

                $_SESSION["read_reportpostmsg"] = "Możesz ponownie zgłosić ten post dopiero po 24 godzinach.";

                header("Location: read.php?p=" . $reportedId);
                exit();
            }

        }

        // inserting a report to database
        $query = sprintf("INSERT INTO reports 
                        (`author_id`, `post_id`, `content`, `date`) VALUES (%d, %d, '%s', NOW());",
                        $_SESSION["user_id"], $reportedId, $reportedContent);
        $connection->query($query);

        $leastOccupiedAdminId = get_least_occupied_admin($connection);

        if($leastOccupiedAdminId !== null) {
            $content = 'Użytkownik <a href="profile.php?u='.$_SESSION["user_username"].'">'.
                        $_SESSION["user_username"].
                    '</a>
                    zgłosił post <a href="read.php?p='.$reportedId.'"></a>.';
            
            // inseritng a notification with message to admin
            $query = sprintf("INSERT INTO notifications
                            (`author_id`, `recipient_id`, `content`, `high_priority`, `watched`)
                            VALUES (%d, %d, '%s', true, false);",
                            $_SESSION["user_id"], $leastOccupiedAdminId, $connection->real_escape_string($content));
           
            $connection->query($query);
        }
        
        
        header("Location: read.php?p=" . $reportedId);
        exit();
    }

?>