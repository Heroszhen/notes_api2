<?php

declare(strict_types=1);

namespace App\Model;

final class ActressSearchModel extends SearchBarModel
{
    private ?string $country = null;

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }
}
