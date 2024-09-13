<?php
session_start();
unset($_SESSION['panier']);
header("Location: index.php");
    exit();

?>