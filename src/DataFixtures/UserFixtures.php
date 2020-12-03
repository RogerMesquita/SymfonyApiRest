<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('usuario')
            ->setPassword('$argon2id$v=19$m=65536,t=4,p=1$NG5zSVo4R0Z2emhDMEdhWQ$01pMqRGpcC2S4+c0zp9howxCJV/EZ40JURpxp55hMY4');
        // $product = new Product();
         $manager->persist($user);

        $manager->flush();
    }
}
