<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MedicoRepository")
 */
class Medico implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="integer")
     */
    private $crm;
    /**
     * @ORM\Column(type="string")
     */
    private $nome;

    /**
     * @ORM\ManyToOne(targetEntity=Especialidade::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $especialidade;


    public function getId(): ?int
    {
        return $this->id;
    }



    public function getCrm(): ?int
    {
        return $this->crm;
    }

    public function setCrm(int $crm): self
    {
        $this->crm = $crm;
        return $this;
    }


    public function getNome(): ?string
    {
        return $this->nome;
    }


    public function setNome(string $nome): self
    {
        $this->nome = $nome;
        return $this;
    }


    public function getEspecialidade(): ?Especialidade
    {
        return $this->especialidade;
    }

    public function setEspecialidade(?Especialidade $especialidade): self
    {
        $this->especialidade = $especialidade;

        return $this;
    }

    public function jsonSerialize()
    {
       return [
           'id' => $this->getId(),
           'nome' => $this->getNome(),
           'crm' => $this->getCrm(),
           'especialidadeId' => $this->getEspecialidade()->getId(),
           '_links' => [
               [
                   'rel' => 'self',
                   'path' => '/medicos/'.$this->getId()
               ],
               [
                   'rel' => 'especialidade',
                   'path' => '/especialidades/'. $this->getEspecialidade()->getId()
               ]
           ]
       ];
    }
}