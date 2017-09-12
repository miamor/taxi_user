<?php
include 'config.php';
//if (!$_SESSION['taxi']) {
    include 'objects/login.php';
    $login = new Login();

    $login->username = isset($_POST['username']) ? $_POST['username'] : null;
    $login->password = isset($_POST['password']) ? $_POST['password'] : null;

//    echo $_POST['username'];

    if ($login->username && $login->password) {
        $do = $login->login();
        if ($do) {
            $_SESSION['taxi'] = $login->id;
        }
        echo ($do ? json_encode($login->taxiInfo, JSON_UNESCAPED_UNICODE) : 0);
    } else echo -1;
//} else echo -2;
