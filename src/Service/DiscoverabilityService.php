<?php

namespace App\Service;


class DiscoverabilityService
{

    public function getLinks(array $selfDiscoverabilityList, ?int $mainEntityId = null, ?int $secondEntityId = null): array
    {
        // TODO le show de tous les produits devrait afficher l'url de show one avec chaque id !
        // TODO le show de tous les produits ne devrait apparaitre qu'une seule fois
        $links = [];

        foreach($selfDiscoverabilityList as $discoverability){
            $uri = $discoverability->getUri();

            if($mainEntityId && str_contains($uri, '{id}')){
                $uri = str_replace('{id}', $mainEntityId, $uri);
            }

            if($mainEntityId && str_contains($uri, '{company_id}')){
                $uri = str_replace('{company_id}', $mainEntityId, $uri);
            }

            if($secondEntityId && str_contains($uri, '{user_id}')){
                $uri = str_replace('{user_id}', $secondEntityId, $uri);
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