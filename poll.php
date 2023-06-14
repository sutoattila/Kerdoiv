<h2>Kérdésszerkesztő</h2>
<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
}
require_once "jsonstorage.php";
$jsonstorage = new JsonStorage("polls.json");
$array = $jsonstorage->all();
$length = count($array);
require_once "poll_class.php";
?>
<form action="" method="post" novalidate>
    <label for="question">Kérdés: </label>
    <input id="question" name="question" type="text"><br>
    <label for="options">Válasz: </label><br>
    <textarea id="options" name="options" value=""></textarea><br>
    <input type="hidden" name="isMultiple" value="False" />
    <input type="checkbox" id="isMultiple" name="isMultiple" value="True">
    <label for="isMultiple">Több válasz is megadható</label><br>
    <label for="deadLine">Határidő:</label>
    <input type="date" id="deadLine" name="deadLine">
    <input type="submit" value="Küldés">
</form>
<?php
$jsonstorage = new JsonStorage("polls.json");
function is_empty($input, $key)
{
    return !(isset($input[$key]) && trim($input[$key]) !== "");
}
if (count($_POST) != 0) {
    if ((is_empty($_POST, 'question') || is_empty($_POST, 'options') || is_empty($_POST, 'deadLine'))) {
        echo '<p style="color: red;">*Az összes mező kitöltése szükséges</p>';
    } else {
        $array = explode(PHP_EOL, $_POST["options"]);
        print_r($array);
        $post = new Poll($_POST["question"], $array, $_POST['isMultiple'], $_POST['deadLine']);
        $jsonstorage->insert($post);
        header('Location: main.php');
    }
}
?>
<a href="main.php">Vissza a főoldalra</a>