<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FileController extends AbstractController
{
    /**
     * @Route("/file", name="file")
     */
    public function index()
    {
        return $this->render('file/index.html.twig', [
            'controller_name' => 'FileController',
        ]);
    }
}
