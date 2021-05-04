<?php

namespace App\Tests\Controller;

use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ResultsControllerTest extends WebTestCase
{
    public function testVisitingWhileNotLoggedIn()
    {
        $client = static::createClient();

        $client->request('GET', '/results/');
        $this->assertResponseRedirects();
    }

    public function testVisitingWhileLoggedInOnUser()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UsersRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('truc@truc121.com');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        // test e.g. the profile page
        $client->request('GET', '/results/');
        $this->assertResponseRedirects();
    }
    public function testVisitingWhileLoggedInOnAdmin()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UsersRepository::class);

        // retrieve the test admin
        $testUser = $userRepository->findOneByEmail('test@admin.fr');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        // test e.g. the profile page
        $client->request('GET', '/results/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Result');
    }
}
