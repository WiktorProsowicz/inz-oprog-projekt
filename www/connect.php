<?php 

    $connect_host = "localhost";
    $connect_user = "root";
    $connect_passwd = "";
    $connect_dbname = "inz_oprog";

    function connect_to_database() {
        global $connect_host, $connect_user, $connect_passwd, $connect_dbname;
        return new mysqli($connect_host, $connect_user, $connect_passwd, $connect_dbname);
    }

?>