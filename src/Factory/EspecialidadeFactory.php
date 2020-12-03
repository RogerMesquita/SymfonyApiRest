<?php


namespace App\Factory;


use App\Entity\Especialidade;
use App\Entity\Factory;

class EspecialidadeFactory implements Factory
{
    public function create(String $json): Especialidade
    {
        $dataJson = json_decode($json);
        if(!property_exists($dataJson,'descricao')){
            throw new EntityFactoryException(
                'Especialidade precisa de descrição'
            );
        }

        $especialidade = new Especialidade();
        $especialidade->setDescricao($dataJson->descricao);
        return $especialidade;
    }
}