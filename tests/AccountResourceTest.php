<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sensson\Enom\Data\AccountBalance;
use Sensson\Enom\Facades\Enom;
use Sensson\Enom\Requests\Account\GetBalance;

it('gets the account balance', function (): void {
    $mock = new MockClient([
        GetBalance::class => MockResponse::make('<interface-response><balance>250.00</balance><currency>USD</currency></interface-response>'),
    ]);

    Enom::fake($mock);

    $result = Enom::account()->balance();

    expect($result)
        ->toBeInstanceOf(AccountBalance::class)
        ->balance->toBe(250.0)
        ->currency->toBe('USD');

    $mock->assertSent(function (GetBalance $request): bool {
        return $request->query()->get('Command') === 'GetBalance';
    });
});
