<?php

declare(strict_types=1);

namespace Renttek\SouinCache\Model;

use Laminas\Http\Client;
use Laminas\Http\Header\GenericHeader;
use Laminas\Http\Headers;
use Laminas\Http\Request;
use Laminas\Uri\Uri;
use Renttek\SouinCache\Model\Config\Source\HeaderStyle;
use function Psl\Iter\all;
use function Psl\Vec\map;

readonly class SouinClient
{
    private const int DEFAULT_TIMEOUT = 30;

    public function __construct(
        private Config $config,
        private Server $server,
        private Client $httpClient = new Client(),
        private ?int   $timeout = self::DEFAULT_TIMEOUT,
    ) {}

    /**
     * @param list<string> $keys
     */
    public function purge(array $keys): bool
    {
        if ($this->timeout !== null) {
            $this->httpClient->setOptions([
                'timeout' => $this->timeout,
            ]);
        }

        $separator = $this->config->getKeyListStyle() === HeaderStyle::TYPE_SOUIN ? ', ' : ' ';
        $keyString = implode($separator, $keys);
        $requests  = map($this->server->getUris(), fn(Uri $uri): Request => $this->createPurgeRequest($uri, $keyString));

        return all(
            $requests,
            fn(Request $request): bool => $this->httpClient
                ->send($request)
                ->isSuccess(),
        );
    }

    public function flush(): bool
    {
        if ($this->timeout !== null) {
            $this->httpClient->setOptions([
                'timeout' => $this->timeout,
            ]);
        }

        return all(
            map($this->server->getUris(), $this->createFlushRequest(...)),
            fn(Request $request): bool => $this->httpClient->send($request)->isSuccess(),
        );
    }

    private function createPurgeRequest(Uri $uri, ?string $surrogateKeyValue = null): Request
    {
        $request = new Request();
        $request->setAllowCustomMethods(true);
        $request->setMethod('PURGE');
        $request->setVersion('1.1');
        $request->setUri((string)$uri);

        if ($surrogateKeyValue !== null) {
            $surrogateKeysHeader = new GenericHeader('Surrogate-Keys', $surrogateKeyValue);

            $headers = new Headers();
            $headers->addHeader($surrogateKeysHeader);
            $request->setHeaders($headers);
        }

        return $request;
    }

    private function createFlushRequest(Uri $uri): Request
    {
        $flushPath = rtrim($uri->getPath() ?? '', '/') . '/flush';
        $uri->setPath($flushPath);

        $request = new Request();
        $request->setAllowCustomMethods(true);
        $request->setMethod('PURGE');
        $request->setVersion('1.1');
        $request->setUri((string)$uri);

        return $request;
    }
}
