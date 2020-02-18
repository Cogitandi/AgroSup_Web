<?php

namespace App\Controller\api;

# Import those

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\SerializerInterface;


class apiController extends AbstractController {

    /**
     * api route redirects
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function api(SerializerInterface $serializer) {
//        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
//        $encoders = [new XmlEncoder(), new JsonEncoder()];
//        $normalizers = [new ObjectNormalizer()];
//
//        $serializer = new Serializer($normalizers, $encoders);
//        
// 
        //$yearPlans = $this->getUser()->getChoosedYearPlan();
        $yearPlans = $this->getUser()->getYearPlans();

        //$jsonContent = $serializer->serialize($yearPlans, 'json',['groups' => 'read','ignored_attributes' => ['user','yearPlan','field', 'ArimrOperator']]);
        //$jsonContent = $serializer->serialize($yearPlans, 'json',['groups' => ['read']]);
        $jsonContent = $serializer->serialize($yearPlans,'json', ['groups' => ['read']]);
        return new Response($jsonContent);
    }

}
