<?php

declare(strict_types=1);

namespace Renttek\SouinCache\Model;

interface CacheFlusher
{
    /**
     * @param list<string> $keys
     */
    public function flushKeys(array $keys): bool;

    public function flushAll(): bool;
}
