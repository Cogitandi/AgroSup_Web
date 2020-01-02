<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Operator;
use App\Form\AddOperatorFormType;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\YearPlan;

class OperatorController extends AbstractController {

    /**
     * @Route("/operator", name="operator")
     */
    public function index() {
        $user = $this->getUser();

        $yearPlan = $user->getChoosedYearPlan();
        if ($yearPlan) {
            $operators = $yearPlan->getOperators();
            $parameters = [
                'operator' => $operators
            ];
            return $this->render('operator/index.html.twig', $parameters);
        }
        return $this->redirectToRoute('chooseYearPlan');
    }

    /**
     * @Route("/operator/delete/{id}", name="delete")
     */
    public function deleteOperator($id) {
        $user = $this->getUser();
        $yearPlan = $user->getChoosedYearPlan();

        if ($yearPlan) {
            $operator = OperatorController::findEntityById($yearPlan->getOperators(), $id);

            if ($operator) {
                $operator->setDisable(true);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();
            }
            return $this->redirectToRoute('operator');
        }
        return $this->redirectToRoute('chooseYearPlan');
    }

    /**
     * @Route("/operator/add", name="add")
     */
    public function addOperator(Request $request) {
        $user = $this->getUser();
        $yearPlan = $user->getChoosedYearPlan();
        if ($yearPlan) {
            $operator = new Operator();

            $form = $this->createForm(AddOperatorFormType::class, $operator);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $operator->setUser($user);
                $operator->setYearPlan($yearPlan);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($operator);
                $entityManager->flush();
                return $this->redirectToRoute('operator');
            }
            return $this->render('operator/new.html.twig', ['addNewOperatorForm' => $form->createView()]);
        }
        return $this->redirectToRoute('chooseYearPlan');
    }

    public function findEntityById($collection, $id) {
        foreach ($collection as $item) {
            if ($item->getId() == $id)
                return $item;
        }
    }

}
