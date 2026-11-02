<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractMainController extends AbstractController
{
    protected function getRedirectionUrl(Request $request, string $routeName): RedirectResponse
    {
        $backUrl = $request->getSession()->get($routeName.'_url');

        return \is_string($backUrl)
            ? $this->redirect($backUrl)
            : $this->redirectToRoute($routeName);
    }
}
