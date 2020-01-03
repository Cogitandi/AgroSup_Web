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
use Doctrine\Common\Collections\ArrayCollection;
use DeepCopy\DeepCopy;

class DataController extends AbstractController {

    /**
     * @Route("/yearPlan", name="chooseYearPlan")
     */
    public function chooseYearPlan() {
        $user = $this->getUser();
        $userYearPlans = $user->getYearPlans();
        return $this->render('data/chooseYearPlan.twig', ['yearPlanCollection' => $userYearPlans]);
    }

    /**
     * @Route("/setYearPlan", name="setYearPlan")
     */
    public function setYearPlan(Request $request) {
        $user = $this->getUser();

        // From POST
        $yearPlanId = $request->request->get('yearPlan');

        $yearPlan = DataController::findYearPlanById($yearPlanId);
        if ($yearPlan) {
            $user->setChoosedYearPlan($yearPlan);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }
        return $this->redirectToRoute('main');
    }

    /**
     * @Route("/yearPlan/status", name="yearPlanStatus")
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
     * @Route("/yearPlan/add", name="addYearPlan")
     */
    public function addYearPlan(ValidatorInterface $validator, Request $request) {
        $user = $this->getUser();
        $yearPlan = new YearPlan();
        $yearPlan->setUser($user);

        $form = $this->createForm(NewYearPlanFormType::class, $yearPlan, ['yearPlans' => $user->getYearPlans()]);
        $form->handleRequest($request);

        $errors = $validator->validate($yearPlan);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $yearPlanToImport = $form->get('import')->getData();
            $yearPlan->setEndYear($yearPlan->getStartYear() + 1);
            $entityManager->persist($yearPlan);

            if ($yearPlanToImport) { //Import settings from other YearPlan
                DataController::deepSave($yearPlanToImport, $yearPlan);
            }
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
     * @Route("/yearPlan", name="yearPlan")
     */
    public function yearPlan() {
        $user = $this->getUser();
        return $this->render('data/yearPlan.html.twig', ['yearPlan' => $user->getYearPlans()]);
    }

    /**
     * @Route("/parcel", name="parcelList")
     */
    public function parcelList(Request $request) {
        $user = $this->getUser();
        $yearPlan = $user->getChoosedYearPlan();

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
        return $this->redirectToRoute('chooseYearPlan');
    }

    /**
     * @Route("/field", name="field")
     */
    public function field(Request $request) {
        $user = $this->getUser();
        $yearPlan = $user->getChoosedYearPlan();

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

        return $this->redirectToRoute('chooseYearPlan');
    }

    /**
     * @Route("/field/add", name="addField")
     */
    public function addField(Request $request) {
        $user = $this->getUser();
        $yearPlan = $user->getChoosedYearPlan();

        if ($yearPlan) {
            $operators = $yearPlan->getOperators();
            $entityManager = $this->getDoctrine()->getManager();

            $field = new Field();
            $field->setYearPlan($yearPlan);
            $form = $this->createForm(NewFieldFormType::class, $field, ['operators' => $operators]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->addFlash('success', 'Pole ' . $field->getName() . ' zostało utworzone');
                $entityManager->persist($field);
                DataController::addYearToParcels($field, $entityManager);
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
        return $this->redirectToRoute('chooseYearPlan');
    }

    /**
     * @Route("/field/edit/{id}", name="editField")
     */
    public function editField($id, Request $request) {
        $user = $this->getUser();
        $yearPlan = $user->getChoosedYearPlan();

        if ($yearPlan) {
            $operators = $yearPlan->getOperators();
            $field = DataController::findFieldById($id, $user);
            if ($field) {
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

    public function findYearPlanById($id) {
        $user = $this->getUser();
        $repository = $this->getDoctrine()->getRepository(YearPlan::class);
        $yearPlan = $repository->find($id);
        if ($yearPlan) {
            if ($yearPlan->getUser() == $user)
                return $yearPlan;
        }
    }

    public function deepSave($yearPlanToImport, $yearPlanOutput) {
        $entityManager = $this->getDoctrine()->getManager();

        $operatorsList = new ArrayCollection();
        foreach ($yearPlanToImport->getOperators() as $operator) {
            $operatorNew = clone $operator;
            $operatorNew->setYearPlan($yearPlanOutput);
            $entityManager->persist($operatorNew);
            $operatorsList->add($operatorNew);
        }

        foreach ($yearPlanToImport->getFields() as $field) {
            $fieldNew = clone $field;
            $fieldNew->setYearPlan($yearPlanOutput);
            foreach ($field->getParcels() as $parcel) {
                $parcelNew = clone $parcel;
                $parcelNew->setYearPlan($yearPlanOutput);
                $parcelNew->setField($fieldNew);
                $parcelNew->setArimrOperator(null);
                foreach ($operatorsList as $operator) {
                    if (!$parcel->getArimrOperator())
                        break;
                    if ($operator->getFirstName() == $parcel->getArimrOperator()->getFirstName() &&
                            $operator->getSurname() == $parcel->getArimrOperator()->getSurname()
                    ) {
                        $parcelNew->setArimrOperator($operator);
                    }
                }
                $entityManager->persist($parcelNew);
            }
            $entityManager->persist($fieldNew);
        }
    }

}
