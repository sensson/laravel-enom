<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sensson\Enom\Data\AccountBalance;
use Sensson\Enom\Facades\Enom;
use Sensson\Enom\Requests\Account\GetBalance;

it('gets the account balance', function (): void {
    $mock = new MockClient([
        GetBalance::class => MockResponse::make('<interface-response><Balance>50,000.00</Balance><AvailableBalance>50,700.00</AvailableBalance></interface-response>'),
    ]);

    Enom::fake($mock);

    $result = Enom::account()->balance();

    expect($result)
        ->toBeInstanceOf(AccountBalance::class)
        ->amount->toBe(50000.0)
        ->currency->toBe('USD');

    $mock->assertSent(function (GetBalance $request): bool {
        return $request->query()->get('Command') === 'GetBalance';
    });
});

it('validates the connection with test', function (): void {
    $mock = new MockClient([
        GetBalance::class => MockResponse::make('<interface-response><Balance>50,000.00</Balance><AvailableBalance>50,700.00</AvailableBalance></interface-response>'),
    ]);

    Enom::fake($mock);

    Enom::test();

    $mock->assertSent(GetBalance::class);
});
