<?php

declare(strict_types=1);

namespace Sensson\Enom\Resources;

use Saloon\Http\BaseResource;
use Sensson\Enom\Data\AccountBalance;
use Sensson\Enom\Requests\Account\GetBalance;

final class AccountResource extends BaseResource
{
    public function balance(): AccountBalance
    {
        return $this->connector->send(new GetBalance)->dto();
    }
}
