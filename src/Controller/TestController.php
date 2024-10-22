<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    #[Route('/test/{name}', name: 'app_test')]
    public function test(string $name): Response
    {
        return $this->render('test/test.html.twig', [
            'controller_name' => 'TestController',
            'name' => $name,
        ]);
    }



    #[Route('/test/{name}', name: 'bj')]
    public function bonjour(string $name): Response
    {
        return $this->render('test/test.html.twig', [
            'name' => $name,
        ]);
    }



}