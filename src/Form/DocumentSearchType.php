<?php

declare(strict_types=1);

namespace App\Form;

class DocumentSearchType extends SearchBarType
{
    protected function getPlaceholder(): string
    {
        return 'Rechercher par titre, contenu';
    }
}
