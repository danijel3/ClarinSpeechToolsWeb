<?php

require(__DIR__.'/db.php');

$id = urldecode($_REQUEST['id']);
$project = $db_projects->get_project($id);
$db_projects->delete_project($id);
header("Location: ../project.php?id=" . urlencode($id));