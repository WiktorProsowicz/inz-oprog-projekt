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

        $results->free_result();
        exit();

    }


?>