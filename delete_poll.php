<?php
session_start();
if ($_SESSION['user']['username'] === 'admin') {
    require_once "jsonstorage.php";
    $jsonstorage = new JsonStorage("polls.json");
    function getPoll($poll)
    {
        return $poll->id === $_POST['getPoll'];
    }
    $jsonstorage->delete('getPoll');
    echo '<p style="color:green;">Kérdés törölve</p>';
} else {
    header('Location:main.php');
}
?>
<a href="main.php">Vissza a főoldalra</a>