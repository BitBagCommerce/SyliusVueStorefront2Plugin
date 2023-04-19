<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Command;

use BitBag\SyliusVueStorefront2Plugin\Doctrine\Repository\TaxonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Generator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductTaxonRepository;
use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductRepository;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Faker\Factory;
use Webmozart\Assert\Assert;

final class GenerateProductsCommand extends Command
{
    protected static $defaultName = 'vsf2:generate-products';

    public function __construct(
        private ProductRepository $productRepository,
        private FactoryInterface $productFactory,
        private EntityManagerInterface $entityManager,
        private TaxonRepository $taxonRepository,
        private FactoryInterface $productVariantFactory,
        private ChannelRepositoryInterface $channelRepository,
        private FactoryInterface $channelPricingFactory,
        private ProductTaxonRepository $productTaxonRepository
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('vsf2:generate-products')
            ->setDescription('Generate a given number of products.')
            ->addOption('number-of-products', null, InputOption::VALUE_REQUIRED, 'The number of products to generate.')
            ->addOption('channel-code', null, InputOption::VALUE_REQUIRED, 'Channel for products');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $numberOfProducts = $this->getValueFromOption($input, 'number-of-products');
        $channelCode = $this->getValueFromOption($input, 'channel-code');

        $faker = Factory::create();

        $channel = $this->channelRepository->findOneByCode($channelCode);

        Assert::notNull($channel, sprintf('The channel was not found with code "%s"',$channelCode));

        $startTime = microtime(true);

        for ($i = 1; $i <= $numberOfProducts; ++$i) {
            $product = $this->createProduct($faker, $channel);

            $this->productRepository->add($product);
        }

        $this->entityManager->flush();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $output->writeln(sprintf('Generated %d products for %s channel in %f seconds.', $numberOfProducts, $channel->getName(), $executionTime));

        return 0;
    }

    private function getValueFromOption(InputInterface $input, string $optionName): string|int
    {
        $option = $input->getOption($optionName);
        Assert::notNull($option, $optionName . ' option cannot be empty');

        return $option;
    }

    private function createProduct(Generator $faker, ChannelInterface $channel): ProductInterface
    {
        /** @var ProductInterface $product */
        $product = $this->productFactory->createNew();

        $uuid = Uuid::uuid4();

        $product->setSlug($faker->slug . '-' . $uuid);
        $product->setEnabled(true);
        $product->setName($faker->words(3, true));
        $product->setShortDescription($faker->sentence);
        $product->setDescription($faker->paragraphs(3, true));
        $product->setCode(sprintf('CODE-%s', $uuid));
        $product->setCreatedAt($faker->dateTimeBetween('-1 year'));
        $product->addChannel($channel);
        $this->setTaxonsForProduct($product);

        $variant = $this->createProductVariant($uuid, $faker, $channel);

        $product->addVariant($variant);

        return $product;
    }

    private function createProductVariant(
        UuidInterface $uuid,
        Generator $faker,
        ChannelInterface $channel
    ): ProductVariantInterface {
        $variant = $this->productVariantFactory->createNew();
        $variant->setCode(sprintf('CODE-%s', $uuid));
        $variant->setName(sprintf('Product %s', $uuid));

        $channelPricing = $this->channelPricingFactory->createNew();
        $channelPricing->setPrice($faker->randomNumber());
        $channelPricing->setChannelCode($channel->getCode());
        $variant->addChannelPricing($channelPricing);

        return $variant;
    }

    private function setTaxonsForProduct(ProductInterface $product): void
    {
        try {
            $taxons = $this->taxonRepository->findAll();
            $taxonProducts = $this->productTaxonRepository->findAll();

            if ($taxons && $taxonProducts) {
                $taxonsCount = count($taxons);
                $taxonProductsCount = count($taxonProducts);

                $product->setMainTaxon($taxons[random_int(0, $taxonsCount - 1)]);
                $product->addProductTaxon($taxonProducts[random_int(0, $taxonProductsCount - 1)]);
            }
        } catch (\Throwable $exception) {
            throw new \Exception('Something went wrong during assigning taxonomy to product');
        }
    }
}
