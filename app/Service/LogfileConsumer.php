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
    /** @var string */
    protected $sentryDsn;

    /**
     * LogfileConsumer constructor.
     * @param string $sentryDsn
     */
    public function __construct($sentryDsn)
    {
        $this->sentryDsn = $sentryDsn;
    }

    /** @inheritdoc */
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
        $errors = [];
        $prevLines = '';        // Line before
        $error = null;          // Error text
        $countErrorStrings = 0; // Error strings count
        $osVersion = null;      // Detected OS

        while (($currLine = fgets($logResource)) !== false) {
            if (preg_match('/^descr\s+:\s+.*$/', $currLine)) {  // Look for error START
                $error = $prevLines . $currLine;
                $countErrorStrings = 2;
            } elseif ($error) {                             // Collect error strings
                $error .= $currLine;
                $countErrorStrings++;
            }

            // Detect error END
            if ($error && (preg_match('/^line\s{2}:\s+.*$/', $currLine) || $countErrorStrings > 9)) {
                $errors[] = $error;
                $error = null;
                $countErrorStrings = 0;
            }

            $prevLines = $currLine;

            if ($osVersion) {
                continue;
            } elseif (preg_match('/^\s+OSVersion\s+=\s+\'(\w+)\s/', $currLine, $matches)) {
                $osVersion = $matches[1];
            }
        }

        fclose($logResource);

        foreach ($errors as $error) {
            $errCode = '';
            if (preg_match('/code\s+:\s+(\d+)/', $error, $matches)) {
                $errCode = $matches[1];
            } else {
                if (preg_match('/file\s+:\s+([^\s]+)\S/', $error, $matches)) {
                    $errCode .= $matches[1];
                }
                if (preg_match('/descr\s+:\s+(.*)\s/', $error, $matches)) {
                    $errCode .= $matches[1];
                }
                $errCode = md5($errCode);
            }

            // Message format
            $template = <<<TEXT
Code: {$errCode}
Platform: %s
Error: %s
TEXT;
            // Send error to Sentry
            $client = new \Raven_Client($this->sentryDsn);
            $client->captureMessage($template, [
                $osVersion,
                $error,
            ], [
                'tags' => [
                    'platform' => $osVersion,
                ],
            ]);
        }

        unlink($message['path']);

        return self::MSG_ACK;
    }
}