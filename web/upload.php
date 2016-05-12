<?php
require_once __DIR__ . '/../app/autoload.php';

use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use OldSound\RabbitMqBundle\DependencyInjection\OldSoundRabbitMqExtension;
use OldSound\RabbitMqBundle\DependencyInjection\Compiler\RegisterPartsPass;

// Init container
$container = new ContainerBuilder();

// Register php-amqplib/rabbitmq-bundle
$container->registerExtension(new OldSoundRabbitMqExtension());
$container->addCompilerPass(new RegisterPartsPass());

// Load configs and compile container
$loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../app/config'));
$loader->load('config.yml');
$container->compile();


/** @var \OldSound\RabbitMqBundle\RabbitMq\Producer $producer */
$producer = $container->get('old_sound_rabbit_mq.upload_log_producer');

/** @var \Ser\Services\LogfileHandler $logfileHandler */
$logfileHandler = $container->get('logfileHandler');


if ($logfileHandler->addAmqpMessage($producer)) {
    $response = [
        'message' => 'Data successfully loaded',
    ];
} else {
    $response = [
        'message' => 'Ocured some errors.',
    ];
}

exit(json_encode($response));


