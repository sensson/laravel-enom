<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Account;

use Saloon\Http\Response;
use Sensson\Enom\Data\AccountBalance;
use Sensson\Enom\Requests\EnomRequest;

class GetBalance extends EnomRequest
{
    protected function command(): string
    {
        return 'GetBalance';
    }

    public function createDtoFromResponse(Response $response): AccountBalance
    {
        $xml = $response->xml();

        return new AccountBalance(
            amount: (float) $xml->balance,
            currency: (string) ($xml->currency ?? 'USD'),
        );
    }
}
