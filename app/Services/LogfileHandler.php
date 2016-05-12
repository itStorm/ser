<?php
namespace Ser\Services;

use Symfony\Component\HttpFoundation\Request;
use OldSound\RabbitMqBundle\RabbitMq\Producer;

/**
 * Class LogfileHandler
 * @package App\Services
 */
class LogfileHandler
{
    const MIME_TYPE = 'application/zip';

    /**
     * @param Producer $producer
     * @return bool
     */
    public function addAmqpMessage(Producer $producer)
    {
        $request = Request::createFromGlobals();
        $mimeType = $request->get('report_content_type');
        $filePath = $request->get('report_path');


        if (!$request->isMethod('POST') || $mimeType != self::MIME_TYPE || !$filePath) {
            return false;
        }

        $message = [
            'path' => $filePath,
        ];

        $producer->publish(serialize($message));

        return true;
    }
}