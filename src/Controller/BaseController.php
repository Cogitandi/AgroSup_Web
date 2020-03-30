<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;

class BaseController extends AbstractController {

    /**
     * @Route("/", name="main")
     */
    public function main() {
        return $this->render('pages/main.html.twig', []);
    }

}
