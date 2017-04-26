<?php

require(__DIR__.'/db.php');
require(__DIR__.'/tasks.php');

$id = urldecode($_REQUEST['id']);
$filename = $_FILES['mediaFile']['name'];
$info = pathinfo($filename);
$name = $info['filename'];
$ext = $info['extension'];

if (isset($_REQUEST['fid']))
    $fid = urldecode($_REQUEST['fid']);
else
    $fid = uniqid();

if (strlen($filename) > 0) {

    $project = $db_projects->get_project($id);

    if (!array_key_exists('media', $project))
        $project['media'] = [];

    if (array_key_exists($fid, $project['media'])) {
        if (file_exists($project['media'][$fid]['orig_path']))
            unlink($project['media'][$fid]['orig_path']);
        if (file_exists($project['media'][$fid]['file_path']))
            unlink($project['media'][$fid]['file_path']);
    }

    $input_media_path = $project['path'] . '/media/orig_' . $fid . '.' . $ext;

    move_uploaded_file($_FILES['mediaFile']['tmp_name'], $input_media_path);

    $project['media'][$fid]['original_name'] = $filename;
    $project['media'][$fid]['orig_path'] = $input_media_path;
    $project['media'][$fid]['download_name'] = $name . '.wav';

    $output_media_path = $project['path'] . '/media/' . $fid . '.wav';

    $task->ffmpeg($id, $fid, $input_media_path, $output_media_path);

    $db_projects->save_project($project);
}

header("Location: ../project.php?id=" . urlencode($id));