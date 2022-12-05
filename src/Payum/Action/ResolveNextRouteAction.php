<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Sylius\Bundle\PayumBundle\Request\ResolveNextRoute;
use Sylius\Component\Core\Model\PaymentInterface;
use Webmozart\Assert\Assert;

final class ResolveNextRouteAction implements ActionInterface
{
    public function execute($request): void
    {
        Assert::isInstanceOf($request, ResolveNextRoute::class);

        /** @var PaymentInterface $payment */
        $payment = $request->getFirstModel();

        $request->setRouteName(
            'bitbag_sylius_vue_storefront2_thank_you_page',
        );

        $order = $payment->getOrder();
        Assert::notNull($order);
        $request->setRouteParameters(['orderNumber' => $order->getNumber()]);
    }

    public function supports($request): bool
    {
        return
            $request instanceof ResolveNextRoute &&
            $request->getFirstModel() instanceof PaymentInterface
        ;
    }
}
