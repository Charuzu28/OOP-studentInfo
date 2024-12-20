<?php
session_start();
session_destroy();
echo "<script>alert('Log out successfully'); window.location.href = 'login.php';</script>";
exit;
?>
