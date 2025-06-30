<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Controller\Adminhtml\Status;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use NetworldSports\CustomOrderProcessing\Api\CustomOrderStatusRepositoryInterface;
use NetworldSports\CustomOrderProcessing\Api\Data\CustomOrderStatusInterfaceFactory;
use NetworldSports\CustomOrderProcessing\Model\CustomOrderStatus;

/**
 * Save Controller
 */
class Save extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level
     */
    const ADMIN_RESOURCE = 'NetworldSports_CustomOrderProcessing::status_add';

    /**
     * @var CustomOrderStatusRepositoryInterface
     */
    private CustomOrderStatusRepositoryInterface $customOrderStatusRepository;

    /**
     * @var CustomOrderStatusInterfaceFactory
     */
    private CustomOrderStatusInterfaceFactory $customOrderStatusFactory;

    /**
     * @var TypeListInterface
     */
    private TypeListInterface $cacheTypeList;

    /**
     * @var DataPersistorInterface
     */
    private DataPersistorInterface $dataPersistor;

    /**
     * @param Context $context
     * @param CustomOrderStatusRepositoryInterface $customOrderStatusRepository
     * @param CustomOrderStatusInterfaceFactory $customOrderStatusFactory
     * @param TypeListInterface $cacheTypeList
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        CustomOrderStatusRepositoryInterface $customOrderStatusRepository,
        CustomOrderStatusInterfaceFactory $customOrderStatusFactory,
        TypeListInterface $cacheTypeList,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->customOrderStatusRepository = $customOrderStatusRepository;
        $this->customOrderStatusFactory = $customOrderStatusFactory;
        $this->cacheTypeList = $cacheTypeList;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * Execute action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $statusId = isset($data['status_id']) ? (int)$data['status_id'] : null;

            if ($statusId) {
                $model = $this->customOrderStatusRepository->getById($statusId);
            } else {
                $model = $this->customOrderStatusFactory->create();
            }

            $model->setStatusName($data['status_name'] ?? '');
            $model->setIsEnabled((bool)($data['is_enabled'] ?? false));

            $this->customOrderStatusRepository->save($model);
            $this->messageManager->addSuccessMessage(__('The status has been saved.'));
            $this->dataPersistor->clear('custom_order_status');

            // Invalidate cache
            $this->cacheTypeList->invalidate(CustomOrderStatus::CACHE_TAG);

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['status_id' => $model->getStatusId()]);
            }
        } catch (LocalizedException $e) {
            $statusName = $data['status_name'] ?? '';
            $this->messageManager->addErrorMessage(
                __('A status with the name "%1" already exists. Please choose a different name.', $statusName)
            );
            return $this->processRedirectAfterFailure($resultRedirect, $data);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving the status.'));
            return $this->processRedirectAfterFailure($resultRedirect, $data);
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Process redirect after failure
     *
     * @param \Magento\Framework\Controller\Result\Redirect $resultRedirect
     * @param array $data
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    private function processRedirectAfterFailure($resultRedirect, array $data)
    {
        $this->dataPersistor->set('custom_order_status', $data);

        if (isset($data['status_id'])) {
            return $resultRedirect->setPath('*/*/edit', ['status_id' => $data['status_id']]);
        }
        return $resultRedirect->setPath('*/*/new');
    }
}
