<?php

// api/src/DataProvider/BlogPostCollectionDataProvider.php

namespace App\DataProvider;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\User;
use App\Entity\Parcel;
use Generator;

final class ParcelCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface {

    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->tokenStorage = $tokenStorage;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool {
        return Parcel::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): Generator {
        if (array_key_exists('filters', $context)) {
            $user = $this->tokenStorage->getToken()->getUser();

            if ($user instanceof User) {
                $yearPlan = $user->getYearPlanById($context['filters']['yearPlan']);
                if ($yearPlan) {
                    $parcels = $yearPlan->getParcels();
                    foreach ($parcels as $parcel) {
                        yield $parcel;
                    }
                }
            }
        }
    }

}
