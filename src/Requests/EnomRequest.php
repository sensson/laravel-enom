<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use SimpleXMLElement;

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

    /**
     * Return the first non-empty value among the given element names. Enom's
     * documentation and its real responses do not always agree on element
     * names, so parsers list every known name for a field.
     */
    protected function field(SimpleXMLElement $element, string ...$names): ?string
    {
        foreach ($names as $name) {
            $value = trim((string) ($element->{$name} ?? ''));

            if ($value !== '') {
                return $value;
            }
        }

        return null;
    }
}
