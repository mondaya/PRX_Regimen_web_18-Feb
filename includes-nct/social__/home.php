<?php
session_start();
//Always place this code at the top of the Page
if (!isset($_SESSION['id'])) {
    // Redirection to login page twitter or facebook
    header("location: index.php");
}

echo '<h1>Welcome</h1>';
echo 'id : ' . $_SESSION['id'];
echo '<br/>Name : ' . $_SESSION['username'];
echo '<br/>Email : ' . $_SESSION['email'];
echo '<br/>Logout from <a href="logout.php?logout">Logout</a>';
//print_r($_SESSION['data']);
?>