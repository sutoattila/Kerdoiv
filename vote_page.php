<h2>Szavazóoldal</h2>
<a href="main.php">Vissza a főoldalra</a><br>
<?php
require_once "jsonstorage.php";
$jsonstorage = new JsonStorage("polls.json");
$array = $jsonstorage->all();
$cnt = 1;
session_start();
if (!isset($_SESSION['user'])) {
    header('Location:login.php');
}
if ($_SESSION['user']['username'] === 'admin') {
    echo "Az adminok nem szavazhatnak!";
} else {
    if (isset($_SESSION['getPoll'])) {
        $_POST['getPoll'] = $_SESSION['getPoll'];
        if (isset($_SESSION['not_three'])) {
            echo '<p style="color: red;">*Három válaszlehetőséget kell megjelölni</p>';
        } else {
            echo '<p style="color: red;">*A válasz megadása kötelező</p>';
        }
    }
    echo $array[$_POST['getPoll']]->question;
    if (filter_var($array[$_POST['getPoll']]->isMultiple, FILTER_VALIDATE_BOOLEAN)) {
        $cnt = 1;
        echo '<form action="vote_successfull.php" method="post" novalidate>';
        foreach ($array[$_POST['getPoll']]->options as $option) {
            echo '<input type="checkbox" id="solution' . $cnt . '" name="solution' . $cnt . '" value="' . $option . '">
                    <label for="solution' . $cnt . '">' . $option . '</label><br>';
            $cnt++;
        }
        echo '<input type="hidden" name="getPoll" value="' . $array[$_POST['getPoll']]->id . '">';
        if (in_array($_SESSION['user']['username'], $array[$_POST['getPoll']]->voters, True)) {
            echo '<input type="submit" value="Szavazat frissítése"></form>';
        } else {
            echo '<input type="submit" value="Szavazat leadása"></form>';
        }

        echo 'Leadás határideje: ' . $array[$_POST['getPoll']]->deadline;
        echo '<br>Létrehozás ideje: ' . $array[$_POST['getPoll']]->createdAt;
    } else {
        $cnt = 1;
        echo '<form action="vote_successfull.php" method="post" novalidate>';
        foreach ($array[$_POST['getPoll']]->options as $option) {
            echo '<input type="radio" id="solution' . $cnt . '" name="solution" value="' . $option . '">
                    <label for="solution' . $cnt . '">' . $option . '</label><br>';
            $cnt++;
        }
        echo '<input type="hidden" name="getPoll" value="' . $array[$_POST['getPoll']]->id . '">';
        if (in_array($_SESSION['user']['username'], $array[$_POST['getPoll']]->voters, True)) {
            echo '<input type="submit" value="Szavazat frissítése"></form>';
        } else {
            echo '<input type="submit" value="Szavazat leadása"></form>';
        }

        echo 'Leadás határideje: ' . $array[$_POST['getPoll']]->deadline;
        echo '<br>Létrehozás ideje: ' . $array[$_POST['getPoll']]->createdAt;
    }
}
?>