<?php

require_once __DIR__ . '/database.php';

if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit;
}

?>