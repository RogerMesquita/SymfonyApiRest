<?php


namespace App\Factory;


use App\Entity\Factory;
use App\Entity\Medico;
use App\Repository\EspecialidadeRepository;


class MedicoFactory implements Factory
{
    /**
     * @var EspecialidadeRepository
     */
    private $especialidadeRepository;

    public function __construct(EspecialidadeRepository $especialidadeRepository)
    {

        $this->especialidadeRepository = $especialidadeRepository;
    }

    public function create(string  $json) : Medico
    {
        $dadoEmJson = json_decode($json);
        $this->checkAllProperties($dadoEmJson);

        $especialidadeId = $dadoEmJson->especialidade;
        $especialidade = $this->especialidadeRepository->find($especialidadeId);
        $medico = new Medico();
        $medico->setCrm($dadoEmJson->crm)
        ->setNome($dadoEmJson->nome)
        ->setEspecialidade($especialidade);

        return $medico;
    }


    private function checkAllProperties(object $dadoEmJson): void
    {
        if (!property_exists($dadoEmJson, 'name')){
            throw new EntityFactoryException('name incorreto');
        }
        if(!property_exists($dadoEmJson, 'crm')){
            throw new EntityFactoryException('Crm incorreto');
       }
        if(!property_exists($dadoEmJson, 'especialidadeId')) {
            throw new EntityFactoryException('especialidadeId incorreto');
        }
    }
}
