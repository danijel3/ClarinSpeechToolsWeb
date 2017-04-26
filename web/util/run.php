<?php

function deleteDirectory($dir)
{
    system('rm -rf ' . escapeshellarg($dir), $retval);
    return $retval == 0; // UNIX commands return zero on success
}

require(__DIR__.'/db.php');
require(__DIR__.'/tasks.php');

$id = urldecode($_REQUEST['id']);
$task_name = urldecode($_REQUEST['task']);

if (empty($task_name)) {
    header("Location: ../project.php?id=" . urlencode($id));
    exit();
}

$project = $db_projects->get_project($id);

if (!array_key_exists('tools', $project))
    $project['tools'] = [];

if (isset($project['tools'][$task_name])) {
    if (!isset($_REQUEST['force'])) {
        header("Location: ../run_verify.php?id=" . urlencode($id) . "&task=" . urlencode($task_name));
        exit();
    }

    deleteDirectory($project['path'] . '/' . $task_name);

}

$project['tools'][$task_name] = (object)null;

$db_projects->save_project($project);

$task->speech_tool($id, $task_name);

header("Location: ../project.php?id=" . urlencode($id));