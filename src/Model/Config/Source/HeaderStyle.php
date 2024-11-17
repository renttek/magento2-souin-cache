<?php

declare(strict_types=1);

namespace Renttek\SurrogateKey\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

readonly class HeaderStyle implements OptionSourceInterface
{
    public const string TYPE_SOUIN  = 'souin';
    public const string TYPE_FASTLY = 'fastly';

    /**
     * @phpstan-return list<array{label: string, value: string}>
     */
    public function toOptionArray(): array
    {
        return [
            [
                'label' => 'Souin',
                'value' => self::TYPE_SOUIN,
            ],
            [
                'label' => 'Fastly',
                'value' => self::TYPE_FASTLY,
            ],
        ];
    }
}
