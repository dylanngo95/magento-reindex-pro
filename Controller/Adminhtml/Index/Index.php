<?php

/**
 * Jundat
 *
 * Copyright © Jundat LLC. All rights reserved.
 * See COPYING.txt for license details.
 * http://www.jundat95.com | it.tinhngo@gmail.com
 */

namespace Jundat\Reindex\Controller\Adminhtml\Index;

use Jundat\Reindex\Model\Config\Source\ListMode;
use Magento\Framework\App\ResponseInterface;

class Index extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Indexer\Model\IndexerFactory
     */
    protected $indexerFactory;

    /**
     * @var \Jundat\Reindex\Helper\Configuration
     */
    protected $config;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Jundat\Reindex\Helper\Configuration $configuration,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory
    )
    {
        parent::__construct($context);
        $this->config = $configuration;
        $this->indexerFactory = $indexerFactory;
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $indexerIds = $this->getRequest()->getParam('indexer_ids');
        if (is_array($indexerIds)) {

            $reindexMode = $this->config->getConfigValue(
                'reindex/general/list_mode',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ) ?? 'async';

            switch ($reindexMode) {
                case ListMode::ASYNC:
                    $this->asyncReindex($indexerIds);
                    break;
                case ListMode::SYNC:
                    $this->syncReindex($indexerIds);
                    break;
                default:
                    $this->syncReindex($indexerIds);
                    break;
            }

        } else {
            $this->messageManager->addErrorMessage('Please select indexers');
        }

        $this->_redirect('*/indexer/list/');
    }

    /**
     * asynchronous reindex
     * @param $indexerIds
     * @throws \Exception
     */
    private function syncReindex($indexerIds) {
        try {
            foreach ($indexerIds as $indexerId) {
                $indexer = $this->indexerFactory->create();
                $indexer->load($indexerId)->reindexAll();
            }

            $this->messageManager->addSuccessMessage(
                __('Reindex %1 indexer(s).', count($indexerIds))
            );
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __("Reindex is fail")
            );
        }
    }

    /**
     * synchronous reindex
     * @param $indexerIds
     */
    private function aSyncReindex($indexerIds) {
        $currentDir = explode('\\', getcwd());
        $rootProject = str_replace('pub', '', $currentDir);
        try {
            foreach ($indexerIds as $item) {
                $command = $rootProject[0].'bin/magento indexer:reindex '.$item;
                $this->execInBackground($command);
            }
            $this->messageManager->addSuccessMessage( __('%1 indexer(s) is processing', count($indexerIds)));
        } catch (Exception $ex) {
            $this->messageManager->addErrorMessage('Reindex is fail');
        }
    }

    /**
     * exec Command In Background both Windows and Unix
     *
     * @param $cmd
     */
    private function execInBackground($cmd) {
        if (substr(php_uname(), 0, 7) == "Windows"){
            pclose(popen("start /B ". $cmd, "r"));
        }
        else {
            exec($cmd . " > /dev/null &");
        }
    }

    /**
     * @return bool
     */
    protected function _isAllowed() {
        switch ($this->_request->getActionName()) {
            case 'index':
                return $this->_authorization->isAllowed('Jundat_Reindex::Index');
        }
        return false;
    }
}