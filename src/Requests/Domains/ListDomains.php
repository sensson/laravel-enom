<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Domains;

use Saloon\Http\Response;
use Sensson\Enom\Requests\EnomRequest;

class ListDomains extends EnomRequest
{
    protected function command(): string
    {
        return 'GetAllDomains';
    }

    /** @return array<string> */
    public function createDtoFromResponse(Response $response): array
    {
        $xml = $response->xml();
        $domains = [];

        foreach ($xml->GetAllDomains->DomainDetail ?? [] as $detail) {
            $name = (string) $detail->DomainName;

            if ($name !== '') {
                $domains[] = $name;
            }
        }

        return $domains;
    }
}
