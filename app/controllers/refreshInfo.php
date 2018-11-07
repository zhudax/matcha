<?php

require "../models/Database.class.php";
require "../models/Refresh.class.php";

if (!isset($_SESSION))
    session_start();
if (!isset($_SESSION['flash']))
    $_SESSION['flash'] = [];
if (isset($_SESSION['uid']) && !empty($_SESSION['uid']) && isset($_POST['action']) &&
    !empty($_POST['action'])) {
    $db = new Database();
    $refresh = new Refresh();
    $uid = $_SESSION['uid'];
    $action = $_POST['action'];
    $refresh->init_val($db, $uid, $_POST);
    if ($action == "get_notif") {
        $_SESSION['numNotif'] = $refresh->get_notif();
        echo $_SESSION['numNotif'];
    }
    else if ($action == "read_notif") {
        $_SESSION['numNotif'] = 0;
        echo $refresh->read_notif();
    }
    else if ($action == "get_friends") {
        echo $refresh->get_friends();
    }
    else if ($action == "get_history") {
        echo $refresh->get_history();
    }
    else if ($action == "save_message") {
        echo $refresh->save_message();
    }
    else if ($action == "read_message") {
        echo $refresh->read_message();
    }
    else {
        $_SESSION['flash']['error'] = "Une erreur s'est passée!";
        echo "Error";
    }
}
else {
    $_SESSION['flash']['error'] = "Une erreur s'est passée!";
    echo "Error";
}
