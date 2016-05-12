<?php
namespace Ser\Services;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class LogfileConsumer
 * @package App\Services
 */
class LogfileConsumer implements ConsumerInterface
{
    public function execute(AMQPMessage $msg)
    {
        var_dump('Process message', $msg);
    }
}