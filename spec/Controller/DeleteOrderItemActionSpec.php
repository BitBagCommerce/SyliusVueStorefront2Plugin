<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusGraphqlPlugin\Controller;

use BitBag\SyliusGraphqlPlugin\Command\Cart\RemoveItemFromCart;
use BitBag\SyliusGraphqlPlugin\Controller\DeleteOrderItemAction;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class DeleteOrderItemActionSpec extends ObjectBehavior
{
    function let(MessageBusInterface $commandBus): void
    {
        $this->beConstructedWith($commandBus);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(DeleteOrderItemAction::class);
    }

    function it_is_invokable(
        MessageBusInterface $commandBus
    ): void {
        $attributes = [
            'id' => 'id',
            'itemId' => 11,
        ];
        $request = new Request([], [], $attributes);

        $command = new RemoveItemFromCart(
            (string) $request->attributes->get('id'),
            (string) $request->attributes->get('itemId'),
        );

        $envelope = Envelope::wrap($command);

        $commandBus->dispatch($command)->willReturn($envelope);

        $this->__invoke($request)->shouldBeLike(new JsonResponse());
    }
}
