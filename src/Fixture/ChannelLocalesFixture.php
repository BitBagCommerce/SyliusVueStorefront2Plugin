<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Fixture;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class ChannelLocalesFixture extends AbstractFixture
{
    private const ADDITIONAL_LOCALE_CODE = 'de_DE';

    private ChannelRepositoryInterface $channelRepository;

    private RepositoryInterface $localeRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        RepositoryInterface $localeRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->channelRepository = $channelRepository;
        $this->localeRepository = $localeRepository;
        $this->entityManager = $entityManager;
    }

    public function getName(): string
    {
        return 'channel_locales';
    }

    public function load(array $options): void
    {
        $channels = $this->channelRepository->findAll();
        if (count($channels) === 0) {
            return;
        }

        /** @var LocaleInterface|null $additionalLocale */
        $additionalLocale = $this->localeRepository->findOneBy(['code' => self::ADDITIONAL_LOCALE_CODE]);
        if ($additionalLocale === null) {
            return;
        }

        /** @var ChannelInterface $channel */
        foreach ($channels as $channel) {
            $channel->addLocale($additionalLocale);
        }

        $this->entityManager->flush();
    }

    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {

    }
}
