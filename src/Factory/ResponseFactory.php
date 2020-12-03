<?php


namespace App\Factory;


use Symfony\Component\HttpFoundation\JsonResponse;

class ResponseFactory
{
    /**
     * @var bool
     */
    private $sucesso;
    /**
     * @var int
     */
    private $paginaAtual;
    /**
     * @var int
     */
    private $itensPorPagina;
    private $contentResponse;
    /**
     * @var int
     */
    private $statusCode;

    public function __construct(
        bool $sucesso,
        $contentResponse,
        int $statusCode = 200,
        int $paginaAtual = null,
        int $itensPorPagina = null
      )
    {
        $this->sucesso = $sucesso;
        $this->paginaAtual = $paginaAtual;
        $this->itensPorPagina = $itensPorPagina;
        $this->contentResponse = $contentResponse;
        $this->statusCode = $statusCode;
    }


    public function getResponse() : JsonResponse{
        $response =[
            'sucesso' => $this->sucesso,
            'page' => $this->paginaAtual,
            'quantForPage' => $this->itensPorPagina,
            'content' => $this->contentResponse
        ];
        if(is_null($this->paginaAtual)){
            unset($response['page']);
            unset($response['quantForPage']);
        }
        return new JsonResponse($this->contentResponse,$this->statusCode);
    }

    public static function fromError(\Throwable $erro)
    {
        return new self(['mensagem' => $erro->getMessage()], false, Response::HTTP_INTERNAL_SERVER_ERROR, null);
    }

}