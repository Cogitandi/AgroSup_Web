<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Operator;
use App\Form\AddOperatorFormType;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OperatorController extends AbstractController {

    /**
     * @Route("/operator", name="operator")
     */
    public function index(Request $request) {
        $user = $this->getUser();
        $operator = new Operator();

        $operators = $user->getOperators();

        $parameters = [
            'operator' => $operators
        ];

        return $this->render('operator/index.html.twig', $parameters);
    }

    /**
     * @Route("/operator/delete/{id}", name="delete")
     */
    public function deleteOperator($id) {
        $user = $this->getUser();
        // czy operator nalezy do uzytkownika
        $operator = $this->getDoctrine()->getRepository(Operator::class)->find($id);

        if (!$operator) {
            throw $this->createNotFoundException('No product found for id ' . $id);
        }


        $operator->setDisable(true);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($operator);
        $entityManager->flush();

        return $this->redirectToRoute('operator');
    }

    /**
     * @Route("/operator/add", name="add")
     */
    public function addOperator(Request $request, ValidatorInterface $validator) {
        $user = $this->getUser();
        $operator = new Operator();

        $form = $this->createForm(AddOperatorFormType::class, $operator);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $operator->setDisable(false);
            $operator->setUser($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($operator);
            $entityManager->flush();
            return $this->redirectToRoute('operator');
        }
        return $this->render('operator/new.html.twig', ['addNewOperatorForm' => $form->createView()]);
    }

}
