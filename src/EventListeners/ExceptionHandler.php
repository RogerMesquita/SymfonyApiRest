<?php


namespace App\EventListeners;


use App\Factory\EntityFactoryException;
use App\Factory\ResponseFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;


class ExceptionHandler implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {

        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
       return [
           KernelEvents::EXCEPTION => [
               ['handleEntityException',1],
               ['handle404Exception', 0],
               ['handleGenericException',-1],
           ],
       ];
    }

    public function handle404Exception(ExceptionEvent $event)
    {
        if($event->getThrowable() instanceof NotFoundHttpException){
            $response = ResponseFactory::fromError($event
                ->getThrowable())->getResponse();
            $response->setStatusCode(404);
            $event->setResponse($response);
        }
    }

    public function handleEntityException(ExceptionEvent $event)
    {
        if($event->getThrowable() instanceof EntityFactoryException){
         $response =  ResponseFactory::fromError($event
             ->getThrowable())->getResponse();
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $event->setResponse($response);
        }
    }

    public function handleGenericException(ExceptionEvent $event)
    {
        $this->logger->critical('Uma Exceçãoocorreu. {stack}',[
            'stack' => $event->getThrowable()->getTraceAsString()
        ]);
        $response = ResponseFactory::fromError($event->getThrowable());
        $event->setResponse($response->getResponse());
    }
}
