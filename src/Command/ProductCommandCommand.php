<?php

namespace App\Command;

use App\Doctrine\EnumStatusExtendedType;
use App\Repository\ProductRepository;
use App\Service\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ProductCommandCommand
 * @package App\Command
 */
class ProductCommandCommand extends Command
{


    /** @var string */
    protected static $defaultName = 'app:product:command';
    /** @var EntityManagerInterface $em */
    private $em;
    /** @var ProductRepository $productRepository */
    private $productRepository;
    /** @var Notification $notification */
    private $notification;

    /**
     * ProductCommandCommand constructor.
     * @param EntityManagerInterface $em
     * @param ProductRepository $productRepository
     * @param Notification $notification
     */
    public function __construct(
        EntityManagerInterface $em,
        ProductRepository $productRepository,
        Notification $notification
    )
    {
        $this->em = $em;
        $this->productRepository = $productRepository;
        $this->notification = $notification;
        parent::__construct();
    }

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setDescription(
                'Send a notification for products with a specific status for more than a week'
            )
            ->addArgument(
                'status',
                InputArgument::OPTIONAL,
                'The status to look for. Default = ' . EnumStatusExtendedType::STATUS_PENDING
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $status = $input->getArgument('status');
        if (!$status) $status = EnumStatusExtendedType::STATUS_PENDING;
        if (!in_array($status, EnumStatusExtendedType::STATUSES))
            throw new InvalidArgumentException(
                'Invalid status. Statuses accepted : ' .
                implode(', ', EnumStatusExtendedType::STATUSES)
            );
        if ($status !== 'pending')
            throw new InvalidArgumentException(
                'We\'re sorry, for the moment the only status supported is ' .
                EnumStatusExtendedType::STATUS_PENDING
            );
        $products = $this->productRepository->findByStatusAndBeforeCreatedAt(
            $status, new \DateTime('-7 days')
        );
        foreach ($products as $product) {
            //We create a notification for the author of the product
            $notification = $this->notification->new(
                Notification::PENDING_PRODUCT,
                $product->getAuthor()
            );
            $notification
                ->setProduct($product);
            $this->em->persist($notification);
        }
        $this->em->flush();
        $countProducts = count($products);
        $productString = $countProducts > 1 ? 'products' : 'product';
        $notificationString = $countProducts > 1 ? 'notifications' : 'notification';
        $io->success(
            $countProducts === 0 ? 'No product match your criteria, so no notifications where sent' :
                "$countProducts $productString $notificationString where sent"
        );
    }
}