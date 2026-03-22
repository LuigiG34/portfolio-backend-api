<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PortfolioControllerTest extends WebTestCase
{
    public function testPortfolioRouteIsPublic(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/portfolio');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }

    public function testPortfolioRouteReturnsExpectedKeys(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/portfolio');

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('projects', $data);
        $this->assertArrayHasKey('experiences', $data);
        $this->assertArrayHasKey('skills', $data);
        $this->assertArrayHasKey('degrees', $data);
        $this->assertArrayHasKey('technologies', $data);
    }

    public function testPortfolioRouteReturnsJson(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/portfolio');

        $this->assertJson($client->getResponse()->getContent());
    }
}