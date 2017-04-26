<?php
require(__DIR__.'/db.php');

$id = urldecode($_REQUEST['id']);
$fid = urldecode($_REQUEST['fid']);

$project = $db_projects->get_project($id);

if (!array_key_exists('trans_file_path', $project['media'][$fid]))
    exit();

$filepath = $project['media'][$fid]['trans_file_path'];
$filename = $project['media'][$fid]['trans_download_name'];

header('Content-Type: text/plain');
header('Content-length: ' . filesize($filepath));
header('Content-Disposition: attachment; filename="' . $filename . '"');

readfile($filepath);
exit();