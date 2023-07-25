<?php

namespace App\Trait;

use Symfony\Component\Serializer\Annotation\Groups;

trait SelfDiscoverableTrait
{
    #[Groups(['showUsers'])]
    private array $links;

    public function setLinks(array $links): self
    {
        $this->links = $links;
        return $this;
    }

    public function getLinks(): array
    {
        return $this->links;
    }
}
