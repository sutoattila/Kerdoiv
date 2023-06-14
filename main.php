<h2>Közvéleménykutatás az IK-n</h2>
<p>Ezen az oldalon az IK hallgatóinak véleményét mérjük fel az alábbi kérdésekben.</p>
<style>
    form {
        padding: 0ch;
        margin: 0%;
    }
</style>
<?php
session_start();
$admin = false;
if (isset($_SESSION['user'])) {
    if (!isset($_SESSION['_id'])) {
        require_once "user.php";
        $ur = new UserRepository("users.json");
        foreach ($ur->all() as $key => $value) {
            if ($value->username === $_SESSION['user']['username']) {
                $_SESSION['_id'] = $key;
            }
        }
    }
    if (isset($_SESSION['_id']))
        echo '<p>Bejelentkezve: ' . $_SESSION['user']['username'] . '</p>';

    if ($_SESSION['user']['username'] === 'admin') {
        echo '<a href="poll.php">Ugrás a kérdésszerkesztő oldalra</a><br>';
        $admin = true;
        echo '<p>Bejelentkezve: admin</p>';
    }
    echo '<a href="logout.php">Kijelentkezés</a>';
} else {
    echo '<p>Nincs bejelentkezett felhasználó</p>*A szavazáshoz bejelentkezés szükséges<br><a href="login.php">Bejelentkezés</a>';
}
?>

<?php
unset($_SESSION['getPoll']);
echo "<h3>Aktuális kérdőívek</h3>";
require_once "jsonstorage.php";
$jsonstorage = new JsonStorage("polls.json");
$array = $jsonstorage->all();
function compareByTimeStamp($time1, $time2)
{
    if ($time1->createdAt < $time2->createdAt)
        return 1;
    else if ($time1->createdAt > $time2->createdAt)
        return -1;
    else
        return 0;
}
usort($array, "compareByTimeStamp");
$expired = [];
$current = [];
foreach ($array as $polls) {
    if ($polls->deadline < date("Y-m-d")) {
        $expired[] = $polls;
    } else {
        $current[] = $polls;
    }
}
foreach ($current as $polls) {
    echo 'Azonosító: ' . $polls->id;
    echo "<br>";
    echo 'Létrehozva: ' . $polls->createdAt;
    echo "<br>";
    echo 'Határidő: ' . $polls->deadline;
    echo "<br>";
    $voters = [];
    foreach ($array as $value) {
        if ($value->id === $polls->id) {
            $voters = $value->voters;
            break;
        }
    }
    if (isset($_SESSION['user']['username']) && in_array($_SESSION['user']['username'], $voters, true)) {
        echo '<form action="vote_page.php" method="post" novalidate>
            <input type="hidden"  name="getPoll" value="' . $polls->id . '">
            <input type="submit" value="Szavazat frissítése">
            </form>';
    } else {
        echo '<form action="vote_page.php" method="post" novalidate>
            <input type="hidden"  name="getPoll" value="' . $polls->id . '">
            <input type="submit" value="Szavazás">
            </form>';
    }
    if ($admin) {
        echo '<form action="delete_poll.php" method="post" novalidate>
            <input type="hidden"  name="getPoll" value="' . $polls->id . '">
            <input type="submit" value="Törlés">
            </form>';
    }
    echo "<br>";
}
echo "<h3>Lejárt kérdőívek</h3>";
foreach ($expired as $polls) {
    echo 'Azonosító: ' . $polls->id;
    echo "<br>";
    echo 'Létrehozva: ' . $polls->createdAt;
    echo "<br>";
    echo 'Határidő: ' . $polls->deadline;
    echo "<br>";
    echo '<form action="result.php" method="post" novalidate>
            <input type="hidden"  name="getPoll" value="' . $polls->id . '">
            <input type="submit" value="Eredmény megtekintése">
            </form>';

    if ($admin) {
        echo '<form action="delete_poll.php" method="post" novalidate>
            <input type="hidden"  name="getPoll" value="' . $polls->id . '">
            <input type="submit" value="Törlés">
            </form>';
    }
    echo "<br>";
}
?>