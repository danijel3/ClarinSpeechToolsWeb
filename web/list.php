<?php
require 'util/db.php';
require_once 'util/settings.php';

session_start();

if (isset($_REQUEST['reset'])) {
    unset($_SESSION['filter_user']);
    unset($_SESSION['filter_description']);
    unset($_SESSION['filter_afterdate']);
    unset($_SESSION['filter_beforedate']);
}

function get($req)
{
    global $_REQUEST, $_SESSION;
    if (isset($_REQUEST[$req])) {
        $_SESSION['filter_' . $req] = $_REQUEST[$req];
        return $_REQUEST[$req];
    }
    if (isset($_SESSION['filter_' . $req]))
        return $_SESSION['filter_' . $req];
    return '';
}

$user = get('user');
$description = get('description');
$afterdate = get('afterdate');
$beforedate = get('beforedate');

if (isset($_REQUEST['page']))
    $currpage = $_REQUEST['page'];
else $currpage = 1;

?>
<!doctype html>
<html>
<?php require('templates/head.html'); ?>
<body>
<?php require('templates/menu.html'); ?>
<div class="container" id="main-container">
    <?php require('templates/logo.html'); ?>

    <a href="main.php">Powrót do głównej strony</a>

    <h1>Lista projektów</h1>

    <div class="panel-group">
        <div class="panel panel-default">

            <div class="panel-heading">
                <a data-toggle="collapse" href="#collapse-filter">
                    <div class="panel-title">
                        Kryteria wyszukiwania
                        <span class="help"> (kliknij żeby rozwinąć/zwinąć)</span>
                    </div>
                </a>
            </div>


            <div class="panel-collapse collapse" id="collapse-filter">
                <div class="panel-body">
                    <form action="list.php" method="post">
                        <div class="form-group">
                            <label for="description">Nazwa projektu</label>
                            <input type="text" class="form-control" id="description" name="description"
                                   value="<?php echo $description; ?>">
                        </div>
                        <div class="form-group">
                            <label for="user">Użytkownik</label>
                            <input type="text" class="form-control" id="user" name="user" value="<?php echo $user; ?>">
                        </div>
                        <div class="form-group">
                            <label for="afterdate">Po dacie</label>
                            <input type="date" class="form-control" id="afterdate" name="afterdate"
                                   value="<?php echo $afterdate; ?>">
                        </div>
                        <div class="form-group">
                            <label for="beforedate">Przed datą</label>
                            <input type="date" class="form-control" id="beforedate" name="beforedate"
                                   value="<?php echo $beforedate; ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Szukaj</button>
                    </form>
                    <div style="margin-top:10px">
                        <a href="list.php?reset">
                            <button class="btn">Resetuj</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <table class="table">
        <thead class="thead-inverse">
        <tr>
            <th>ID</th>
            <th>Nazwa</th>
            <th>Użytkownik</th>
            <th>Data</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $query = [];
        if (strlen($user) > 0) $query[] = ['user' => new MongoDB\BSON\Regex($user)];
        if (strlen($description)) $query[] = ['description' => new MongoDB\BSON\Regex('.*' . $description . '.*')];
        if (strlen($afterdate)) $query[] = ['date' => ['$gte' => new MongoDB\BSON\UTCDateTime(new DateTime($afterdate))]];
        if (strlen($beforedate)) $query[] = ['date' => ['$lte' => new MongoDB\BSON\UTCDateTime(new DateTime($beforedate))]];


        $res = $db_projects->list_projects($currpage, $query);
        $pagecount = (int)($res[0] / $projects_per_page);
        $projects = $res[1];
        ?>
        <?php foreach ($projects as $project): ?>
            <tr>
                <td>
                    <?php
                    $id = $project['_id'];
                    ?>
                    <a href="project.php?id=<?php echo $id; ?>"><?php echo $id; ?></a>
                </td>
                <td>
                    <?php if (array_key_exists('description', $project))
                        echo $project['description']; ?>
                </td>
                <td>
                    <?php if (array_key_exists('user', $project))
                        echo $project['user']; ?>
                </td>
                <td>
                    <?php
                    if (array_key_exists('date', $project))
                        echo $project['date']->toDateTime()->format('Y-m-d H:i:s');
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <ul class="pagination">
        <?php
        foreach (range(1, $pagecount + 1) as $page) {
            if ($currpage == $page)
                echo '<li class="active">';
            else
                echo '<li>';
            echo '<a href="list.php?page=' . $page . '">';
            echo $page;
            echo '</a>';
            echo '</li>';
        }
        ?>
    </ul>

</div>
<?php require('templates/footer.html'); ?>
</body>
</html>