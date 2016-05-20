<?php
namespace Ser\Service;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

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
        $prevLines = '';        // Line before
        $error = null;          // Error text
        $countErrorStrings = 0; // Error strings count

        while (($currLine = fgets($logResource)) !== false) {
            if (preg_match('/^descr : .*$/', $currLine)) {
                $error = $prevLines . $currLine;
                $countErrorStrings = 2;
            } elseif ($error) {
                $error .= $currLine;
                $countErrorStrings++;
            }

            if ($error && (preg_match('/^line  : .*$/', $currLine) || $countErrorStrings > 9)) {
                // @todo заменить echo на отправку ошибки в Sentry
                echo $error;
                $error = null;
                $countErrorStrings = 0;
            }
            $prevLines = $currLine;
        }

        fclose($logResource);
        unlink($message['path']);

        return self::MSG_ACK;
    }
}