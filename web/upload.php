<?php
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/../app/autoload.php';

$kernel = new \Ser\SerKernel('prod', false);
$kernel->loadClassCache();

$kernel->boot();
$container = $kernel->getContainer();

/** @var \OldSound\RabbitMqBundle\RabbitMq\Producer $producer */
$producer = $container->get('old_sound_rabbit_mq.upload_log_producer');

/** @var \Ser\Service\LogfileHandler $logfileHandler */
$logfileHandler = $container->get('logfileHandler');

if ($logfileHandler->addAmqpMessage($producer)) {
    $content = [
        'message' => 'Data successfully loaded',
    ];
    $code = 200;
} else {
    $content = [
        'message' => 'Ocured some errors.',
    ];
    $code = 400;
}

$response = new Response(json_encode($content), $code, ['Content-Type' => 'application/json']);
$response->send();


