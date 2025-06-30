<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Controller\Adminhtml\Status;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use NetworldSports\CustomOrderProcessing\Api\CustomOrderStatusRepositoryInterface;

/**
 * Edit Controller
 */
class Edit extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level
     */
    const ADMIN_RESOURCE = 'NetworldSports_CustomOrderProcessing::status_add';

    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

    /**
     * @var CustomOrderStatusRepositoryInterface
     */
    private CustomOrderStatusRepositoryInterface $customOrderStatusRepository;

    /**
     * @var Registry
     */
    private Registry $registry;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param CustomOrderStatusRepositoryInterface $customOrderStatusRepository
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CustomOrderStatusRepositoryInterface $customOrderStatusRepository,
        Registry $registry
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->customOrderStatusRepository = $customOrderStatusRepository;
        $this->registry = $registry;
    }

    /**
     * Execute action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $statusId = (int)$this->getRequest()->getParam('status_id');

        if ($statusId) {
            try {
                $model = $this->customOrderStatusRepository->getById($statusId);
                $this->registry->register('custom_order_status', $model);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('This status no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('NetworldSports_CustomOrderProcessing::status');
        $resultPage->addBreadcrumb(__('Sales'), __('Sales'));
        $resultPage->addBreadcrumb(__('Custom Order Statuses'), __('Custom Order Statuses'));
        $resultPage->addBreadcrumb(
            $statusId ? __('Edit Status') : __('New Status'),
            $statusId ? __('Edit Status') : __('New Status')
        );
        $resultPage->getConfig()->getTitle()->prepend(
            $statusId ? __('Edit Custom Order Status') : __('New Custom Order Status')
        );

        return $resultPage;
    }
}
