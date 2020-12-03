<?php


namespace App\Entity;


interface Factory
{
    public function create(string $json);
}