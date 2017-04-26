<?php
require 'util/db.php';
$id = urldecode($_REQUEST['id']);
$project = $db_projects->get_project($id, false);
?>
<!doctype html>
<html>
<?php require('templates/head.html'); ?>
<body>
<?php require('templates/menu.html'); ?>
<div class="container" id="main-container">
    <?php require('templates/logo.html'); ?>

    <a href="main.php">Powrót na główną stronę</a>

    <h1>Projekt wymaga podania hasła</h1>

    <div class="panel panel-default" style="margin-bottom: 200px;">
        <div class="panel-body">
            <form action="project.php" method="post">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <div class="form-group">
                    <label for="user">Hasło</label>
                    <input type="password" class="form-control" id="pass" name="pass"">
                </div>
                <button type="submit" class="btn btn-primary">Otwórz</button>
            </form>
        </div>
    </div>

</div>
<?php require('templates/footer.html'); ?>
</body>
</html>