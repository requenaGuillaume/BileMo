<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException($event): void
    {
        $exception = $event->getThrowable();

        if($exception instanceof HttpException){
            $statusCode = $exception->getStatusCode();
            $initialMessage = $exception->getMessage();

            if($statusCode === 404){
                $message = $this->getNotFoundMessage($initialMessage);
            }

            if($statusCode === 403){
                $message = $initialMessage;
            }

            $data = [
                'statusCode' => $statusCode,
                'message' => $message
            ];

            $event->setResponse(new JsonResponse($data));
        }else{
            $data = [
                'statusCode' => 500,
                'message' => 'Internal server error.'
            ];

            // TODO - Log the real error message ?

            $event->setResponse(new JsonResponse($data));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }

    private function getNotFoundMessage(string $initialMessage): string
    {
        $message = null;

        if(preg_match('(Company|User|Product)', $initialMessage)) {
            $entity = explode('"', explode('"App\\Entity\\', $initialMessage)[1])[0];
            $message = "$entity not found.";
        }

        if(!$message){
            $message = 'Resource not found.';
        }

        return $message;
    }
}
