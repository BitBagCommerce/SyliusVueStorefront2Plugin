<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\Controller;

use BitBag\SyliusVueStorefront2Plugin\Controller\ThankYouPageController;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

final class ThankYouPageControllerSpec extends ObjectBehavior
{
    private const VSF2_HOST = 'http://localhost/';

    public function let(RouterInterface $router): void
    {
        $this->beConstructedWith(self::VSF2_HOST, $router);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ThankYouPageController::class);
    }

    public function it_redirects_to_homepage_if_host_not_provided(
        RouterInterface $router,
        Request $request,
    ): void {
        $this->beConstructedWith(null, $router);

        $url = '/en_US/';
        $router->generate('sylius_shop_homepage')->willReturn($url);

        $response = $this->redirectAction($request);

        $response->shouldBeAnInstanceOf(RedirectResponse::class);
        $response->getTargetUrl()->shouldReturn($url);
    }

    public function it_redirects_to_homepage_if_order_number_is_not_provided(
        RouterInterface $router,
    ): void {
        $attributes = [];
        $request = new Request([], [], $attributes);

        $url = '/en_US/';
        $router->generate('sylius_shop_homepage')->willReturn($url);

        $response = $this->redirectAction($request);

        $response->shouldBeAnInstanceOf(RedirectResponse::class);
        $response->getTargetUrl()->shouldReturn($url);
    }

    function it_redirects_to_thank_you_page(): void
    {
        $locale = 'de_DE';
        $orderNumber = '123';
        $request = new Request(['locale' => $locale], [], ['orderNumber' => $orderNumber]);

        $url = sprintf(
            '%s/%s/checkout/thank-you?order=%s',
            self::VSF2_HOST,
            locale_get_primary_language($locale),
            $orderNumber
        );

        $response = $this->redirectAction($request);

        $response->shouldBeAnInstanceOf(RedirectResponse::class);
        $response->getTargetUrl()->shouldReturn($url);
    }
}
