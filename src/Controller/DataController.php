<?php

namespace App\Controller;

use App\Entity\Field;
use App\Entity\Parcel;
use App\Entity\YearPlan;
use App\Form\NewFieldFormType;
use App\Form\NewYearPlanFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

class DataController extends AbstractController {

    /**
     * @Route("/data/yearPlan", name="yearPlan")
     */
    public function yearPlan() {
        $user = $this->getUser();
        $yearPlan = new YearPlan();

        // List of yearPlans
        return $this->render('data/yearPlan.html.twig', [
                    'yearPlan' => $user->getYearPlans(),
        ]);
    }

    /**
     * @Route("/data/yearPlan/status", name="yearPlanStatus")
     */
    public function yearPlanStatus(Request $request) {
        $user = $this->getUser();
        $yearPlanId = $request->request->get('yearPlan');
        $status = $request->request->get('status');
        $yearPlan = $this->getDoctrine()->getRepository(YearPlan::class)->find($yearPlanId);

        if ($status == "open") {
            $yearPlan->setIsClosed(true);
        }
        if ($status == "closed") {
            $yearPlan->setIsClosed(false);
        }


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($yearPlan);
        $entityManager->flush();

        return $this->redirectToRoute('yearPlan');
    }

    /**
     * @Route("/data/yearPlan/add", name="addYearPlan")
     */
    public function addYearPlan(Request $request) {
        // TODO:
        // Jeden plan na jeden sezon
        $user = $this->getUser();
        $yearPlan = new YearPlan();

        // create newYearPlanFormType
        $form = $this->createForm(NewYearPlanFormType::class, $yearPlan);
        $form->handleRequest($request);

        //add to database new yearplan
        if ($form->isSubmitted() && $form->isValid()) {

            $yearPlan->setIsClosed(false);
            $yearPlan->setEndYear($yearPlan->getStartYear() + 1);
            $yearPlan->setUser($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($yearPlan);
            $entityManager->flush();
            return $this->redirectToRoute('yearPlan');
        }

        return $this->render('data/newYearPlan.html.twig', ['newYearPlanForm' => $form->createView()]);
    }

    /**
     * @Route("data/parcel/", name="parcelList")
     */
    public function parcelList(Request $request) {
        $yearPlanId = $request->request->get('yearPlan');
        if ($yearPlanId == null) {
            $user = $this->getUser();
            $yearPlanCollection = $user->getYearPlans();
            return $this->render('data/yearPlanToChooseParcel.html.twig', ['yearPlanCollection' => $yearPlanCollection]);
        } else {
            // Show list of available yearPlans
            $user = $this->getUser();
            //$operators = $user->getOperators();
            $yearPlan = $this->getDoctrine()->getRepository(YearPlan::class)->find($yearPlanId);
            $parcels = new ArrayCollection();
            foreach ($yearPlan->getParcels() as $parcel) {
                $parcels->add($parcel);
            }
            $orderBy = (Criteria::create())->orderBy([
                'field' => Criteria::ASC,
            ]);
            $parcels = $parcels->matching($orderBy)->toArray();
            return $this->render('data/parcel.html.twig', ['parcels' => $parcels, 'start' => $yearPlan->getStartYear()]);
        }
    }

    /**
     * @Route("data/field", name="field")
     */
    public function field(Request $request) {
        $user = $this->getUser();
        $yearPlanId = $request->request->get('yearPlan');
        $operators = $user->getOperators();


        if ($yearPlanId == null) { // lista do planow rocznych do wyboru
            $yearPlanCollection = $user->getYearPlans();
            return $this->render('data/yearPlanToChooseFieldList.html.twig', ['yearPlanCollection' => $yearPlanCollection]);
        } else { // wybrano plan roczny
            $yearPlan = $this->getDoctrine()->getRepository(YearPlan::class)->find($yearPlanId);
            $fields = $this->getDoctrine()->getRepository(Field::class)->findBy(array('yearPlan' => $yearPlanId));
            $cultivatedArea = DataController::sumAreaEachField($fields);

            $parameters = [
                'yearPlan' => $yearPlan,
                'start' => $yearPlan->getStartYear(),
                'showField' => true,
                'field' => $fields,
                'cultivatedArea' => $cultivatedArea
            ];
            return $this->render('data/field.html.twig', $parameters);
        }
        $yearPlanCollection = $user->getYearPlans();
        $parameters = [
            'yearPlanCollection' => $yearPlanCollection,
            'showField' => false
        ];
        return $this->render('data/field.html.twig', $parameters);
    }

    /**
     * @Route("data/field/yearPlan", name="chooseYearToAddField")
     */
    public function chooseYearToAddField(Request $request) {
        $user = $this->getUser();
        $yearPlanId = $request->request->get('yearPlan');

        if ($yearPlanId == null) {
            $yearPlanCollection = $user->getYearPlans();
            return $this->render('data/yearPlanToChooseField.html.twig', ['yearPlanCollection' => $yearPlanCollection]);
        } else {
            
        }
    }

    /**
     * @Route("data/field/add", name="addField")
     */
    public function addField(Request $request) {
        // ISTNIEJE POLE O TEJ NAZWIE

        $user = $this->getUser();
        $operators = $user->getOperators();
        $entityManager = $this->getDoctrine()->getManager();
        $field = new Field();
        $yearPlanId = $request->request->get('yearPlan');

        // List of yearPlans
        if ($yearPlanId != NULL) {
            $yearPlan = $this->getDoctrine()->getRepository(YearPlan::class)->find($yearPlanId);
            $field->setYearPlan($yearPlan);
        }

        $form = $this->createForm(NewFieldFormType::class, $field, ['operators' => $operators]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->addFlash('success', 'Pole ' . $field->getName() . ' zostało utworzone');
            $entityManager->persist($field);

            foreach ($field->getParcels() as $item) {
                $item->setYearPlan($field->getYearPlan());
                $entityManager->persist($item);
            }
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

    /**
     * @Route("data/field/edit/{id}", name="editField")
     */
    public function editField($id, Request $request) {
        // ACCESS TO POST FIELD
        $user = $this->getUser();
        $field = $this->getDoctrine()->getRepository(Field::class)->find($id);
        $operators = $user->getOperators();
        $yearPlan = $field->getYearPlan();



        $form = $this->createForm(NewFieldFormType::class, $field, array('operators' => $operators));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($field->getParcels() as $parcel) {
                $parcel->setYearPlan($yearPlan);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($field);
            $entityManager->flush();
            $this->addFlash('success', 'Pole ' . $field->getName() . ' zostało zmodyfikwoane');
            //return $this->redirectToRoute('field');
        }

        $parameters = [
            'yearPlan' => $yearPlan,
            'editFieldForm' => $form->createView(),
            'operators' => $operators
        ];

        return $this->render('data/editField.html.twig', $parameters);
    }

    // Functions
    public function sumAreaEachField(Array $fields) {
        $user = $this->getUser();
        $cultivatedArea = Array();
        foreach ($fields as $item) {
            $parcels = $this->getDoctrine()->getRepository(Parcel::class)->findBy(array('field' => $item->getId()));
            $cultivatedAreaSum = 0;
            foreach ($parcels as $each) {
                $cultivatedAreaSum += $each->getCultivatedArea();
            }
            array_push($cultivatedArea, $cultivatedAreaSum);
        }
        return $cultivatedArea;
    }

}
