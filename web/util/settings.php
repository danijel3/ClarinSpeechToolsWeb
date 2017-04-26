<?php

$project_directory = __DIR__.'/../../projects';
$db_name = "clarin";

$rmq_host = 'localhost';
$rmq_port = 5672;
$rmq_user = 'guest';
$rmq_pass = 'guest';

$projects_per_page = 10;

$admin_pw = 'admin_cookie';
#http://localhost:8080/admin.php?pw=admin_cookie

function activate_admin()
{
    global $admin_pw;
    return isset($_SESSION['admin_pw']) && $_SESSION['admin_pw'] == $admin_pw;
}