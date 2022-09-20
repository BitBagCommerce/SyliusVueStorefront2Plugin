<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

final class ShopUserPostVerifyController
{
    private ?string $vsf2Host;

    private RouterInterface $router;

    public function __construct(
        ?string $vsf2Host,
        RouterInterface $router
    ) {
        $this->vsf2Host = $vsf2Host;
        $this->router = $router;
    }

    public function postVerifyAction(Request $request): Response
    {
        if ($this->vsf2Host !== null && strlen($this->vsf2Host) > 0) {
            return new RedirectResponse($this->vsf2Host);
        }

        return new RedirectResponse(
            $this->router->generate('sylius_shop_account_dashboard')
        );
    }
}
