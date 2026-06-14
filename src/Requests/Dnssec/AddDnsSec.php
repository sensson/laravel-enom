<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Dnssec;

use Saloon\Http\Response;
use Sensson\Enom\Data\DnssecRecord;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Requests\EnomRequest;

final class AddDnsSec extends EnomRequest
{
    public function __construct(
        private readonly DomainName $domain,
        private readonly DnssecRecord $record,
    ) {
        //
    }

    protected function command(): string
    {
        return 'AddDnsSec';
    }

    protected function parameters(): array
    {
        return [
            'SLD' => $this->domain->sld,
            'TLD' => $this->domain->tld,
            'KeyTag' => $this->record->key_tag,
            'Alg' => $this->record->algorithm,
            'DigestType' => $this->record->digest_type,
            'Digest' => $this->record->digest,
        ];
    }

    public function createDtoFromResponse(Response $response): DnssecRecord
    {
        return $this->record;
    }
}
