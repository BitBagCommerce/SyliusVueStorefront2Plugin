<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\CLI\Internal;

use BitBag\SyliusVueStorefront2Plugin\Doctrine\Repository\TaxonRepositoryInterface;
use DateTime;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Faker\Generator;
use Ramsey\Uuid\Uuid;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\Taxon;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslation;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webmozart\Assert\Assert;

final class GenerateRandomData extends Command
{
    protected static $defaultName = 'vsf2:generate-mass-data';

    private const DEFAULT_QTY = 100000;
    private const DEFAULT_DEPTH = 10;

    private SymfonyStyle $io;
    private Generator $faker;

    private string $channelCode;
    private TaxonInterface $defaultParentTaxon;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ChannelRepositoryInterface $channelRepository,
        private TaxonRepositoryInterface $taxonRepository,
        private FactoryInterface $productFactory,
        private FactoryInterface $productVariantFactory,
        private FactoryInterface $channelPricingFactory,
        private FactoryInterface $taxonFactory,
    ) {
        parent::__construct();
        $this->faker = Factory::create();
    }

    protected function configure(): void
    {
        $this->setDescription('Generates large amounts of random data.');
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $confirmation = $this->io->confirm(
            'This command must not be executed on production environment. Are you sure you want to proceed?',
            false
        );
        if (!$confirmation) {
            $this->io->info('Aborting...');

            return 0;
        }

        # Gather custom parameters
        $this->askForChannelCode();

        $taxonsQty = $this->askForInteger('Taxons') ?? self::DEFAULT_QTY;
        $productsPerTaxonQty = 0;
        $taxonMaxDepth = 0;
        if ($taxonsQty > 0) {
            $productsPerTaxonQty = $this->askForInteger('Max products per taxon') ?? self::DEFAULT_QTY;
            $taxonMaxDepth = $this->askForInteger('Taxon max depth', self::DEFAULT_DEPTH) ?? self::DEFAULT_DEPTH;
        }

        $wishlistsQty = $this->askForInteger('Wishlists') ?? self::DEFAULT_QTY;
        $productsPerWishlistQty = 0;
        if ($wishlistsQty > 0) {
            $productsPerWishlistQty = $this->askForInteger('Max products per wishlist') ?? self::DEFAULT_QTY;
        }

        $minProducts = max($productsPerTaxonQty, $productsPerWishlistQty);
        $productsQty = $this->askForInteger("Products (at least $minProducts)") ?? self::DEFAULT_QTY;
        $productsQty = max($productsQty, $minProducts);

        # Generate data
        $this->io->info(
            sprintf(
                '%s Generating data for channel %s...',
                (new DateTime())->format('Y-m-d H:i:s'),
                $this->channelCode
            )
        );

        $firstProductId = $this->getNextId('sylius_product');
        $this->generateProducts($productsQty);
        $this->attachProductsToChannel($firstProductId, $productsQty);

        $firstTaxonId = $this->getNextId('sylius_taxon');
        $this->generateTaxons($taxonsQty, $taxonMaxDepth);

        $this->attachProductsToTaxons($firstTaxonId, $taxonsQty, $firstProductId, $productsQty, $productsPerTaxonQty);

        # TODO: generate wishlists
        # TODO: attach products to wishlists

        $this->io->info(sprintf('%s Command finished', (new DateTime())->format('Y-m-d H:i:s')));

        return 1;
    }

    private function askForInteger(string $subject, int $default = 100000): int
    {
        $quantity = $this->io->ask("$subject:", "$default");
        Assert::integerish($quantity);

        return max((int)$quantity, 0);
    }

    private function askForChannelCode(): void
    {
        $channels = [];
        /** @var Channel $channel */
        foreach ($this->channelRepository->findAll() as $channel) {
            $channels[$channel->getCode()] = $channel;
        }

        $this->channelCode = $this->io->choice('Channel', array_keys($channels));

        $channels = null;
    }

    private function setDefaultParentTaxon(): void
    {
        /** @var Taxon $mainTaxon */
        $mainTaxon = $this->taxonRepository->findOneBy(['parent' => null]);

        $this->defaultParentTaxon = $mainTaxon;
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    private function getNextId(string $tableName): int
    {
        $sql = "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_NAME = '$tableName'";

        return (int)$this->entityManager
            ->getConnection()
            ->executeQuery($sql)
            ->fetchFirstColumn()[0];
    }

    /**
     * @throws \Exception
     */
    private function generateProducts(int $quantity): void
    {
        $this->io->info('Generating products:');
        $this->io->progressStart($quantity);

        for ($i = 1; $i <= $quantity; $i++) {
            $product = $this->createProduct();
            $this->entityManager->persist($product);

            $this->io->progressAdvance();

            if ($i % 100 === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
        }

        $this->entityManager->flush();
        $this->entityManager->clear();

        $this->io->progressFinish();
    }

    /**
     * @throws \Exception
     */
    private function createProduct(): ProductInterface
    {
        /** @var ProductInterface $product */
        $product = $this->productFactory->createNew();

        $uuid = Uuid::uuid4()->toString();

        $product->setName($uuid);
        $product->setSlug($this->faker->slug . '-' . $uuid);
        $product->setCode(sprintf('Code-%s', $uuid));
        $product->setDescription($uuid);
        $product->setShortDescription($this->faker->sentence);
        $product->setEnabled(true);
        $product->setCreatedAt($this->faker->dateTimeBetween('-1 year'));

        /** @var ProductVariantInterface $variant */
        $variant = $this->productVariantFactory->createNew();
        $variant->setCode(sprintf('Code-%s', $uuid));
        $variant->setName(sprintf('Product %s', $uuid));

        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $this->channelPricingFactory->createNew();
        $channelPricing->setPrice($this->faker->randomNumber());
        $channelPricing->setChannelCode($this->channelCode);

        $variant->addChannelPricing($channelPricing);

        $product->addVariant($variant);

        return $product;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function attachProductsToChannel(int $firstProductId, mixed $productsQty): void
    {
        $channelId = $this->channelRepository->findOneByCode($this->channelCode)?->getId();
        $sql = function (array $values): string {
             return 'INSERT INTO sylius_product_channels(product_id, channel_id) VALUES ' . implode(', ', $values);
        };

        $this->io->info('Attaching products to channel:');
        $this->io->progressStart($productsQty);

        $values = [];
        $counter = 0;
        for ($i = $firstProductId; $i < $firstProductId + $productsQty; ++$i) {
            $values[] = "($i, $channelId)";
            $counter++;
            $this->io->progressAdvance();

            if ($counter % 1000 === 0) {
                $this->entityManager->getConnection()->executeStatement($sql($values));
                $values = [];
            }
        }

        if (count($values) !== 0) {
            $this->entityManager->getConnection()->executeStatement($sql($values));
        }

        $this->io->progressFinish();
    }

    /**
     * @throws \Exception
     */
    private function generateTaxons(int $quantity, int $maxDepth): void
    {
        $this->io->info('Generating taxons:');
        $this->io->progressStart($quantity);

        $this->setDefaultParentTaxon();

        $depth = 0;
        $taxon = null;
        $blockHavingChild = true;   # first taxon has to be a child of default taxon
        for ($i = 1; $i <= $quantity; $i++) {
            if (!$blockHavingChild && $this->faker->boolean && $depth < $maxDepth) {
                $parent = $taxon;
                $depth++;
            } else {
                $parent = $this->defaultParentTaxon;
                $depth = 0;
            }
            Assert::notNull($parent, 'Parent taxon cannot be null');
            $taxon = $this->createTaxon($parent);
            $this->entityManager->persist($taxon);

            $this->io->progressAdvance();
            if ($i % 100 === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();

                $blockHavingChild = true;   # forbid flushed parent having a child taxon
                $this->setDefaultParentTaxon();

                continue;
            }

            $blockHavingChild = false;
        }

        $this->entityManager->flush();
        $this->entityManager->clear();

        $this->io->progressFinish();
    }

    /**
     * @throws \Exception
     */
    private function createTaxon(?TaxonInterface $parent = null): TaxonInterface
    {
        $uuid = Uuid::uuid4()->toString();

        /** @var TaxonInterface $taxon */
        $taxon = $this->taxonFactory->createNew();
        $taxon->setCode('code-' . $uuid);
        $taxon->setParent($parent);
        $taxon->setposition(0);

        $translation = new TaxonTranslation();
        $translation->setName($uuid);
        $translation->setSlug($this->faker->slug);
        $translation->setLocale('en_US');

        $taxon->addTranslation($translation);

        return $taxon;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function attachProductsToTaxons(
        int $firstTaxonId,
        int $taxonQty,
        int $firstProductId,
        int $productQty,
        int $maxProductsPerTaxon,
    ): void {
        $distributed = $this->distributeAToB(
            $firstProductId,
            $productQty,
            $firstTaxonId,
            $taxonQty,
            $maxProductsPerTaxon
        );

        $sql = function (array $values): string {
            return 'INSERT INTO sylius_product_taxon(product_id, taxon_id, position) VALUES ' . implode(', ', $values);
        };

        $values = [];
        $counter = 0;
        foreach ($distributed as $taxonId => $productIds) {
            foreach ($productIds as $k => $productId) {
                $values[] = "($productId, $taxonId, $k)";
                $counter++;

                if ($counter % 1000 === 0) {
                    $this->entityManager->getConnection()->executeStatement($sql($values));
                    $values = [];
                }
            }
        }

        if (count($values) !== 0) {
            $this->entityManager->getConnection()->executeStatement($sql($values));
        }
    }

    private function distributeAToB(int $firstAId, int $aQty, int $firstBId, int $bQty, int $maxAPerB): array
    {
        $aIds = [];
        for ($i = $firstAId; $i < $firstAId + $aQty; $i++) {
            $aIds[$i] = $i;
        }

        $firstAThresholdId = (int)max(1, $aQty * 0.05);
        $secondAThresholdId = (int)max(1, $aQty * 0.9);

        $firstBThresholdId = $firstBId + floor($bQty * 0.1);
        $secondBThresholdId = $firstBId + floor($bQty * 0.9);

        $distributed = [];
        for ($i = $firstBId; $i < $firstBId + $bQty; $i++) {
            if ($i < $firstBThresholdId) {
                $count = rand(1, $firstAThresholdId);
            } elseif ($i < $secondBThresholdId) {
                $count = rand($firstAThresholdId + 1, $secondAThresholdId);
            } else {
                $count = rand($secondAThresholdId + 1, $aQty);
            }

            $distributed[$i] = (array)array_rand($aIds, $count);
        }

        return $distributed;
    }
}
