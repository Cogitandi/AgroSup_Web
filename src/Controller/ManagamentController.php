<?php

namespace App\Controller;

use App\Entity\YearPlan;
use App\Form\CropPlanType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\Request;

class ManagamentController extends AbstractController {

    /**
     * @Route("/cropPlan", name="cropPlan")
     */
    public function cropPlan(Request $request) {
        $user = $this->getUser();
        $yearPlan = $user->getChoosedYearPlan();
        if ($yearPlan) {
            $yearPlan2 = ManagamentController::findYearPlanByYearBack(2, $yearPlan);
            $yearPlan1 = ManagamentController::findYearPlanByYearBack(2, $yearPlan);


            $form = $this->createForm(CropPlanType::class, $yearPlan);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();
            }

            $parameters = [
                'yearPlan2' => ManagamentController::findMatchingPlant($yearPlan, $yearPlan2),
                'yearPlan1' => ManagamentController::findMatchingPlant($yearPlan, $yearPlan1),
                'yearPlanAreas' => ManagamentController::sumAreaEachField($yearPlan->getFields()),
                'yearPlan' => $yearPlan,
                'form' => $form->createView(),
            ];
            return $this->render('managament/cropPlan.twig', $parameters);
        }
        return $this->redirectToRoute('main');
    }

    public function findYearPlanByYearBack($yearBack, YearPlan $yearPlanGiven) {
        $user = $this->getUser();
        $yearPlans = $user->getYearPlans();
        foreach ($yearPlans as $yearPlan) {
            if ($yearPlan->getStartYear() == ($yearPlanGiven->getStartYear()) - $yearBack)
                return $yearPlan;
        }
        return null;
    }

    public function findMatchingPlant(YearPlan $soughtYP, $searchedYP) {
        $soughtFields = $soughtYP->getFields();
        $plantArray = Array();
        if ($searchedYP == null) {
            foreach ($soughtFields as $soughtField) {

                array_push($plantArray, "Brak danych");
            }
            return $plantArray;
        }

        $searchedFields = $searchedYP->getFields();
        foreach ($soughtFields as $soughtField) {
            foreach ($searchedFields as $searchedField) {
                if ($searchedField->getName() == $soughtField->getName()) {
                    array_push($plantArray, $searchedField->getPlant()->getName());
                    break;
                }
            }
        }
        return $plantArray;
    }

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


    // stworzyc w polu referencje od jakiego pola pochodzi
}
