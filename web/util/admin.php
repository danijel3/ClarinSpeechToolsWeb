<?php

session_start();
$_SESSION['admin_pw'] = $_REQUEST['pw'];

header('Location: ' . $_SERVER['HTTP_REFERER']);