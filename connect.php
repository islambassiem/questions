<?php

    $dsn    = 'mysql:host=localhost; dbname=u426787650_2023party;charset=utf8';
    $user   = 'u426787650_2023party';
    $pass   = 'IBA0274#5325i';

    // $user   = 'root';
    // $pass   = '';

    try {
        $conn = new PDO ($dsn, $user, $pass);
        $conn -> setAttribute (PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn -> setAttribute(PDO::ATTR_STRINGIFY_FETCHES, true);
    }

    catch (PDOException $e) {
        echo 'Failed to connect because of an error ' . $e -> getMessage();
    }