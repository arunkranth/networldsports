<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;
use NetworldSports\CustomOrderProcessing\Api\Data\OrderStatusLogInterfaceFactory;
use NetworldSports\CustomOrderProcessing\Api\OrderStatusLogRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Order Status Change Observer
 */
class OrderStatusChangeObserver implements ObserverInterface
{
    /**
     * @var OrderStatusLogInterfaceFactory
     */
    private OrderStatusLogInterfaceFactory $orderStatusLogFactory;

    /**
     * @var OrderStatusLogRepositoryInterface
     */
    private OrderStatusLogRepositoryInterface $orderStatusLogRepository;

    /**
     * @var TransportBuilder
     */
    private TransportBuilder $transportBuilder;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param OrderStatusLogInterfaceFactory $orderStatusLogFactory
     * @param OrderStatusLogRepositoryInterface $orderStatusLogRepository
     * @param TransportBuilder $transportBuilder
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderStatusLogInterfaceFactory $orderStatusLogFactory,
        OrderStatusLogRepositoryInterface $orderStatusLogRepository,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    ) {
        $this->orderStatusLogFactory = $orderStatusLogFactory;
        $this->orderStatusLogRepository = $orderStatusLogRepository;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        try {
            /** @var Order $order */
            $order = $observer->getEvent()->getOrder();

            if (!$order || !$order->getId()) {
                return;
            }

            $oldStatus = $order->getOrigData('status');
            $newStatus = $order->getStatus();

            // Only log if status actually changed
            if ($oldStatus !== $newStatus) {
                $this->logStatusChange($order, $oldStatus, $newStatus);

                // Send shipment email if status changed to "Shipped"
                if (strtolower($newStatus) === 'shipped') {
                    $this->sendShipmentEmail($order);
                }
            }
        } catch (\Exception $e) {
            $this->logger->error('Error in order status change observer: ' . $e->getMessage());
        }
    }

    /**
     * Log status change
     *
     * @param Order $order
     * @param string|null $oldStatus
     * @param string $newStatus
     * @return void
     */
    private function logStatusChange(Order $order, ?string $oldStatus, string $newStatus): void
    {
        try {
            $log = $this->orderStatusLogFactory->create();
            $log->setOrderId((int)$order->getId());
            $log->setOldStatus($oldStatus);
            $log->setNewStatus($newStatus);
            $this->orderStatusLogRepository->save($log);
        } catch (\Exception $e) {
            $this->logger->error('Failed to log status change: ' . $e->getMessage());
        }
    }

    /**
     * Send shipment email
     *
     * @param Order $order
     * @return void
     */
    private function sendShipmentEmail(Order $order): void
    {
        try {
            if (!$order->getCustomerEmail()) {
                return;
            }

            $storeId = $order->getStoreId();
            $templateId = $this->scopeConfig->getValue(
                'sales_email/shipment/template',
                ScopeInterface::SCOPE_STORE,
                $storeId
            );

            $transport = $this->transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setTemplateOptions([
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $storeId
                ])
                ->setTemplateVars([
                    'order' => $order,
                    'shipment' => null,
                    'billing' => $order->getBillingAddress(),
                    'payment_html' => $this->getPaymentHtml($order),
                    'store' => $order->getStore()
                ])
                ->setFromByScope(
                    $this->scopeConfig->getValue(
                        'sales_email/shipment/identity',
                        ScopeInterface::SCOPE_STORE,
                        $storeId
                    ),
                    $storeId
                )
                ->addTo($order->getCustomerEmail(), $order->getCustomerName())
                ->getTransport();

            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->logger->error('Failed to send shipment email: ' . $e->getMessage());
        }
    }

    /**
     * Get payment HTML
     *
     * @param Order $order
     * @return string
     */
    private function getPaymentHtml(Order $order): string
    {
        try {
            return $order->getPayment()->getMethodInstance()->getTitle();
        } catch (\Exception $e) {
            return '';
        }
    }
}
