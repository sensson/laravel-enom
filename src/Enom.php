<?php

declare(strict_types=1);

namespace Sensson\Enom;

use Saloon\Http\Connector;
use Saloon\Http\Response;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;
use Sensson\Enom\Exceptions\EnomException;
use Sensson\Enom\Resources\AccountResource;
use Sensson\Enom\Resources\DomainsResource;
use SimpleXMLElement;
use Throwable;

class Enom extends Connector
{
    use AlwaysThrowOnErrors;

    public function __construct(
        protected string $username,
        protected string $token,
        protected bool $sandbox = true,
    ) {
        //
    }

    public function resolveBaseUrl(): string
    {
        return $this->sandbox
            ? 'https://resellertest.enom.com'
            : 'https://reseller.enom.com';
    }

    public function hasRequestFailed(Response $response): bool
    {
        return $response->status() >= 400 || $this->errorMessage($response) !== null;
    }

    public function getRequestException(Response $response, ?Throwable $senderException): ?Throwable
    {
        $message = $this->errorMessage($response);

        if ($message === null) {
            return parent::getRequestException($response, $senderException);
        }

        return EnomException::fromMessage($message);
    }

    protected function defaultQuery(): array
    {
        return [
            'UID' => $this->username,
            'PW' => $this->token,
            'ResponseType' => 'xml',
        ];
    }

    public function domains(): DomainsResource
    {
        return new DomainsResource($this);
    }

    public function account(): AccountResource
    {
        return new AccountResource($this);
    }

    /**
     * Enom returns HTTP 200 even on failure, with the error inside the XML body.
     */
    protected function errorMessage(Response $response): ?string
    {
        $xml = $response->xml();

        if (! isset($xml->errors)) {
            return null;
        }

        return collect($xml->errors->children())
            ->map(fn (SimpleXMLElement $error): string => trim((string) $error))
            ->filter()
            ->implode('; ') ?: null;
    }
}
