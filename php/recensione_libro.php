<?php 

    header('Content-Type: application/xml');

    $status_code = "";

    $conn = new mysqli("localhost", "root", "", "recensione_libro");

    if ($_SERVER['REQUEST_METHOD'] == "GET"){

    }else{
        $status_code = 405;
    }

?>