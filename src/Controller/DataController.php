<?php

namespace App\Controller;

use App\Entity\Field;
use App\Entity\Parcel;
use App\Entity\YearPlan;
use App\Form\NewFieldFormType;
use App\Form\NewYearPlanFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class DataController extends AbstractController {

    /**
     * @Route("/data", name="data")
     */
    public function index() {
        return $this->render('data/yearPlan.html.twig', [
                    'controller_name' => 'DataController',
        ]);
    }

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
     * @Route("/data/parcel", name="parcel")
     */
    public function parcel() {
        $user = $this->getUser();
        $operators = $user->getOperators();
       
        
        /*foreach($operators as $operator) {
            array_push($parcels, $operator->getParcels() );
        }*/
        $parcels = $operators[0]->getParcels()[0]->getParcelNumber();
        echo'<pre>'.var_dump($parcels).'</pre>';
        return $this->render('data/parcel.html.twig', [
                    'parcels' => $parcels,
        ]);
    }

    /**
     * @Route("data/field", name="field")
     */
    public function field() {
        // TODO: 
        // poprawnosc id przesylanego getem

        $request = Request::createFromGlobals();
        $yearPlanId = $request->query->get('y');
        if ($yearPlanId != null) {
            $user = $this->getUser();
            $field = $this->getDoctrine()->getRepository(Field::class)->findBy(array('yearPlan' => $yearPlanId));
            $cultivatedArea = Array();
            foreach ($field as $item) {
                $parcels = $this->getDoctrine()->getRepository(Parcel::class)->findBy(array('field' => $item->getId()));
                $cultivatedAreaSum = 0;
                foreach ($parcels as $each) {
                    $cultivatedAreaSum += $each->getCultivatedArea();
                }
                array_push($cultivatedArea, $cultivatedAreaSum);
            }

            return $this->render('data/field.html.twig', ['showField' => true, 'field' => $field, 'cultivatedArea' => $cultivatedArea]);
        }

        // Show list of available yearPlans
        $user = $this->getUser();
        $yearPlanCollection = $user->getYearPlans();

        return $this->render('data/field.html.twig', ['yearPlanCollection' => $yearPlanCollection, 'showField' => false]);
    }

    /**
     * @Route("data/field/add", name="chooseField")
     */
    public function chooseYearPlan() {
        // Show list of available yearPlans
        $user = $this->getUser();
        $yearPlanCollection = $user->getYearPlans();

        return $this->render('data/yearPlanToChoose.html.twig', ['yearPlanCollection' => $yearPlanCollection]);
    }

    /**
     * @Route("data/field/add/{id}", name="addField")
     */
    public function addField($id, Request $request) {
        // TODO
        // check access to plan
        // 
        // find yearPlan
        $user = $this->getUser();
        $yearPlan = $this->getDoctrine()->getRepository(YearPlan::class)->find($id);
        //findBy(array('yearPlan' => $yearPlanId));
        if (!$yearPlan) throw $this->createNotFoundException('Year plan not found ');
       
        $field = $this->getDoctrine()->getRepository(FIeld::class)->find($id);
        
        // form 
        $field = new Field();
        $field->setYearPlan($yearPlan);
        
        /*
        $parcel = new Parcel();
        $parcel->setParcelNumber('tag1');
        $parcel->setCultivatedArea(10000);
        $parcel->setFuelApplication(true);
        
        $parcel->setField($field);
        
        $field->getParcels()->add($parcel);*/
        
        
        $form = $this->createForm(NewFieldFormType::class, $field, array('operators' => $user->getOperators()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($field);
            
            foreach($field->getParcels() as $item ) {
                $item->setYearPlan($yearPlan);
               $entityManager->persist($item); 
            }
            $entityManager->flush();
            return $this->redirectToRoute('field');
        }

        $operators = $user->getOperators();
        return $this->render('data/newField.html.twig', ['newFieldForm' => $form->createView(), 'operators' => $operators]);

    }

}
