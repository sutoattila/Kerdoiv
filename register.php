<?php
require_once "auth.php";
session_start();
if (isset($_SESSION['user']) || isset($_SESSION['_id']))
    session_destroy();
$auth = new Auth();
function is_empty($input, $key)
{
    return !(isset($input[$key]) && trim($input[$key]) !== "");
}
function password_not_matching($input, $psw, $psw_again)
{
    return !($input[$psw] === $input[$psw_again]);
}
function validate($input, &$errors, $auth)
{

    if (is_empty($input, "username")) {
        $errors[] = "A felhasználónév megadása kötelező";
    }
    if (is_empty($input, "password")) {
        $errors[] = "A jelszó megadása kötelező";
    }
    if (is_empty($input, "password_again")) {
        $errors[] = "A jelszó ellenőrzése kötelező";
    }
    if (is_empty($input, "email")) {
        $errors[] = "Az email cím megadása kötelező";
    }
    if (isset($_POST['email']) && !is_empty($input, 'email') && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Helytelen formátumú email cím";
    }
    if (!is_empty($input, "password_again") && !is_empty($input, "password") && password_not_matching($input, 'password', 'password_again')) {
        $errors[] = "A két jelszó nem egyezik";
    }
    if (count($errors) == 0) {
        if ($auth->user_exists($input['username'])) {
            $errors[] = "A felhasználónév már foglalt";
        }
    }
    return !(bool) $errors;
}

$errors = [];
if (count($_POST) != 0) {
    if (validate($_POST, $errors, $auth)) {
        $id = $auth->register($_POST);
        session_start();
        $_SESSION['_id'] = $id;
        header('Location: login.php');
        exit();
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
    <h2>Regisztráció</h2>
    <?php if ($errors) { ?>
        <?php foreach ($errors as $error) { ?>
            <p style="color: red;"><?= '*' . $error ?></li>
            <?php } ?>
        <?php } ?>
            <form action="" method="post" novalidate>
                <label for="username">Felhasználónév: </label>
                <input id="username" name="username" type="text" value=<?= isset($_POST['username']) ? $_POST['username'] : "" ?>><br>
                <label for="email">Email cím: </label>
                <input id="email" name="email" type="text" value=<?= isset($_POST['email']) ? $_POST['email'] : "" ?>><br>
                <label for="password">Jelszó: </label>
                <input id="password" name="password" type="password" value=<?= isset($_POST['password']) ? $_POST['password'] : "" ?>><br>
                <label for="password_again">Jelszó újra: </label>
                <input id="password_again" name="password_again" type="password" value=<?= isset($_POST['password_again']) ? $_POST['password_again'] : "" ?>><br>
                <input type="submit" value="Regisztráció">
            </form>

            <a href="main.php">Főoldal</a>
</body>

</html>