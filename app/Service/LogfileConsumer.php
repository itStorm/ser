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
        $message = unserialize($amqpMessage->getBody());
        var_dump($message);
        return 0;
    }
}