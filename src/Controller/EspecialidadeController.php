<?php

namespace App\Controller;


use App\Entity\Especialidade;
use App\Factory\EspecialidadeFactory;
use App\Helper\ExtractRequestData;
use App\Repository\EspecialidadeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EspecialidadeController extends BaseController
{
    /**
     * @var ExtractRequestData
     */
    private $extract;

    public function __construct(
        EntityManagerInterface $entityManager,
        EspecialidadeRepository $repository,
        EspecialidadeFactory $factory,
        ExtractRequestData $extract,
        CacheItemPoolInterface $cache,
        LoggerInterface $logger)
    {
        parent::__construct($repository,$entityManager,$factory,$extract,$cache,$logger);
        $this->extract = $extract;
    }

    public function updateEntityExists($oldEntity,$newEntity)
    {
        $oldEntity->setDescricao($newEntity->getDescricao());
        return $oldEntity;
    }

    /**
     * @Route("/especionalidades_html")
     */
    public function especialidadesEmHtml()
    {
        $especialidades = $this->repository->findAll();
        return $this->render('especialidades.html.twig',[
            'especialidades' => $especialidades
        ]);
    }

    public function cachePrefix(): string
    {
        return 'especilidade_';
    }
}
