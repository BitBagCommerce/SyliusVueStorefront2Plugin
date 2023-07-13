<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\Payum\Action;

use BitBag\SyliusVueStorefront2Plugin\Payum\Action\ResolveNextRouteAction;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PayumBundle\Request\ResolveNextRoute;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\InvalidArgumentException;

class ResolveNextRouteActionSpec extends ObjectBehavior
{
    public function let(RequestStack $requestStack): void
    {
        $this->beConstructedWith($requestStack);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ResolveNextRouteAction::class);
    }

    public function it_throws_exception_on_execute_with_invalid_request(): void
    {
        $msg = sprintf(
            'Expected an instance of %s. Got: %s',
            ResolveNextRoute::class,
            Request::class,
        );

        $request = new Request();

        $this->shouldThrow(new InvalidArgumentException($msg))->during('execute', [$request]);
    }

    public function it_throws_exception_on_execute_with_no_order(
        ResolveNextRoute $request,
        PaymentInterface $payment,
    ): void {
        $request->getFirstModel()->willReturn($payment);
        $payment->getOrder()->willReturn(null);
        $request->setRouteName(
            'bitbag_sylius_vue_storefront2_thank_you_page',
        )->shouldBeCalled();

        $msg = 'Expected a value other than null.';
        $this->shouldThrow(new InvalidArgumentException($msg))->during('execute', [$request]);
    }

    public function it_executes_with_the_given_request(
        ResolveNextRoute $request,
        PaymentInterface $payment,
        OrderInterface $order,
        RequestStack $requestStack,
        Request $symfonyRequest,
    ): void {
        $orderNumber = '123';
        $locale = 'de_DE';

        $request->getFirstModel()->willReturn($payment);
        $payment->getOrder()->willReturn($order);

        $order->getNumber()->willReturn($orderNumber);
        $requestStack->getCurrentRequest()->willReturn($symfonyRequest);
        $symfonyRequest->getLocale()->willReturn($locale);

        $this->execute($request);

        $params = [
            'orderNumber' => $orderNumber,
            'locale' => $locale,
        ];
        $request->setRouteName('bitbag_sylius_vue_storefront2_thank_you_page')
            ->shouldBeCalled();

        $request->setRouteParameters($params)->shouldBeCalled();
    }

    public function it_does_not_support_invalid_request(Request $symfonyRequest): void
    {
        $this->supports($symfonyRequest)->shouldReturn(false);
    }

    public function it_does_not_support_resolve_next_route_without_payment(ResolveNextRoute $request): void
    {
        $request->getFirstModel()->willReturn(null);

        $this->supports($request)->shouldReturn(false);
    }

    public function it_supports_resolve_next_route_with_payment(
        ResolveNextRoute $request,
        PaymentInterface $payment,
    ): void {
        $request->getFirstModel()->willReturn($payment);

        $this->supports($request)->shouldReturn(true);
    }
}
