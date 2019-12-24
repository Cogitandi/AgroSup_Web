<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Operator;


class BaseController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function number()
    {
        //$number = random_int(0, 100);
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('pages/main.html.twig',[]);


        /*return new Response(
            '<html><body>Lucky number: '. $this->render('lucky/number.html.twig', ['number' => $number]) .' and page'.$page.'</body></html>'
        );*/
    }

}