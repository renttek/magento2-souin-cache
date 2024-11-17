<?php

declare(strict_types=1);

namespace Renttek\SouinCache\Model\CacheFlusher;

use Renttek\SouinCache\Model\CacheFlusher;
use Renttek\SouinCache\Model\SouinClient;

readonly class DefaultCacheFlusher implements CacheFlusher
{
    public function __construct(
        private SouinClient $souinClient,
    ) {}

    public function flushKeys(array $keys): bool
    {
        return $this->souinClient->purge($keys);
    }

    public function flushAll(): bool
    {
        return $this->souinClient->flush();
    }
}
