<?php
require(__DIR__.'/settings.php');
require(__DIR__.'/db.php');

$tmp = tempnam($project_directory, "");
unlink($tmp);
mkdir($tmp);
mkdir($tmp . "/media");

$id = $db_projects->new_project($tmp);

header("Location: ../project.php?id=" . urlencode($id));