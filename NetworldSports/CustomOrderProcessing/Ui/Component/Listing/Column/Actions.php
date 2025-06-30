<?php
/**
 * Copyright © NetworldSports All rights reserved.
 */
declare(strict_types=1);

namespace NetworldSports\CustomOrderProcessing\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Actions Column
 */
class Actions extends Column
{
    /**
     * URL builder
     *
     * @var UrlInterface
     */
    protected UrlInterface $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['status_id'])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                'customorderprocessing/status/edit',
                                ['status_id' => $item['status_id']]
                            ),
                            'label' => __('Edit')
                        ],
                        'enable' => [
                            'href' => $this->urlBuilder->getUrl(
                                'customorderprocessing/status/enable',
                                ['status_id' => $item['status_id']]
                            ),
                            'label' => __('Enable')
                        ],
                        'disable' => [
                            'href' => $this->urlBuilder->getUrl(
                                'customorderprocessing/status/disable',
                                ['status_id' => $item['status_id']]
                            ),
                            'label' => __('Disable')
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                'customorderprocessing/status/delete',
                                ['status_id' => $item['status_id']]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete Status'),
                                'message' => __('Are you sure you want to delete this status? Orders with this status will be set to their default state status.')
                            ]
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
