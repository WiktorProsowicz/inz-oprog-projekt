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

    // if query is long enough, look for queries that contain it in the middle
    if(strlen($q) > 15) $db_percent = "%%";
    else $db_percent = "";

    // for determining whether the user has looked for this query before
    if(isset($_SESSION["user_id"])) $usr_id = $_SESSION["user_id"];
    else $usr_id = -1;

    $query = sprintf("SELECT query, COUNT(*) AS cnt, id IN (SELECT id FROM search_queries WHERE `user_id` = $usr_id) AS user_searched
                    FROM search_queries 
                    WHERE query LIKE BINARY '$db_percent%s%%' 
                    GROUP BY query ORDER BY user_searched DESC, cnt DESC LIMIT 7;", $connection->real_escape_string($q));

    $result = $connection->query($query);

    $rows = $result->fetch_all();
    $result->free_result();

    foreach($rows as $row) {

        // echo '<li>
        //         <a href="/search.php?q='.$row[0].'"><span class="head__searchPopupListBold">'.$q.'</span>'.str_replace($q, "", $row[0]).'</a>
        //     </li>';

            $parts = explode($q, $row[0]);

            if($row[2] == "0") {
                $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        </svg>';
            }
            else {
                $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock-history" viewBox="0 0 16 16">
                            <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022l-.074.997zm2.004.45a7.003 7.003 0 0 0-.985-.299l.219-.976c.383.086.76.2 1.126.342l-.36.933zm1.37.71a7.01 7.01 0 0 0-.439-.27l.493-.87a8.025 8.025 0 0 1 .979.654l-.615.789a6.996 6.996 0 0 0-.418-.302zm1.834 1.79a6.99 6.99 0 0 0-.653-.796l.724-.69c.27.285.52.59.747.91l-.818.576zm.744 1.352a7.08 7.08 0 0 0-.214-.468l.893-.45a7.976 7.976 0 0 1 .45 1.088l-.95.313a7.023 7.023 0 0 0-.179-.483zm.53 2.507a6.991 6.991 0 0 0-.1-1.025l.985-.17c.067.386.106.778.116 1.17l-1 .025zm-.131 1.538c.033-.17.06-.339.081-.51l.993.123a7.957 7.957 0 0 1-.23 1.155l-.964-.267c.046-.165.086-.332.12-.501zm-.952 2.379c.184-.29.346-.594.486-.908l.914.405c-.16.36-.345.706-.555 1.038l-.845-.535zm-.964 1.205c.122-.122.239-.248.35-.378l.758.653a8.073 8.073 0 0 1-.401.432l-.707-.707z"/>
                            <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0v1z"/>
                            <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5z"/>
                        </svg>';
            }

            echo '<li>
                <a href="/search.php?q='.$row[0].'">
                    <span class="head__searchPopupListIcon">'.$icon.'</span>
                    '.$parts[0].'
                    <span class="head__searchPopupListBold">
                    '.$q.'</span>'.$parts[1].'
                </a>
            </li>';

    }

?>