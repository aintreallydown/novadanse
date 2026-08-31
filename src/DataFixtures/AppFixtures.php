<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Uid\Uuid;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;
    private HttpClientInterface $client;

    public function __construct(UserPasswordHasherInterface $hasher, HttpClientInterface $client)
    {
        $this->hasher = $hasher;
        $this->client = $client;
    }

    public function load(ObjectManager $manager): void
    {

        /* Admin USERS */

        /* - Admin */
        $user = new User();
        $user
            ->setPrenom('Geoffrey')
            ->setNom('Flavigny')
            ->setUid(Uuid::v4())
            ->setTelephone('000000000')
            ->setDateOfBirth(new \DateTimeImmutable('1990-01-01'))
            ->setAddress('3 rue de la Lion, Estrées-Saint-Denis')
            ->setEmail('flavignygeoffrey@gmail.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->hasher->hashPassword($user, 'NovaDanse!2026'));


        $manager->persist($user);


        /* Flush */

        $manager->flush();
    }
}
