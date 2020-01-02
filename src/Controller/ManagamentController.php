<?php

namespace App\Controller;

use App\Entity\YearPlan;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ManagamentController extends AbstractController {

    /**
     * @Route("/cropPlan", name="cropPlan")
     */
    public function cropPlan() {
        $user = $this->getUser();
        $yearPlan = $user->getChoosedYearPlan();
        if ($yearPlan) {
            $yearPlan2 = ManagamentController::findYearPlanByYearBack(2, $yearPlan);
            $yearPlan1 = ManagamentController::findYearPlanByYearBack(2, $yearPlan);

            $parameters = [
                'yearPlan2' => ManagamentController::findMatchingPlant($yearPlan, $yearPlan2),
                'yearPlan1' => ManagamentController::findMatchingPlant($yearPlan, $yearPlan1),
                'yearPlan' => $yearPlan
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

    // stworzyc w polu referencje od jakiego pola pochodzi
}
