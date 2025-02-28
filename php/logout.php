<?php
include '../settings/config.php';
include 'user.php';

$user = new User($conn);
$user->logout();

header("Location: ../index");
exit();
?>
