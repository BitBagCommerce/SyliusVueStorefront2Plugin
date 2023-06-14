<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\ConsoleCommand;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\BulkContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\ProductContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\TaxonContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\WishlistContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\BulkGenerator;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\CompositeBulkGenerator;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\GeneratorInterface;
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

    private SymfonyStyle $io;

    private ChannelRepositoryInterface $channelRepository;

    private GeneratorInterface $productGenerator;

    private GeneratorInterface $taxonGenerator;

    private GeneratorInterface $wishlistGenerator;

    private ChannelInterface $channel;

    private int $productsQty = 0;

    private int $taxonsQty = 0;

    private int $wishlistsQty = 0;

    private int $productsPerTaxonQty = 0;

    private int $maxTaxonLevel = 0;

    private int $maxChildrenPerTaxonLevel = 0;

    private int $productsPerWishlistQty = 0;

    public function __construct(
        EntityManagerInterface $entityManager,
        ChannelRepositoryInterface $channelRepository,
        GeneratorInterface $productGenerator,
        GeneratorInterface $taxonGenerator,
        GeneratorInterface $wishlistGenerator
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->channelRepository = $channelRepository;
        $this->productGenerator = $productGenerator;
        $this->taxonGenerator = $taxonGenerator;
        $this->wishlistGenerator = $wishlistGenerator;
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

        $this->gatherInputParams();

        $this->io->info(sprintf(
            '%s Generating data for channel %s...',
                (new \DateTime())->format('Y-m-d H:i:s'),
                $this->channel->getCode()
        ));

        $productBulkGenerator = new BulkGenerator(
            $this->entityManager,
            $this->io,
            $this->productGenerator,
            new BulkContext($this->productsQty, new ProductContext($this->channel)),
        );

        $taxonBulkGenerator = new BulkGenerator(
            $this->entityManager,
            $this->io,
            $this->taxonGenerator,
            new BulkContext($this->taxonsQty, new TaxonContext($this->maxTaxonLevel, $this->maxChildrenPerTaxonLevel)),
        );

        $wishlistBulkGenerator = new BulkGenerator(
            $this->entityManager,
            $this->io,
            $this->wishlistGenerator,
            new BulkContext($this->wishlistsQty, new WishlistContext($this->channel)),
        );

        $compositeBulkGenerator = new CompositeBulkGenerator([
            $productBulkGenerator,
            $taxonBulkGenerator,
            $wishlistBulkGenerator,
        ]);

        $compositeBulkGenerator->generate();

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

    private function gatherInputParams(): void
    {
        $this->channel = $this->askForChannel();
        $this->productsQty = $this->askForInteger('Products', self::DEFAULT_PRODUCTS_QTY);
        $this->taxonsQty = $this->askForInteger('Taxons', self::DEFAULT_TAXONS_QTY);
        $this->wishlistsQty = $this->askForInteger('Wishlists', self::DEFAULT_WISHLISTS_QTY);

        if ($this->taxonsQty > 0) {
            $this->maxTaxonLevel = $this->askForInteger(
                'Taxon max depth',
                self::DEFAULT_MAX_TAXON_LEVEL
            );
            $this->maxChildrenPerTaxonLevel = $this->askForInteger(
                'Taxon children per level',
                self:: DEFAULT_MAX_CHILDREN_PER_TAXON_LEVEL
            );

            if ($this->productsQty > 0) {
                $this->productsPerTaxonQty = $this->askForInteger(
                    'Max products per taxon',
                    self::DEFAULT_PRODUCTS_PER_TAXON_QTY
                );
            }
        }

        if ($this->wishlistsQty > 0 && $this->productsQty > 0) {
            $this->productsPerWishlistQty = $this->askForInteger(
                'Max products per wishlist',
                self::DEFAULT_PRODUCTS_PER_WISHLIST_QTY
            );
        }
    }

    private function askForInteger(string $subject, int $default): int
    {
        $quantity = $this->io->ask("$subject:", "$default") ?? $default;
        Assert::integerish($quantity);

        return max((int)$quantity, 0);
    }

    private function askForChannel(): ChannelInterface
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
