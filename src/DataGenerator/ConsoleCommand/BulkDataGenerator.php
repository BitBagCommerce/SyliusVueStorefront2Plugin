<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\ConsoleCommand;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\DataGeneratorCommandContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Context\DataGeneratorCommandContextFactoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\BulkGeneratorInterface;
use DateTime;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webmozart\Assert\Assert;

final class BulkDataGenerator extends Command implements BulkDataGeneratorInterface
{
    protected static $defaultName = 'vsf2:generate-bulk-data';

    private SymfonyStyle $io;

    private ChannelRepositoryInterface $channelRepository;

    private BulkGeneratorInterface $compositeBulkGenerator;

    private DataGeneratorCommandContextFactoryInterface $commandContextFactory;

    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        BulkGeneratorInterface $compositeBulkGenerator,
        DataGeneratorCommandContextFactoryInterface $commandContextFactory,
    ) {
        parent::__construct();

        $this->channelRepository = $channelRepository;
        $this->compositeBulkGenerator = $compositeBulkGenerator;
        $this->commandContextFactory = $commandContextFactory;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        if (!$this->confirmRunningCommand()) {
            $this->io->info('Aborting...');

            return 0;
        }

        $context = $this->gatherInputParams();

        $this->io->info(sprintf(
            '%s Generating data for channel %s...',
                (new DateTime())->format('Y-m-d H:i:s'),
                $context->getChannel()->getCode()
        ));

        $this->compositeBulkGenerator->generate($context);

        $this->io->info(sprintf('%s Command finished', (new DateTime())->format('Y-m-d H:i:s')));

        return 1;
    }

    protected function configure(): void
    {
        $help = <<<HELP
When you run the command, interactive questions about the parameters of the data to be generated will appear.
If no value for a question will be provided, the suggested value will be assumed.
Channel                     Channel on which the data should be generated. If there is only one channel in the system, it will be set by default
Products                    A number of products to generate
Taxons                      A number of taxons to generate
Wishlists                   A number of wishlists to generate
Taxon max depth             Maximum level of nested taxons
Taxon children per level    Maximum number of children taxons a parent taxon can have (excluding main taxon)
Max products per taxon      Maximum number of products related to a single taxon. Final number of relations will be random
Max products per wishlist   Maximum number of products related to a single wishlist. Final number of relations will be random
Stress                      The probability of creating the number of relations between the entities to be greater than 80% of the given max
HELP;

        $this->setDescription('Generates random data by the given parameters.');
        $this->setHelp($help);
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

    private function gatherInputParams(): DataGeneratorCommandContextInterface
    {
        $channel = $this->askForChannel();
        $productsQty = $this->askForInteger('Products', self::DEFAULT_PRODUCTS_QTY);
        $taxonsQty = $this->askForInteger('Taxons', self::DEFAULT_TAXONS_QTY);
        $wishlistsQty = $this->askForInteger('Wishlists', self::DEFAULT_WISHLISTS_QTY);
        $productsPerTaxonQty = 0;
        $maxTaxonLevel = 0;
        $maxChildrenPerTaxonLevel = 0;
        $productsPerWishlistQty = 0;

        if ($taxonsQty > 0) {
            $maxTaxonLevel = $this->askForInteger(
                'Taxon max depth',
                self::DEFAULT_MAX_TAXON_LEVEL
            );
            $maxChildrenPerTaxonLevel = $this->askForInteger(
                'Taxon children per level',
                self:: DEFAULT_MAX_CHILDREN_PER_TAXON_LEVEL
            );

            if ($productsQty > 0) {
                $productsPerTaxonQty = $this->askForInteger(
                    'Max products per taxon',
                    self::DEFAULT_PRODUCTS_PER_TAXON_QTY
                );
            }
        }

        if ($wishlistsQty > 0 && $productsQty > 0) {
            $productsPerWishlistQty = $this->askForInteger(
                'Max products per wishlist',
                self::DEFAULT_PRODUCTS_PER_WISHLIST_QTY
            );
        }

        $stress = $this->askForInteger('Stress', self::DEFAULT_STRESS);
        $stress = $stress <= 100 ? $stress : self::DEFAULT_STRESS;

        return $this->commandContextFactory->fromInput(
            $this->io,
            $channel,
            $productsQty,
            $taxonsQty,
            $wishlistsQty,
            $productsPerTaxonQty,
            $maxTaxonLevel,
            $maxChildrenPerTaxonLevel,
            $productsPerWishlistQty,
            $stress,
        );
    }
}
