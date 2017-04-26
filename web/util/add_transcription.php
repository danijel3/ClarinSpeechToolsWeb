<?php

require(__DIR__.'/db.php');
require(__DIR__.'/tasks.php');

$id = urldecode($_REQUEST['id']);

$project = $db_projects->get_project($id);

if (!isset($_REQUEST['fid']))
    exit('Missing fid!');

$fid = urldecode($_REQUEST['fid']);

if (!isset($project['media']))
    exit('Missing any media file!');

if (!isset($project['media'][$fid]))
    exit('Missing specific media file!');

if (isset($project['media'][$fid]['trans_orig_path']) && file_exists($project['media'][$fid]['trans_orig_path']))
    unlink($project['media'][$fid]['trans_orig_path']);
if (isset($project['media'][$fid]['trans_file_path']) && file_exists($project['media'][$fid]['trans_file_path']))
    unlink($project['media'][$fid]['trans_file_path']);


if (isset($_FILES['transcriptionFile'])) {

    $filename = $_FILES['transcriptionFile']['name'];
    $info = pathinfo($filename);
    $name = $info['filename'];
    $ext = $info['extension'];

    $input_media_path = $project['path'] . '/media/orig_' . $fid . '.' . $ext;

    if (strlen($filename) <= 0) {
        header("Location: ../project.php?id=" . urlencode($id));
        exit(0);
    }

    move_uploaded_file($_FILES['transcriptionFile']['tmp_name'], $input_media_path);

    $project['media'][$fid]['trans_original_name'] = $filename;
    $project['media'][$fid]['trans_orig_path'] = $input_media_path;
    $project['media'][$fid]['trans_download_name'] = $name . '.txt';

} else if (isset($_REQUEST['transcription'])) {

    if (strlen($_REQUEST['transcription']) <= 0) {
        header("Location: ../project.php?id=" . urlencode($id));
        exit(0);
    }

    $input_media_path = $project['path'] . '/media/orig_' . $fid . '.txt';

    file_put_contents($input_media_path, $_REQUEST['transcription']);

    $project['media'][$fid]['trans_original_name'] = 'text_field';
    $project['media'][$fid]['trans_orig_path'] = $input_media_path;
    $project['media'][$fid]['trans_download_name'] = 'input.txt';

} else {
    header("Location: project.php?id=" . urlencode($id));
    exit(0);
}

$output_media_path = $project['path'] . '/media/' . $fid . '.txt';

$task->normalize($id, $fid, $input_media_path, $output_media_path);

$db_projects->save_project($project);

header("Location: ../project.php?id=" . urlencode($id));