<?php 
    mysqli_report(MYSQLI_REPORT_OFF);
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "perpustakaan";
    $conn = new mysqli($servername, $username, $password, $dbname);
    function query($query) {
        global $conn;
        $result = mysqli_query($conn , $query);
        $rows = [];
        while( $row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }