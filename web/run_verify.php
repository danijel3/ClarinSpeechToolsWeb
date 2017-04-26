<?php
$id = urldecode($_REQUEST['id']);
$task_name = $_REQUEST['task'];
?>
<!doctype html>
<html>
<?php require('templates/head.html'); ?>
<body>
<?php require('templates/menu.html'); ?>
<div class="container" id="main-container">
    <?php require('templates/logo.html'); ?>

    <h1>Zadanie już istnieje</h1>

    <div class="panel-body" style="height: 400px;">

        <p>Zadanie o nazwie <?php echo $task_name; ?> już istnieje w projekcie <?php echo $id; ?>.</p>

        <h2>Czy na pewno go chcesz usunąć?</h2>

        <a href="util/run.php?id=<?php echo $id; ?>&task=<?php echo $task_name; ?>&force">
            <button class="btn">Tak</button>
        </a>
        <a href="project.php?id=<?php echo $id; ?>">
            <button class="btn">Nie</button>
        </a>
    </div>
</div>
<?php require('templates/footer.html'); ?>
</body>
</html>