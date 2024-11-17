<?php

declare(strict_types=1);

namespace Renttek\SouinCache\Observer;

use Magento\CacheInvalidate\Observer\InvalidateVarnishObserver;
use Magento\Framework\App\Cache\Tag\Resolver as CacheTagResolver;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\PageCache\Model\Config as PageCacheConfig;
use Renttek\SouinCache\Model\CacheFlusher;
use Renttek\SouinCache\Model\Config as SouinCacheConfig;

readonly class InvalidateSouinObserver implements ObserverInterface
{
    public function __construct(
        private PageCacheConfig           $pagecacheConfig,
        private SouinCacheConfig          $config,
        private InvalidateVarnishObserver $invalidateVarnishObserver,
        private CacheTagResolver          $tagResolver,
        private CacheFlusher              $cacheFlusher,
    ) {}

    public function execute(Observer $observer): void
    {
        if (!$this->config->isEnabled()) {
            $this->invalidateVarnishObserver->execute($observer);
            return;
        }

        if (!$this->shouldSendPurge()) {
            return;
        }

        $object = $observer->getEvent()->getDataUsingMethod('object');
        if (!is_object($object)) {
            return;
        }

        /** @var list<string> $cacheTags */
        $cacheTags = $this->tagResolver->getTags($object);
        if ($cacheTags === []) {
            return;
        }

        $this->cacheFlusher->flushKeys($cacheTags);
    }

    private function shouldSendPurge(): bool
    {
        return $this->pagecacheConfig->isEnabled()
            && $this->pagecacheConfig->getType() === PageCacheConfig::VARNISH
            && $this->config->isEnabled();
    }
}
