<?php

namespace App\Controller;

use App\Entity\Field;
use App\Entity\YearPlan;
use App\Form\NewFieldFormType;
use App\Form\NewYearPlanFormType;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DataController extends AbstractController {

    /**
     * @Route("/data/yearPlan", name="yearPlan")
     */
    public function yearPlan() {
        $user = $this->getUser();

        return $this->render('data/yearPlan.html.twig', ['yearPlan' => $user->getYearPlans()]);
    }

    /**
     * @Route("/data/yearPlan/status", name="yearPlanStatus")
     */
    public function yearPlanChangeStatus(Request $request) {
        $user = $this->getUser();
        $userYearPlans = $user->getYearPlans();

        // From POST
        $yearPlanId = $request->request->get('yearPlan');
        $status = $request->request->get('status');

        $yearPlan = DataController::findEntityById($userYearPlans, $yearPlanId);

        if ($yearPlan) {
            // Change status
            $status == "open" ? $yearPlan->setIsClosed(true) : $yearPlan->setIsClosed(false);
            // Save to database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }
        return $this->redirectToRoute('yearPlan');
    }

    /**
     * @Route("/data/yearPlan/add", name="addYearPlan")
     */
    public function addYearPlan(ValidatorInterface $validator, Request $request) {
        $user = $this->getUser();
        $yearPlan = new YearPlan();
        $yearPlan->setUser($user);

        $form = $this->createForm(NewYearPlanFormType::class, $yearPlan);
        $form->handleRequest($request);

        $errors = $validator->validate($yearPlan);

        if ($form->isSubmitted() && $form->isValid()) {
            $yearPlan->setEndYear($yearPlan->getStartYear() + 1);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($yearPlan);
            $entityManager->flush();
            return $this->redirectToRoute('yearPlan');
        }

        $parameters = [
            'newYearPlanForm' => $form->createView(),
            'errors' => $errors
        ];
        return $this->render('data/newYearPlan.html.twig', $parameters);
    }

    /**
     * @Route("data/parcel/", name="parcelList")
     */
    public function parcelList(Request $request) {
        $user = $this->getUser();
        $userYearPlans = $user->getYearPlans();

        // from POST
        $yearPlanId = $request->request->get('yearPlan');

        $yearPlan = DataController::findEntityById($userYearPlans, $yearPlanId);
        if ($yearPlan) { // If found yearPlan
            $parcels = $yearPlan->getParcels();
            $orderBy = (Criteria::create())->orderBy([
                'field' => Criteria::ASC,
            ]);
            $parcels = $parcels->matching($orderBy)->toArray();
            $parameters = [
                'parcels' => $parcels,
                'start' => $yearPlan->getStartYear()
            ];
            return $this->render('data/parcel.html.twig', $parameters);
        }



        // yearPlan not choosed
        return $this->render('data/yearPlanToChooseParcel.html.twig', ['yearPlanCollection' => $userYearPlans]);
    }

    /**
     * @Route("data/field/list", name="field")
     */
    public function field(Request $request) {
        $user = $this->getUser();
        $userYearPlans = $user->getYearPlans();

        // from POST
        $yearPlanId = $request->request->get('yearPlan');

        $yearPlan = DataController::findEntityById($userYearPlans, $yearPlanId);

        if ($yearPlan) { // If found yearPlan
            $fields = $yearPlan->getFields();
            $cultivatedArea = DataController::sumAreaEachField($fields);
            $parameters = [
                'start' => $yearPlan->getStartYear(),
                'field' => $fields,
                'cultivatedArea' => $cultivatedArea
            ];
            return $this->render('data/field.html.twig', $parameters);
        }

        return $this->redirectToRoute('chooseYearToFieldList');
    }

    /**
     * @Route("data/field/yearPlan", name="chooseYearToAddField")
     */
    public function chooseYearToAddField() {
        $user = $this->getUser();
        $userYearPlans = $user->getYearPlans();
        return $this->render('data/yearPlanToChooseField.html.twig', ['yearPlanCollection' => $userYearPlans]);
    }

    /**
     * @Route("data/field", name="chooseYearToFieldList")
     */
    public function chooseYearToFieldList() {
        $user = $this->getUser();
        $userYearPlans = $user->getYearPlans();
        return $this->render('data/yearPlanToChooseFieldList.html.twig', ['yearPlanCollection' => $userYearPlans]);
    }

    /**
     * @Route("data/field/add", name="addField")
     */
    public function addField(Request $request) {
        $user = $this->getUser();
        $userYearPlans = $user->getYearPlans();
        $operators = $user->getOperators();
        $entityManager = $this->getDoctrine()->getManager();

        $field = new Field();
        $yearPlanId = $request->request->get('yearPlan');

        $yearPlan = DataController::findEntityById($userYearPlans, $yearPlanId);

        if ($yearPlan) {
            $field->setYearPlan($yearPlan);
            $form = $this->createForm(NewFieldFormType::class, $field, ['operators' => $operators]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->addFlash('success', 'Pole ' . $field->getName() . ' zostało utworzone');
                $entityManager->persist($field);
                DataController::addYearToParcels($field, $yearPlan, $entityManager);
                $entityManager->flush();

                unset($field);
                unset($form);
                $field = new Field();
                $form = $this->createForm(NewFieldFormType::class, $field, ['operators' => $operators]);
            }
            $parameters = [
                'yearPlan' => $yearPlan,
                'newFieldForm' => $form->createView(),
                'operators' => $operators
            ];
            return $this->render('data/newField.html.twig', $parameters);
        }
        return $this->redirectToRoute('chooseYearToAddField');
    }

    /**
     * @Route("data/field/edit/{id}", name="editField")
     */
    public function editField($id, Request $request) {
        $user = $this->getUser();
        $field = DataController::findFieldById($id, $user);

        if ($field) {
            $operators = $user->getOperators();
            $yearPlan = $field->getYearPlan();

            $form = $this->createForm(NewFieldFormType::class, $field, array('operators' => $operators));
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                DataController::addYearToParcels($field, $entityManager);

                $entityManager->persist($field);
                $entityManager->flush();
                $this->addFlash('success', 'Pole ' . $field->getName() . ' zostało zmodyfikwoane');
            }

            $parameters = [
                'yearPlan' => $yearPlan,
                'editFieldForm' => $form->createView(),
                'operators' => $operators
            ];

            return $this->render('data/editField.html.twig', $parameters);
        }
        return $this->redirectToRoute('field');
    }

    // Functions
    public function sumAreaEachField(Collection $fields) {
        $user = $this->getUser();
        $cultivatedArea = Array();
        foreach ($fields as $item) {
            $parcels = $item->getParcels();
            $cultivatedAreaSum = 0;
            foreach ($parcels as $each) {
                $cultivatedAreaSum += $each->getCultivatedArea();
            }
            array_push($cultivatedArea, $cultivatedAreaSum / 100);
        }
        return $cultivatedArea;
    }

    public function findEntityById(Collection $collection, $id) {
        foreach ($collection as $item) {
            if ($item->getId() == $id)
                return $item;
        }
    }

    public function findFieldById($id, $user) {
        $repository = $this->getDoctrine()->getRepository(Field::class);
        $field = $repository->find($id);
        $userYearPlans = $user->getYearPlans();
        foreach ($userYearPlans as $userYearPlan) {
            if ($field->getYearPlan()->getId() == $userYearPlan->getId())
                return $field;
        }
    }

    public function addYearToParcels(Field $field, $entityManager) {
        foreach ($field->getParcels() as $item) {
            $item->setYearPlan($field->getYearPlan());
            $entityManager->persist($item);
        }
    }

}
