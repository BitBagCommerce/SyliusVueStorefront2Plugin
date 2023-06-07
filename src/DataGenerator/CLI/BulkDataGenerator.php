<?php
/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 *  We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\CLI;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\ProductFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\Channel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webmozart\Assert\Assert;

class BulkDataGenerator extends Command
{
    protected static $defaultName = 'vsf2:generate-bulk-data';

    private const DEFAULT_QTY = 100000;
    private const DEFAULT_DEPTH = 10;

    private EntityManagerInterface $entityManager;
    private ChannelRepositoryInterface $channelRepository;
    private ProductFactoryInterface $productFactory;

    private SymfonyStyle $io;
    private string $channelCode;

    public function __construct(
        EntityManagerInterface $entityManager,
        ChannelRepositoryInterface $channelRepository,
        ProductFactoryInterface $productFactory,
    ) {
        parent::__construct();

        $this->channelRepository = $channelRepository;
        $this->productFactory = $productFactory;
        $this->entityManager = $entityManager;
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
        $this->channelCode = $this->askForChannelCode();
        $productsQty = $this->askForInteger("Products");
        $taxonsQty = $this->askForInteger('Taxons');
        $wishlistsQty = $this->askForInteger('Wishlists');
        $productsPerTaxonQty = 0;
        $taxonMaxDepth = 0;
        $taxonChildrenPerLevel = 0;
        $productsPerWishlistQty = 0;

        if ($taxonsQty > 0) {
            $productsPerTaxonQty = $this->askForInteger('Max products per taxon');
            $taxonMaxDepth = $this->askForInteger('Taxon max depth', self::DEFAULT_DEPTH);
            $taxonChildrenPerLevel = $this->askForInteger('Taxon children per level', 1);
        }

        if ($wishlistsQty > 0) {
            $productsPerWishlistQty = $this->askForInteger('Max products per wishlist');
        }

        # Generate data
        $this->io->info(sprintf(
            '%s Generating data for channel %s...',
                (new \DateTime())->format('Y-m-d H:i:s'),
                $this->channelCode
        ));

        $this->io->info(sprintf('%s Generating products', (new \DateTime())->format('Y-m-d H:i:s')));
        $this->generateProducts($productsQty);

        # TODO: generate products
        # TODO: generate taxons
        # TODO: attach products to taxons
        # TODO: generate wishlists
        # TODO: attach products to wishlists
        # TODO: attach wishlists to users

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

    private function askForInteger(string $subject, int $default = self::DEFAULT_QTY): int
    {
        $quantity = $this->io->ask("$subject:", "$default") ?? $default;
        Assert::integerish($quantity);

        return max((int)$quantity, 0);
    }

    private function askForChannelCode(): string
    {
        $channels = [];
        /** @var Channel $channel */
        foreach ($this->channelRepository->findAll() as $channel) {
            $channels[$channel->getCode()] = $channel;
        }

        return $this->io->choice('Channel', array_keys($channels));
    }

    private function generateProducts(int $productsQty): void
    {
        $this->io->progressStart($productsQty);

        for ($i = 1; $i <= $productsQty; $i++) {
            $product = $this->productFactory->create($this->channelCode);

            $this->entityManager->persist($product);
            $this->io->progressAdvance();

            if ($i % 1000 === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
        }

        $this->entityManager->flush();
        $this->entityManager->clear();

        $this->io->progressFinish();
    }
}
