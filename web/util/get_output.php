<?php
require(__DIR__.'/db.php');

$id = urldecode($_REQUEST['id']);
$task = urldecode($_REQUEST['task']);
$filename = urldecode($_REQUEST['file']);

//TODO: emudb service
if ($filename == 'emuDB') {
    header('Location: index.php');
    exit();
}

$project = $db_projects->get_project($id);

$filepath = $project['path'] . '/' . $task . '/' . $filename;

if (!file_exists($filepath))
    exit();

header('Content-Type: text/plain');
header('Content-length: ' . filesize($filepath));
header('Content-Disposition: attachment; filename="' . $filename . '"');

readfile($filepath);
exit();