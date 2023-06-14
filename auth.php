<?php
require_once "user.php";
class Auth
{
    private $userRepository;
    public function __construct()
    {
        $this->userRepository = new UserRepository('users.json');
    }
    public function register($user)
    {
        $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
        $user['password_again'] = password_hash($user['password_again'], PASSWORD_DEFAULT);
        $user['solutions'] = [];
        session_start();
        $_SESSION['_id'] = $user->_id;
        return $this->userRepository->insert((object) $user);
    }
    public function user_exists($username)
    {
        $users = $this->userRepository->filter(function ($user) use ($username) {
            return ((array) $user)['username'] === $username;
        });
        return count($users) >= 1;
    }
    public function login($user)
    {
        $_SESSION["user"] = $user;
    }
    public function check_credentials($username, $password)
    {
        $users = $this->userRepository->filter(function ($user) use ($username) {
            return ((array) $user)['username'] === $username;
        });
        if (count($users) === 1) {
            $user = (array) array_values($users)[0];
            return password_verify($password, $user["password"])
                ? $user
                : false;
        }
        return false;
    }
    public function is_authenticated()
    {
        return isset($_SESSION["user"]);
    }
    public function logout()
    {
        unset($_SESSION["user"]);
    }
}
