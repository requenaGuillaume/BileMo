<?php

namespace App\Service;

use App\Entity\SelfDiscoverability;
use App\Interface\DiscoverabilityServiceInterface;

class DiscoverabilityService implements DiscoverabilityServiceInterface
{

    public function setLinksForList(array &$entities, array $entitySelfDiscoverabilityList, bool $isUsersResource = false): void
    {
        foreach($entities as $entity){
            if($isUsersResource){
                $links = $this->getLinks($entitySelfDiscoverabilityList, $entity->getCompany()->getId(), $entity->getId());
            }else{
                $links = $this->getLinks($entitySelfDiscoverabilityList, $entity->getId());
            }

            $entity->setLinks($links);
        }
    }

    public function getLinks(array $selfDiscoverabilityList, int $mainEntityId = null, ?int $secondEntityId = null): array
    {
        $links = [];

        foreach($selfDiscoverabilityList as $discoverability){
            $uri = $discoverability->getUri();

            if($mainEntityId && str_contains($uri, SelfDiscoverability::URI_ID)){
                $uri = str_replace(SelfDiscoverability::URI_ID, $mainEntityId, $uri);
            }elseif($mainEntityId && str_contains($uri, SelfDiscoverability::URI_COMPANY_ID)){
                $uri = str_replace(SelfDiscoverability::URI_COMPANY_ID, $mainEntityId, $uri);
            }

            if($secondEntityId && str_contains($uri, SelfDiscoverability::URI_USER_ID)){
                $uri = str_replace(SelfDiscoverability::URI_USER_ID, $secondEntityId, $uri);
            }

            $links[] = [
                'method' => $discoverability->getMethod(),
                'uri' => $uri,
                'description' => $discoverability->getDescription()
            ];
        }

        return $links;
    }

}
