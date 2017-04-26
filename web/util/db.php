<?php

require_once __DIR__ . '/settings.php';
require __DIR__ . '/../../vendor/autoload.php';

$db_projects = new Projects();

class Projects
{
    private $projects;

    public function __construct()
    {
        global $db_name;
        $this->projects = (new MongoDB\Client)->selectDatabase($db_name)->projects;
    }

    public function new_project($path)
    {
        $project = ['path' => $path, 'date' => new MongoDB\BSON\UTCDateTime(), 'remote_client' => $_SERVER['REMOTE_ADDR']];

        $res = $this->projects->insertOne($project);

        return $res->getInsertedId();
    }

    public function get_project($id, $check_pw = true)
    {
        try {
            $oid = new MongoDB\BSON\ObjectID($id);
        } catch (Exception $e) {
            return [];
        }

        $project = $this->projects->findOne(['_id' => $oid]);
        if ($check_pw) {
            session_start();
            if (array_key_exists('password', $project) && strlen($project['password']) > 0) {
                $pass = '';
                if (isset($_REQUEST['pass']))
                    $pass = $_REQUEST['pass'];
                else if (isset($_SESSION['pass']))
                    $pass = $_SESSION['pass'];
                if (!password_verify($pass, $project['password'])) {
                    exit('incorrect password');
                }
            }
        }
        return $project;
    }

    public function save_project($proj)
    {
        $this->projects->replaceOne(['_id' => new MongoDB\BSON\ObjectID($proj['_id'])], $proj);
    }

    public function delete_project($id)
    {
        $this->projects->updateOne(['_id' => new MongoDB\BSON\ObjectID($id)], ['$set' => ['deleted' => true]]);
    }

    public function list_projects($page, $query)
    {
        global $projects_per_page;
        $query[] = ['deleted' => ['$exists' => false]];
        $criteria = ['$and' => $query];
        $options = [
            'sort' => ['date' => -1],
            'limit' => $projects_per_page,
            'skip' => ($page - 1) * $projects_per_page
        ];

        $count = $this->projects->count($criteria);
        $projects = $this->projects->find($criteria, $options);

        return [$count, $projects];
    }
}


function project_xml($project)
{
    $xml = new DOMDocument("1.0", "UTF-8");

    $xml->preserveWhiteSpace = false;
    $xml->formatOutput = true;

    $elProject = $xml->createElement('project');
    $xml->appendChild($elProject);

    if (isset($project['deleted'])) {
        $elProject->appendChild($xml->createElement('deleted'));
        return $xml->saveXML();
    }

    $elProject->appendChild($xml->createElement('id', $project['_id']));
    $elProject->appendChild($xml->createElement('user', $project['user']));
    $elProject->appendChild($xml->createElement('date', $project['date']->toDateTime()->format(DATE_W3C)));
    $elProject->appendChild($xml->createElement('description', $project['description']));
    if (strlen($project['password']) > 0)
        $elProject->appendChild($xml->createElement('has_password'));
    $elMedia = $xml->createElement('media');
    $elProject->appendChild($elMedia);
    foreach ($project['media'] as $id => $file) {
        $elFile = $xml->createElement('file');
        $elMedia->appendChild($elFile);
        $elFile->appendChild($xml->createElement('id', $id));
        $elFile->appendChild($xml->createElement('name', $file['original_name']));
        if (isset($file['file_path']))
            $elFile->appendChild($xml->createElement('succesfully_converted'));
        if (isset($file['trans_original_name'])) {
            $elFile->appendChild($xml->createElement('trans_name', $file['trans_original_name']));
            if (isset($file['trans_file_path']))
                $elFile->appendChild($xml->createElement('succesfully_normalized_transcription'));
        }
    }
    $elTools = $xml->createElement('tools');
    $elProject->appendChild($elTools);
    foreach ($project['tools'] as $tool_name => $tool) {
        if (empty($tool_name))
            continue;
        $elTool = $xml->createElement('tool');
        $elTool->setAttribute('name', $tool_name);
        $elTools->appendChild($elTool);
        if (isset($tool['error'])) {
            $elError = $xml->createElement('error', $tool['error']);
            $elTool->appendChild($elError);
        }
        foreach ($tool['files'] as $file) {
            $elFile = $xml->createElement('f', $file);
            $elTool->appendChild($elFile);
        }
    }


    return $xml->saveXML();

}