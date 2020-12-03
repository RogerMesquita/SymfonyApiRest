<?php


namespace App\Controller;


use App\Entity\Factory;
use App\Factory\ResponseFactory;
use App\Helper\ExtractRequestData;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectRepository;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    /**
     * @var ObjectRepository
     */
    protected $repository;
    /**
     * @var EntityManager
     */
    protected $entityManager;
    /**
     * @var Factory
     */
    protected $factory;
    /**
     * @var ExtractRequestData
     */
    private $extract;
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ObjectRepository $repository,
        EntityManager $entityManager,
        Factory $factory,
        ExtractRequestData $extract,
        CacheItemPoolInterface $cache,
        LoggerInterface $logger
        )
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->factory = $factory;
        $this->extract = $extract;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    public function findAll(Request $request):Response
    {
        $order = $this->extract->dataOrder($request);
        $filter = $this->extract->dataFilter($request);
        [$page,$itensPorPagina] = $this->extract->dataPage($request);
        $entityList = $this->repository->findBy(
            $filter,
            $order,
            $itensPorPagina,
            ($page - 1) * $itensPorPagina
        );

        $factoryResponse = new ResponseFactory(
            true,
            $entityList,
            200,
            $page,
            $itensPorPagina
        );


        return $factoryResponse->getResponse();
    }

    public function findId(int $id) : Response
    {

        $entity = $this->cache->hasItem($this->cachePrefix().$id) ?
           $this->cache->getItem($this->cachePrefix().$id)->get()
            : $this->repository->find($id);
        $statusCode = is_null($entity) ? Response::HTTP_NO_CONTENT : Response::HTTP_ACCEPTED;
        $factoryResponse = new ResponseFactory(
            true,
            $entity,
            $statusCode
        );
        return $factoryResponse->getResponse();
    }

    public function remove(int $id): Response
    {
        $entity = $this->repository->find($id);
        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        $this->cache->deleteItem($this->cachePrefix().$id);
        return new Response('',Response::HTTP_NO_CONTENT);
    }

    public function new(Request $request): Response
    {
        $dadosRequest = $request->getContent();

        $entidade = $this->factory->create($dadosRequest);

        $this->entityManager->persist($entidade);
        $this->entityManager->flush();

        $cacheItem = $this->cache->getItem($this->cachePrefix().$entidade->getId());
        $cacheItem->set($entidade);
        $this->cache->save($cacheItem);

        $this->logger->notice('Novo Registro de {entidade} adicionada em um ID: {id}',
        [
            'entidade' => get_class($entidade),
            'id' => $entidade->getId()
        ]
        );

        return new JsonResponse($entidade);
    }

    public function update(int $id,Request $request) : Response
    {
        $corpoRequisicao = $request->getContent();
        $entidadeEnviada = $this->factory->create($corpoRequisicao);
        try{
            $EntidadeExistente = $this->repository->find($id);

            if(is_null($EntidadeExistente)){
                return new Response('',Response::HTTP_NOT_FOUND);
            }

            $entity = $this->updateEntityExists($EntidadeExistente,$entidadeEnviada);

            $this->entityManager->flush();

            $cacheItem = $this->cache->getItem($this->cachePrefix().$id);
            $cacheItem->set($entity);
            $this->cache->save($cacheItem);

            $response = new ResponseFactory(
                true,
                $entidadeEnviada,
                Response::HTTP_OK
            );
            return $response->getResponse();

        }catch (\InvalidArgumentException $e){
            $response = new ResponseFactory(
                false,
                'Entity Not Found',
                Response::HTTP_NOT_FOUND
            );
            return $response->getResponse();
        }

    }

    abstract public function updateEntityExists($oldEntity,$newEntity);
    abstract public function cachePrefix() : string;

}