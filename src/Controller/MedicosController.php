<?php


namespace App\Controller;


use App\Entity\Medico;
use App\Factory\MedicoFactory;
use App\Helper\ExtractRequestData;
use App\Repository\MedicoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MedicosController extends BaseController
{

    /**
     * @var ExtractRequestData
     */
    private $extract;

    public function __construct(
        MedicoFactory $medicoFactory,
        EntityManagerInterface $entityManager,
        MedicoRepository $medicoRepository,
        ExtractRequestData $extract,
        CacheItemPoolInterface $cache,
        LoggerInterface $logger)
    {
        parent::__construct($medicoRepository,$entityManager,$medicoFactory,$extract,$cache,$logger);
        $this->extract = $extract;
    }



    /**
     * @Route("/especialidades/{especialidadeId}/medicos",methods={"GET"})
     */
    public function buscaPorEspecialidade(int $especialidadeId): Response
    {
        $medicos = $this->repository->findBy([
            'especialidade' => $especialidadeId
        ]);
        return new JsonResponse($medicos);
    }


    public function updateEntityExists($oldEntity,$newEntity)
    {
        $oldEntity->setCrm($newEntity->getCrm())
        ->setNome($newEntity->getNome())
        ->setEspecialidade($newEntity->getEspecialidade());
        return $oldEntity;
    }

    public function cachePrefix(): string
    {
        return 'medico_';
    }
}