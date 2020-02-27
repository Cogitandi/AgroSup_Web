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
     * @Route("cropPlan", name="cropPlan")
     */
    public function cropPlan(Request $request) {
        $user = $this->getUser();
        $yearPlan = $user->getChoosedYearPlan();
        if ($yearPlan) {
            $yearPlan2 = ManagamentController::findYearPlanByYearBack(2, $yearPlan);
            $yearPlan1 = ManagamentController::findYearPlanByYearBack(1, $yearPlan);

            $form = $this->createForm(CropPlanType::class, $yearPlan, ['userPlantList' => $user->getUserPlants()]);
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
        return $this->redirectToRoute('chooseYearPlan');
    }

    /**
     * @Route("summary", name="summary")
     */
    public function summary() {
        $user = $this->getUser();
        $yearPlan = $user->getChoosedYearPlan();
        if ($yearPlan) {
            $operators = $yearPlan->getOperators();
            $plantForEachOperator = array();

            foreach ($operators as $operator) {
                $plantForEachOperator[$operator->getId()] = ManagamentController::createArrayWithPlants($operator);
                $plantForEachOperator[$operator->getId()] = ManagamentController::addAreaToPlantsArray($operator, $plantForEachOperator[$operator->getId()]);
            }
            //Without payments
            $plantForEachOperator["NonePayments"] = ManagamentController::createArrayWithPlantsForNullOperator($yearPlan);
            $plantForEachOperator["NonePayments"] = ManagamentController::addAreaToPlantsArrayForNullOperator($yearPlan, $plantForEachOperator["NonePayments"]);
            //Sum
            $plantForEachOperator["Sum"] = ManagamentController::createArrayWithPlantsSum($yearPlan);
            $plantForEachOperator["Sum"] = ManagamentController::addAreaToPlantsArraySum($yearPlan, $plantForEachOperator["Sum"]);
//            print "<pre>";
//            print_r($plantForEachOperator);
//            print "</pre>";

            $parameters = [
                'yearPlan' => $yearPlan,
                'plants' => $plantForEachOperator,
            ];
            return $this->render('managament/summary.twig', $parameters);
        }
        return $this->redirectToRoute('yearPlan');
    }

    // Sum
    public function createArrayWithPlantsSum($yearPlan) {
        $outputArray = array();
        foreach ($yearPlan->getFields() as $field) {
            foreach ($field->getParcels() as $parcel) {
                $plant = $parcel->getField()->getPlant();
                if (ManagamentController::IsNoExistInArray($outputArray, $plant)) {
                    $outputArray[$plant->getName()] = 0;
                }
            }
        }
        return $outputArray;
    }

    public function addAreaToPlantsArraySum($yearPlan, $plantArray) {
        $plantArray['Paliwo'] = 0;
        foreach ($yearPlan->getFields() as $field) {
            foreach ($field->getParcels() as $parcel) {
                if($parcel->getFuelApplication() ) {
                    $plantArray['Paliwo'] += $parcel->getCultivatedArea();
                                                   
                }
                $plant = $parcel->getField()->getPlant();
                if ($plant) {
                    $plantArray[$plant->getName()] += $parcel->getCultivatedArea();
                }
            }
        }
        return $plantArray;
    }

    // None payments
    public function createArrayWithPlantsForNullOperator($yearPlan) {
        $outputArray = array();

        foreach ($yearPlan->getFields() as $field) {
            foreach ($field->getParcels() as $parcel) {
                if ($parcel->getArimrOperator() == null) {
                    $plant = $parcel->getField()->getPlant();
                    if (ManagamentController::IsNoExistInArray($outputArray, $plant)) {
                        $outputArray[$plant->getName()] = 0;
                    }
                }
            }
        }
        return $outputArray;
    }

    public function addAreaToPlantsArrayForNullOperator($yearPlan, $plantArray) {
        $plantArray['Paliwo'] = 0;
        foreach ($yearPlan->getFields() as $field) {
            foreach ($field->getParcels() as $parcel) {
                if ($parcel->getArimrOperator() == null) {
                    if($parcel->getFuelApplication() ) {
                    $plantArray['Paliwo'] += $parcel->getCultivatedArea();
                                                   
                }
                    $plant = $parcel->getField()->getPlant();
                    if ($plant) {
                        $plantArray[$plant->getName()] += $parcel->getCultivatedArea();
                    }
                }
            }
        }
        return $plantArray;
    }

    // Each operator
    public function addAreaToPlantsArray($operator, $plantArray) {
        $plantArray['EFA'] = 0;
        $plantArray['Paliwo'] = 0;
        foreach ($operator->getParcels() as $parcel) {
            if($parcel->getFuelApplication() ) {
                    $plantArray['Paliwo'] += $parcel->getCultivatedArea();
                                                   
                }
            $plant = $parcel->getField()->getPlant();
            if ($plant) {
                $plantArray[$plant->getName()] += $parcel->getCultivatedArea();
                if ($plant->getEfaNitrogen()) {
                    $plantArray['EFA'] += $parcel->getCultivatedArea();
                }
            }
        }
        return $plantArray;
    }

    public function createArrayWithPlants($operator) {
        $outputArray = array();

        foreach ($operator->getParcels() as $parcel) {
            $plant = $parcel->getField()->getPlant();
            if (ManagamentController::IsNoExistInArray($outputArray, $plant)) {
                $outputArray[$plant->getName()] = 0;
            }
        }
        return $outputArray;
    }

    public function IsNoExistInArray($array, $plant) {
        if ($plant == null)
            return false;
        foreach ($array as $item => $value) {
            if ($item == $plant->getName())
                return false;
        }
        return true;
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
                if ($searchedField->getPlant() == NULL) {
                    array_push($plantArray, "Brak danych");
                    break;
                }
                if ($searchedField->getName() == $soughtField->getName() && $searchedField->getPlant() != NULL) {
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
