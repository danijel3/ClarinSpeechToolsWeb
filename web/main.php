<!doctype html>
<html>
<?php require('templates/head.html'); ?>
<body>
<?php require('templates/menu.html'); ?>
<div class="container" id="main-container">
    <?php require('templates/logo.html'); ?>

    <h1>Narzędzia analizy mowy</h1>

    <div class="panel-body" style="height: 400px;">
        <div class="row main-index">
            <div class="col-md-6">
                <a href="util/new.php" class="btn btn-default" style="background-color: lightgreen">
                    <i class="fa fa-plus" aria-hidden="true"></i><br>
                    <span>Nowy projekt</span>
                </a>
            </div>
            <div class="col-md-6">
                <a href="list.php" class="btn btn-default" style="background-color: lightblue">
                    <i class="fa fa-search" aria-hidden="true"></i><br>
                    Wyszukaj projekt
                </a>
            </div>
        </div>
    </div>

</div>
<?php require('templates/footer.html'); ?>
</body>
</html>