<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{

    public function __construct(private UrlGeneratorInterface $urlGenerator, private LoggerInterface $logger)
    {
    }

    public function onKernelException($event): void
    {
        $requestUri = $event->getRequest()->getRequestUri();
        $exception = $event->getThrowable();
        $statusCode = $exception->getStatusCode();

        if(str_contains($requestUri, '/api/')){ 
            $initialMessage = $exception->getMessage();

            if($exception instanceof HttpException){
                $message = $this->getErrorMessage($statusCode, $initialMessage);
                $data = ['statusCode' => $statusCode, 'message' => $message];
                $event->setResponse(new JsonResponse($data));
            }else{
                $data = ['statusCode' => 500, 'message' => 'Internal server error.'];
                $this->logger->error("Code : $statusCode, message : $initialMessage");
                $event->setResponse(new JsonResponse($data));
            }
        }else{
            if($statusCode === 404){
                $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_documentation')));
            }
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

    private function getErrorMessage(int $statusCode, string $initialMessage): ?string
    {
        $message = null;

        if($statusCode === 404){
            $message = $this->getNotFoundMessage($initialMessage);
        }

        if($statusCode === 403){
            $message = $initialMessage;
        }

        if($statusCode === 405){
            $message = 'Method not allowed';
        }

        return $message;
    }
}
