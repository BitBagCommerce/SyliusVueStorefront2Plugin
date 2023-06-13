<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\ConsoleCommand;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webmozart\Assert\Assert;

class BulkDataGenerator extends Command implements BulkDataGeneratorInterface
{
    protected static $defaultName = 'vsf2:generate-bulk-data';

    private EntityManagerInterface $entityManager;
    private ChannelRepositoryInterface $channelRepository;

    private SymfonyStyle $io;
    private ChannelInterface $channel;

    public function __construct(
        EntityManagerInterface $entityManager,
        ChannelRepositoryInterface $channelRepository,
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->channelRepository = $channelRepository;
    }

    protected function configure(): void
    {
        $this->setDescription('Generates random data by the given parameters.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        if (!$this->confirmRunningCommand()) {
            $this->io->info('Aborting...');

            return 0;
        }

        # Gather custom parameters
        $this->channel = $this->askForChannelCode();
        $productsQty = $this->askForInteger('Products', self::DEFAULT_PRODUCTS_QTY);
        $taxonsQty = $this->askForInteger('Taxons', self::DEFAULT_TAXONS_QTY);
        $wishlistsQty = $this->askForInteger('Wishlists', self::DEFAULT_WISHLISTS_QTY);
        $maxTaxonLevel = 0;
        $maxChildrenPerTaxonLevel = 0;
        $productsPerTaxonQty = 0;
        $productsPerWishlistQty = 0;

        if ($taxonsQty > 0) {
            if ($productsQty > 0) {
                $productsPerTaxonQty = $this->askForInteger(
                    'Max products per taxon',
                    self::DEFAULT_PRODUCTS_PER_TAXON_QTY
                );
            }
            $maxTaxonLevel = $this->askForInteger(
                'Taxon max depth',
                self::DEFAULT_MAX_TAXON_LEVEL
            );
            $maxChildrenPerTaxonLevel = $this->askForInteger(
                'Taxon children per level',
                self:: DEFAULT_MAX_CHILDREN_PER_TAXON_LEVEL
            );
        }

        if ($wishlistsQty > 0 && $productsQty > 0) {
            $productsPerWishlistQty = $this->askForInteger(
                'Max products per wishlist',
                self::DEFAULT_PRODUCTS_PER_WISHLIST_QTY
            );
        }

        $this->io->info(sprintf(
            '%s Generating data for channel %s...',
                (new \DateTime())->format('Y-m-d H:i:s'),
                $this->channel->getCode()
        ));

        //

        $this->io->info(sprintf('%s Command finished', (new \DateTime())->format('Y-m-d H:i:s')));

        return 1;
    }

    private function confirmRunningCommand(): bool
    {
        return $this->io->confirm(
            'This command must not be executed on production environment. Are you sure you want to proceed?',
            false
        );
    }

    private function askForInteger(string $subject, int $default): int
    {
        $quantity = $this->io->ask("$subject:", "$default") ?? $default;
        Assert::integerish($quantity);

        return max((int)$quantity, 0);
    }

    private function askForChannelCode(): ChannelInterface
    {
        $channels = [];
        /** @var ChannelInterface $channel */
        foreach ($this->channelRepository->findAll() as $channel) {
            $channels[$channel->getCode()] = $channel;
        }

        if (count($channels) === 1) {
            return reset($channels);
        }

        $channelCode = $this->io->choice('Channel', array_keys($channels));

        return $channels[$channelCode];
    }
}
