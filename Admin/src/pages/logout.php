<?php
require '../../config/db.php'; // ភ្ជាបប់ទៅ databese
session_start();//ចាប់ផ្តើម


session_unset();
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>
