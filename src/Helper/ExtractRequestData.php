<?php


namespace App\Helper;


use Symfony\Component\HttpFoundation\Request;

class ExtractRequestData
{
    private function dataRequest(Request $request)
    {
        $queryString = $request->query->all();
        $infoOrder = array_key_exists('sort', $queryString)?
            $queryString['sort'] : null;
        unset($queryString['sort']);

        $pageAtual = array_key_exists('page',$queryString)?
            $queryString['page'] : 1;
        unset($queryString['page']);

        $itensQuant = array_key_exists('quantItens',$queryString)?
            $queryString['quantItens'] : 5;
        unset($queryString['quantItens']);

        return [$infoOrder,$queryString,$pageAtual,$itensQuant];
    }

    public function dataOrder(Request $request)
    {
        [$order,] = $this->dataRequest($request);
        return $order;
    }

    public function dataFilter(Request $request)
    {
        [,$filter] = $this->dataRequest($request);
        return $filter;
    }

    public function dataPage(Request $request)
    {
        [,,$page,$quant] = $this->dataRequest($request);
        return [$page,$quant];
    }

}
