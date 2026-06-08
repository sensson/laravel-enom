<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

abstract class EnomRequest extends Request
{
    protected Method $method = Method::GET;

    abstract protected function command(): string;

    protected function parameters(): array
    {
        return [];
    }

    public function resolveEndpoint(): string
    {
        return '/interface.asp';
    }

    protected function defaultQuery(): array
    {
        return array_merge(['Command' => $this->command()], $this->parameters());
    }
}
