<?php

// api/src/DataProvider/BlogPostCollectionDataProvider.php

namespace App\DataProvider;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\User;
use App\Entity\YearPlan;
use Generator;

final class YearPlanCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface {

    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->tokenStorage = $tokenStorage;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool {
        return YearPlan::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null): Generator {
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof User) {
            $yearPlans = $user->getYearPlans();
            if ($yearPlans) {
				foreach($yearPlans as $yearPlan) {
                    yield $yearPlan;
				}
            }
        }
    }

}
