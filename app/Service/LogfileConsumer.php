<?php
namespace Ser\Service;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class LogfileConsumer
 * @package App\Services
 */
class LogfileConsumer implements ConsumerInterface
{
    public function execute(AMQPMessage $amqpMessage)
    {
        $zipArchive = new \ZipArchive();
        $message = unserialize($amqpMessage->getBody());


        if (!isset($message['path'])) {
            echo "Bad message: {$amqpMessage->getBody()}\n";

            return self::MSG_REJECT;
        }

        if ($zipArchive->open($message['path']) !== true) {
            echo "Bad archive: {$amqpMessage->getBody()}\n";

            return self::MSG_REJECT;
        }

        if (!($logResource = $zipArchive->getStream('monitor.log'))) {
            echo "Can't get file handler for monitor.log\n";

            return self::MSG_REJECT;
        }

        // Read file
        while (($buffer = fgets($logResource)) !== false) {

            echo 'String: ' . $buffer;
        }

        fclose($logResource);
        unlink($message['path']);

        return self::MSG_ACK;
    }
}