<?php
require 'util/db.php';

if (isset($_REQUEST['xml'])) {
    $id = urldecode($_REQUEST['id']);
    $project = $db_projects->get_project($id);
    header('Content-Type: text/xml');
    echo project_xml($project);
    exit();
}

$id = urldecode($_REQUEST['id']);
$project = $db_projects->get_project($id, false);

if (array_key_exists('deleted', $project) && $project['deleted']) {
    header("Location: deleted.php");
    exit();
}

session_start();
if (array_key_exists('password', $project) && strlen($project['password']) > 0) {
    $pass_state = true;

    $pass = '';
    if (isset($_REQUEST['pass']))
        $pass = $_REQUEST['pass'];
    else if (isset($_SESSION['pass']))
        $pass = $_SESSION['pass'];

    if (!password_verify($pass, $project['password'])) {
        header("Location: authorize.php?id=" . $id);
        exit();
    }
    $_SESSION['pass'] = $pass;
} else
    $pass_state = false;

$user = $project['user'];
$description = $project['description'];

if (strlen($description) > 0) {
    $name = $description;
} else {
    $name = 'bez nazwy';
}
//phpinfo();
?>
<!doctype html>
<html>
<?php require('templates/head.html'); ?>

<script src="js/project_utils.js"></script>

<body>
<?php require('templates/menu.html'); ?>
<div class="container" id="main-container">
    <?php require('templates/logo.html'); ?>

    <a href="main.php">Powrót do głównej strony</a>

    <h1>Projekt "<?php echo $name; ?>"</h1>

    <ul class="nav nav-tabs nav-justified" role="tablist">
        <li role="presentation" class="active">
            <a href="#properties" data-toggle="tab">
                <i class="fa fa-key"></i> Właściwości
            </a>
        </li>
        <li role="presentation">
            <a href="#files" data-toggle="tab">
                <i class="fa fa-files-o" aria-hidden="true"></i> Pliki
            </a>
        </li>
        <li role="presentation">
            <a href="#tools" data-toggle="tab">
                <i class="fa fa-cogs" aria-hidden="true"></i> Narzędzia
            </a>
        </li>
    </ul>

    <div class="tab-content">

        <div role="tabpanel" class="active tab-pane fade in" id="properties">
            <p>Tutaj możesz ustawić niektóre ustawienia dla swojego projektu. Nazwa użytkownika i opis pomogą w
                wyszukiwaniu, a hasło uniemożliwi osobom niepowołanym dostępu do danych. Jak skończysz tutaj,
                przejdź do dodawania plików kilkając na zakładkę powyżej.</p>

            <div class="row">
                <div class="col-md-6">
                    <form action="util/rename.php" method="post">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <div class="form-group">
                            <label for="description">Nazwa projektu</label>
                            <input type="text" class="form-control" id="description" name="description"
                                   value="<?php echo $description; ?>">
                        </div>
                        <div class="form-group">
                            <label for="user">Użytkownik</label>
                            <input type="text" class="form-control" id="user" name="user"
                                   value="<?php echo $user; ?>">
                        </div>
                        <div class="form-group">
                            <label for="password">Hasło</label>
                            <small>(Zostaw puste żeby nie zmieniać!)</small>
                            <input type="password" class="form-control" id="password" name="password"">
                            <small><?php
                                if ($pass_state) {
                                    echo 'Hasło jest ustawione! ';
                                    echo '<input type="checkbox" name="delete_pass" class="form-check-input"> Usuń';
                                } else {
                                    echo 'Hasło nie jest ustawione!';
                                }
                                ?></small>
                        </div>
                        <button type="submit" class="btn btn-primary form-control">Zmień</button>
                    </form>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Link do projektu</label>
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button class="btn btn-secondary" type="button"
                                        onclick="do_copy('project_link')">Kopiuj</button>
                            </span>
                            <input class="form-control" type="text" id="project_link"
                                   value="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Pobierz XML</label> <br>
                        <a href="project.php?id=<?php echo $id ?>&xml" class="btn btn-info">XML</a>
                    </div>

                    <div class="form-group">
                        <label>Usuń ten projekt</label> <br>
                        <a href="util/delete.php?id=<?php echo $id; ?>"
                           onclick="return confirm('Czy na pewno?')"
                           class="btn btn-danger"><i class="fa fa-trash-o" aria-hidden="true"></i> Usuń</a>
                    </div>
                </div>
            </div>
        </div>


        <div role="tabpanel" class="tab-pane fade" id="files">

            <p>Ta zakładka służy do dodawania plików do projektu. Każdy plik z nagraniem może posiadać opcjonalnie
                transkrypcję. Jak skończysz dodawać pliki, przejdź do narzędzi klikając ostatnią zakładę powyżej.</p>


            <div class="row">
                <div class="col-md-6">

                    <h2><i class="fa fa-file-audio-o" aria-hidden="true"></i>
                        <small>Nagranie</small>
                    </h2>

                    <div class="form-group">
                        <form action="util/add_media.php" method="post" enctype="multipart/form-data">
                            <input type="file" class="form-control" name="mediaFile">
                            <button type="submit" class="btn btn-primary form-control">Wgraj plik z nagraniem
                            </button>
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <input type="hidden" name="fid" value="default">
                        </form>
                    </div>

                    <?php if (isset($project['media']['default']['file_path'])): ?>
                        <div class="form-group">
                            <p>Wgrano plik o nazwie
                                <strong><?php echo $project['media']['default']['original_name']; ?></strong></p>
                            <audio controls preload="none" style="width: 100%">
                                <source id="audio-source"
                                        src="util/get_media.php?id=<?php echo $id; ?>&fid=default">
                                Cannot play audio!
                            </audio>
                        </div>
                    <?php else: ?>
                        <div class="form-group">
                            <p>Jeszcze nie wgrano pliku do projektu</p>
                        </div>
                    <?php endif; ?>

                </div>

                <div class="col-md-6">

                    <h2><i class="fa fa-file-text-o" aria-hidden="true"></i>
                        <small>Transkrypcja</small>
                    </h2>

                    <div class="form-group">


                        <form action="util/add_transcription.php" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <input type="file" class="form-control" name="transcriptionFile">
                                <button type="submit" class="btn btn-primary form-control">Wgraj plik z transkrypcją
                                </button>
                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                <input type="hidden" name="fid" value="default">
                            </div>
                        </form>
                    </div>

                    <div class="form-group">
                        <?php if (isset($project['media']['default']['trans_file_path'])): ?>
                            <p>Wgrano plik o nazwie
                                <strong><?php echo $project['media']['default']['trans_original_name']; ?></strong>
                            </p>
                        <?php else: ?>
                            <p>Jeszcze nie wgrano pliku do projektu</p>
                        <?php endif; ?>

                        <form action="util/add_transcription.php" method="post">
                            <textarea class="form-control" name="transcription"><?php
                                if (isset($project['media']['default']['trans_file_path'])) {
                                    //readfile($project['media']['default']['trans_file_path']);
                                    $handle = fopen($project['media']['default']['trans_file_path'], "r");
                                    $contents = fread($handle, 10000);
                                    fclose($handle);
                                    echo $contents;
                                }
                                ?></textarea>
                            <button type="submit" class="btn btn-primary form-control">Aktualizuj transkrypcję
                            </button>
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <input type="hidden" name="fid" value="default">
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div role="tabpanel" class="tab-pane fade" id="tools">

            <p>Tutaj można uruchamiać narzędzia na plikach dodanych w poprzedniej zakładce. Wyniki działania narzędzi
                będą wyświetlone w tabelce poniżej.</p>


            <div class="form-group">
                <h3>Wybierz narzędzie</h3>
                <form action="util/run.php" method="post">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="task" value="forcealign" checked>
                            Force align
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="task" value="segmentalign">
                            Segment align
                        </label>
                    </div>
                    <button class="btn btn-primary" style="width: 200px" type="submit">Uruchom</button>
                </form>
            </div>

            <div class="form-group">
                <h3>Wyniki działania narzędzi</h3>

                <table class="table">
                    <thead class="thead-inverse">
                    <tr>
                        <th class="col-md-2">Narzędzie</th>
                        <th>Pliki do pobrania</th>
                        <th>Usługi</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (isset($project['tools']))
                        foreach ($project['tools'] as $tool => $output): ?>
                            <?php
                            if (empty($tool)) continue;
                            $has_emudb = false;
                            ?>
                            <tr>
                                <td><?php echo $tool ?></td>
                                <?php if (isset($output['error'])): ?>
                                    <td>błąd: <?php echo $output['error'] ?></td>
                                <?php elseif (!isset($output['files'])): ?>
                                    <td><i>w trakcie przetwarzania...</i></td>
                                <?php else: ?>
                                    <td>
                                        <?php foreach ($output['files'] as $file): ?>
                                            <a href="util/get_output.php?id=<?php echo $id; ?>&task=<?php echo $tool; ?>&file=<?php echo $file; ?>"><?php echo $file; ?></a>
                                            <?php
                                            if ($file == 'emuDB.zip' || $file == 'emuDB')
                                                $has_emudb = true;
                                            ?>
                                        <?php endforeach; ?>
                                    </td>
                                <?php endif; ?>
                                <td>
                                    <?php if ($has_emudb): ?>
                                        <?php
                                        $server_url = urlencode('ws://mowa.clarin-pl.eu:17890/' . $id . '/' . $tool);
                                        ?>
                                        <a href="http://ips-lmu.github.io/EMU-webApp/?autoConnect=true&serverUrl=<?php echo $server_url; ?>">EMU-webApp</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <?php if (activate_admin()): ?>
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.11.0/styles/default.min.css">
        <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.11.0/highlight.min.js"></script>
        <script>hljs.initHighlightingOnLoad();</script>
        <div class="panel panel-default">
            <div class="panel-body">
                <pre><code><?php echo json_encode($project, JSON_PRETTY_PRINT); ?></code></pre>
            </div>
        </div>
    <?php endif; ?>

</div>
<?php require('templates/footer.html'); ?>
</body>
</html>
