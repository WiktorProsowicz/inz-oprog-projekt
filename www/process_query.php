<?php 

    session_start();

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

    if(!isset($_POST["q"])) {
        exit();
    }

    $q = $_POST["q"];

    $query = sprintf("SELECT query, COUNT(*) AS cnt FROM search_queries 
                    WHERE query LIKE '%s%%' 
                    GROUP BY query ORDER BY cnt DESC LIMIT 7;", $connection->real_escape_string($q));

    $result = $connection->query($query);

    $rows = $result->fetch_all();
    $result->free_result();

    foreach($rows as $row) {

        echo '<li>
                <a href="/search.php?q='.$row[0].'"><span class="head__searchPopupListBold">'.$q.'</span>'.str_replace($q, "", $row[0]).'</a>
            </li>';
    }

?>