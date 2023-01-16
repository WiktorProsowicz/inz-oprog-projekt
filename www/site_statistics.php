<?php 

    require_once("./connect.php");

    try {
        $connection = connect_to_database();
    }
    catch(Exception $e) {
        die('');
    }

    if($connection->connect_error) {
        die('');
    }


    if(isset($_POST["fetchMonthlyActivity"])) {

        $month = $_POST["month"];
        $year = $_POST["year"];

        $query = sprintf("SELECT EXTRACT(DAY FROM `created`) AS `day`, COUNT(*) AS `count`
                            FROM `posts` 
                            WHERE EXTRACT(YEAR FROM `created`) = %d AND EXTRACT(MONTH FROM `created`) = %d
                            GROUP BY `day`
                            ORDER BY 1;", $year, $month);

        $results = $connection->query($query);
        
        if($results->num_rows > 0) {
            $counts = array();
            for($day = 0; $day < cal_days_in_month(CAL_GREGORIAN, $month, $year); $day += 1) {
                array_push($counts, 0);
            }

            foreach($results->fetch_all(MYSQLI_ASSOC) as $row) {
                $counts[$row["day"] - 1] = $row["count"];
            }

            foreach(array_values($counts) as $index=>$count) {
                echo ($index + 1) . " " . $count . "\n";
            }
        }    

        $results->free_result();
        exit();

    }


    if(isset($_POST["fetchYearlyActivity"])) {

        $year = $_POST["year"];

        $query = sprintf("SELECT EXTRACT(WEEK FROM `modified`) as `week`, COUNT(*) AS `count`
                        FROM `posts`
                        WHERE EXTRACT(YEAR FROM `modified`) = %d
                        GROUP BY `week`;", $year);
    
        $results = $connection->query($query);
        
        if($results->num_rows > 0) {

            $counts = array();
            for($week = 0; $week < 53; $week += 1) {
                array_push($counts, 0);
            }

            $rows = $results->fetch_all(MYSQLI_ASSOC);
            foreach($rows as $row) {
                $counts[$row["week"]] = $row["count"];
            }

            foreach($counts as $index=>$count) {
                echo $index . " " . $count . "\n";
            }
        }
        
        $results->free_result();
        exit();

    }


    if(isset($_POST["fetchYearlyNumbers"])) {
        
        $year = $_POST["year"];

        // in order: nPosts, nComments, nNewUsers, nReports
        $queries = array(
            "SELECT COUNT(*) FROM posts WHERE created BETWEEN '$year/01/01' AND '$year/12/31';",
            "SELECT COUNT(*) FROM comments WHERE created BETWEEN '$year/01/01' AND '$year/12/31';",
            "SELECT COUNT(*) FROM users WHERE created_account BETWEEN '$year/01/01' AND '$year/12/31';",
            "SELECT COUNT(*) FROM reports WHERE `date` BETWEEN '$year/01/01' AND '$year/12/31';"
        );

        foreach($queries as $query) {
            $result = $connection->query($query);
            echo $result->fetch_array()[0] . "\n";
            $result->free_result();
        }

        exit();
    }


?>