<?php
require(__DIR__.'/db.php');

$id = urldecode($_REQUEST['id']);
$fid = urldecode($_REQUEST['fid']);

$project = $db_projects->get_project($id);

if (!array_key_exists('file_path', $project['media'][$fid]))
    exit();

$filepath = $project['media'][$fid]['file_path'];
$filename = $project['media'][$fid]['download_name'];

header("Content-Transfer-Encoding: binary");
header('Content-Type: audio/wav');
header('Content-length: ' . filesize($filepath));
header('Content-Disposition: attachment; filename="' . $filename . '"');


readfile($filepath);
exit();