<?php

declare(strict_types=1);

namespace App\Model;

abstract class SearchBarModel
{
    private ?string $keywords = null;

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(string $keywords): static
    {
        $this->keywords = $keywords;

        return $this;
    }
}
