<?php

namespace App\Interface;

interface DiscoverabilityServiceInterface
{

    public function setLinksForList(array &$entities, array $entitySelfDiscoverabilityList, bool $isUsersResource = false): void;

    public function getLinks(array $selfDiscoverabilityList, int $mainEntityId = null, ?int $secondEntityId = null): array

}