<?php

declare(strict_types=1);

namespace Renttek\SouinCache\Observer;

use Magento\CacheInvalidate\Observer\FlushAllCacheObserver as MagentoFlushAllCacheObserver;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\PageCache\Model\Config as PageCacheConfig;
use Renttek\SouinCache\Model\CacheFlusher;
use Renttek\SouinCache\Model\Config as SouinCacheConfig;

readonly class FlushAllCacheObserver implements ObserverInterface
{
    public function __construct(
        private PageCacheConfig              $pagecacheConfig,
        private SouinCacheConfig             $config,
        private MagentoFlushAllCacheObserver $magentoFlushAllCacheObserver,
        private CacheFlusher                 $cacheFlusher,
    ) {}

    public function execute(Observer $observer): void
    {
        if (!$this->config->isEnabled()) {
            $this->magentoFlushAllCacheObserver->execute($observer);
            return;
        }

        if (!$this->shouldSendPurge()) {
            return;
        }

        $this->cacheFlusher->flushAll();
    }

    private function shouldSendPurge(): bool
    {
        return $this->pagecacheConfig->isEnabled()
            && $this->pagecacheConfig->getType() === PageCacheConfig::VARNISH
            && $this->config->isEnabled();
    }
}
