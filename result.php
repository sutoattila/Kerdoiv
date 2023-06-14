<h2>Eredmény</h2>
<?php
require_once "jsonstorage.php";
$jsonstorage = new JsonStorage("polls.json");
$array = $jsonstorage->all();
?>
<style>
    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
        text-align: center;
    }
</style>
<h4>Kérdés: <?= $array[$_POST['getPoll']]->question ?></h4>
<?php
echo '<table><tr><th>Válasz</th><th>Szavazatok száma</th></tr>';

foreach ($array[$_POST['getPoll']]->given_answers as $key => $value) {
    echo '<tr><td>' . $key . '</td><td>' . $value . '</td></tr>';
}
echo "</table>";
echo '<br><a href="main.php">Vissza a főoldalra</a>';
