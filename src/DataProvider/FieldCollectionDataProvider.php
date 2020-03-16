<?php

// api/src/DataProvider/BlogPostCollectionDataProvider.php

namespace App\DataProvider;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\User;
use App\Entity\Field;
use Generator;

final class FieldCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface {

    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->tokenStorage = $tokenStorage;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool {
//        if(array_key_exists('filters', $context)) {
//            var_dump($context['filters']);
//        }

        return Field::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): Generator {
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof User) {
            foreach ($user->getYearPlans() as $yearplan) {
                foreach ($yearplan->getFieds() as $field) {
                    yield $field;
                }
            }
        }

//            if ($user instanceof User) {
//                $yearPlan = $user->getYearPlanById($context['filters']['yearPlan']);
//                if ($yearPlan) {
//                    $fields = $yearPlan->getFields();
//                    foreach ($fields as $field) {
//                        yield $field;
//                    }
//                }
//            }
    }

}
