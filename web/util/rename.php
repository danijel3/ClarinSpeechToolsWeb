<?php

require(__DIR__.'/db.php');

$id = urldecode($_REQUEST['id']);
$project = $db_projects->get_project($id);

$project['user'] = $_REQUEST['user'];
$project['description'] = $_REQUEST['description'];

if (strlen($_REQUEST['password']) > 0)
    $project['password'] = password_hash($_REQUEST['password'], PASSWORD_DEFAULT);

if (isset($_REQUEST['delete_pass']))
    $project['password'] = '';

$db_projects->save_project($project);


header("Location: ../project.php?id=" . urlencode($id));