<?php
require_once 'settings.php';
require_once  __DIR__.'/../../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$task = new Task();

class Task
{
    private $connection;
    private $channel;

    public function __construct()
    {
        global $rmq_host, $rmq_port, $rmq_user, $rmq_pass;
        $this->connection = new AMQPStreamConnection($rmq_host, $rmq_port, $rmq_user, $rmq_pass);
        $this->channel = $this->connection->channel();
    }

    function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }


    public function ffmpeg($id, $fid, $input, $output)
    {
        $data = json_encode(['id' => $id, 'fid' => $fid, 'input' => $input, 'output' => $output]);

        $this->channel->queue_declare('ffmpeg', false, true, false, false);
        $msg = new AMQPMessage($data,
            array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
        );
        $this->channel->basic_publish($msg, '', 'ffmpeg');
    }

    public function normalize($id, $fid, $input, $output)
    {
        $data = json_encode(['id' => $id, 'fid' => $fid, 'input' => $input, 'output' => $output]);

        $this->channel->queue_declare('text_normalizer', false, true, false, false);
        $msg = new AMQPMessage($data,
            array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
        );
        $this->channel->basic_publish($msg, '', 'text_normalizer');
    }

    public function speech_tool($id, $task)
    {
        $data = json_encode(['id' => $id, 'task' => $task]);

        $this->channel->queue_declare('speech_tools', false, true, false, false);
        $msg = new AMQPMessage($data,
            array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
        );
        $this->channel->basic_publish($msg, '', 'speech_tools');
    }
}

