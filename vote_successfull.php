<?php
session_start();
if ($_SESSION['user']['username'] === 'admin') {
    echo "Az adminok nem szavazhatnak!<br>";
    echo '<a href="main.php">Vissza a főoldalra</a>';
} else {
    if (isset($_POST['getPoll'])) {

        require_once "jsonstorage.php";
        $jsonstorage = new JsonStorage("polls.json");
        $array = $jsonstorage->all();
        $cnt = 1;
        while ($cnt <= count($array[$_POST['getPoll']]->options) && !isset($_POST['solution' . $cnt])) {
            $cnt++;
        }
        if ($cnt > count($array[$_POST['getPoll']]->options) && !isset($_POST['solution'])) {
            echo $cnt;
            echo count($array[$_POST['getPoll']]->options);
            $_SESSION['getPoll'] = $_POST['getPoll'];
            header('Location:vote_page.php');
        } else {

            $solutions = [];
            for ($i = 0; $i <= count($array[$_POST['getPoll']]->options); $i++) {
                if (isset($_POST['solution' . $i])) {
                    $solutions[] = $_POST['solution' . $i];
                }
            }
            if (count($solutions) != 0 && count($solutions) != 3) {
                $_SESSION['getPoll'] = $_POST['getPoll'];
                $_SESSION['not_three'] = true;
                header('Location:vote_page.php');
            } else {
                echo "<br>";
                require_once "poll_class.php";
                echo '<p style="color: green;">Sikeres szavazás</p>';
                echo "----------------------<br>";
                $array1 = json_decode(json_encode($array[$_POST['getPoll']]->given_answers), true);
                for ($i = 1; $i <= count($array[$_POST['getPoll']]->options); $i++) {
                    if (isset($_POST['solution' . $i])) {
                        $array1[$_POST['solution' . $i]]++;
                    }
                }
                if (isset($_POST['solution']))
                    $array1[$_POST['solution']]++;
                echo "<br>";
                $id = $array[$_POST['getPoll']]->id;
                $question = $array[$_POST['getPoll']]->question;
                $options = $array[$_POST['getPoll']]->options;
                $isMultiple = $array[$_POST['getPoll']]->isMultiple;
                $deadLine = $array[$_POST['getPoll']]->deadline;
                $createdAt = $array[$_POST['getPoll']]->createdAt;
                $voters = $array[$_POST['getPoll']]->voters;
                $p = new Poll($question, $options, $isMultiple, $deadLine);
                $p->set_id($id);
                $p->set_given_answers($array1);
                $p->set_createdAt($createdAt);
                $p->set_voters($voters);
                if (isset($_SESSION['user']['username'])) {
                    if (!in_array($_SESSION['user']['username'], $voters, true)) {
                        $p->add_voters($_SESSION['user']['username']);
                    }
                    require_once 'user.php';
                    $ur = new UserRepository('users.json');
                    $object = $ur->all()[$_SESSION['_id']];
                    $answers = json_decode(json_encode($object->solutions), true);
                    if (array_key_exists($question, $answers)) {
                        foreach ($answers[$question] as $key => $value) {
                            $array1[$value]--;
                        }
                    }
                    $answer = isset($_POST['solution']) ? array($_POST['solution']) : $solutions;
                    $answers[$question] = $answer;
                    $object->solutions = $answers;
                    $ur->update_by_id($object);
                }
                $p->set_given_answers($array1);
                $jsonstorage->update_by_id($p);
                echo '<a href="main.php">Vissza a főoldalra</a>';
            }
        }
    } else {
        header('Location:main.php');
    }
}
