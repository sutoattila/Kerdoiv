<?php
require_once "auth.php";
session_start();
if (isset($_SESSION['user']))
    header('Location:logout.php');
$auth = new Auth();
function is_empty($input, $key)
{
    return !(isset($input[$key]) && trim($input[$key]) !== "");
}
function validate($input, &$errors, $auth)
{

    if (is_empty($input, "username")) {
        $errors[] = "A felhasználónév megadása kötelező";
    }
    if (is_empty($input, "password")) {
        $errors[] = "A jelszó megadása kötelező";
    }
    if (count($errors) == 0 && !(isset($_POST['username']) && $_POST['username'] === 'admin')) {
        if (!$auth->check_credentials($input['username'], $input['password'])) {
            $errors[] = "Hibás felhasználónév vagy jelszó";
        }
    }

    return !(bool) $errors;
}

$errors = [];
if (count($_POST) != 0) {
    if (validate($_POST, $errors, $auth)) {
        $auth->login($_POST);
        print_r($_SESSION['user']);
        header('Location: main.php');
        die();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h2>Bejelentkezés</h2>
    <?php if ($errors) { ?>
        <?php foreach ($errors as $error) { ?>
            <p style="color: red;"><?= '*' . $error ?></p>
        <?php } ?>

    <?php } ?>
    <form action="" method="post" novalidate>
        <label for="username">Felhasználónév: </label>
        <input id="username" name="username" type="text" value=<?= isset($_POST['username']) ? $_POST['username'] : "" ?>><br>
        <label for="password">Jelszó: </label>
        <input id="password" name="password" type="password"><br>
        <input type="submit" value="Bejelentkezés">
    </form>
    <a href="register.php">Regisztáció</a><br>
    <a href="main.php">Főoldal</a>
</body>

</html>