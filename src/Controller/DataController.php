<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\YearPlan;
use App\Entity\User;
use App\Entity\Field;
use App\Entity\Parcel;
use App\Form\NewYearPlanFormType;
use App\Form\NewFieldFormType;

use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

use Symfony\Component\HttpFoundation\Session\SessionInterface;


class DataController extends AbstractController
{
    /**
     * @Route("/data", name="data")
     */
    public function index()
    {
        return $this->render('data/yearPlan.html.twig', [
            'controller_name' => 'DataController',
        ]);
    }
    /**
     * @Route("/data/yearPlan", name="yearPlan")
     */
    public function yearPlan()
    {
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
        if ($form->isSubmitted() && $form->isValid() ) {

            $yearPlan->setIsClosed(false);
            $yearPlan->setEndYear($yearPlan->getStartYear()+1);
            $yearPlan->setUser($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($yearPlan);
            $entityManager->flush();
            return $this->redirectToRoute('yearPlan');
        }

        return $this->render('data/newYearPlan.html.twig', ['newYearPlanForm' => $form->createView() ]);
    }
    /**
     * @Route("/data/parcel", name="parcel")
     */
    public function parcel()
    {
        return $this->render('data/parcel.html.twig', [
            'controller_name' => 'DataController',
        ]);
    }
    /**
     * @Route("data/field", name="field")
     */
    public function field()
    {
        // TODO: 
        // poprawnosc id przesylanego getem

        $request = Request::createFromGlobals();
        $yearPlanId = $request->query->get('y');
        if( $yearPlanId != null) {
            $user = $this->getUser();
            $field = $this->getDoctrine()->getRepository(Field::class)->findBy(array('yearPlan' => $yearPlanId));
            $cultivatedArea = Array();
            foreach( $field as $item) {
                $parcels = $this->getDoctrine()->getRepository(Parcel::class)->findBy(array('field' => $item->getId() ));
                $cultivatedAreaSum = 0;
                foreach( $parcels as $each ) {
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
    private $session;
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    /**
     * @Route("data/field/add/{id}", name="addField")
     */

    public function addField($id, Request $request, SessionInterface $session) {
         //$this->session->set('attribute-name', 'attribute-value');
;        // TODO
        // check access to plan
        
        // if the attribute may or may not exist, you can define a default value for it
        //$token = $session->get('attribute-name', 'default-attribute-value');
        // ...
        //$session->clear();

        // find yearPlan
        $user = $this->getUser();
        $yearPlan = $this->getDoctrine()->getRepository(YearPlan::class)->find($id);



        if (!$yearPlan) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        // create field
        //return $this->render('data/yearPlanToChoose.html.twig', ['yearPlanCollection' => 'a' ]);


        // create form for add field
        $field = new Field();
        $form = $this->createForm(NewFieldFormType::class, $field);
        $form->handleRequest($request);

        // fetch from form
        if ($form->isSubmitted() && $form->isValid() ) {
            $field->setYearPlan($yearPlan);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($field);
            $entityManager->flush();
            return $this->redirectToRoute('field');
        }
        $operators = $user->getOperators();

        return $this->render('data/newField.html.twig', ['newFieldForm' => $form->createView(), 'operators' => $operators ]);


    }
}
