<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sensson\Enom\Data\TransferOrder;
use Sensson\Enom\Facades\Enom;
use Sensson\Enom\Requests\Transfers\CancelTransferOrder;
use Sensson\Enom\Requests\Transfers\GetTransferOrder;
use Sensson\Enom\Requests\Transfers\GetTransferOrdersByDomain;

it('gets a transfer order by id', function (): void {
    $xml = <<<'XML'
    <interface-response>
        <TP_GetOrder>
            <TransferOrder>
                <OrderID>12345</OrderID>
                <SLD>example</SLD>
                <TLD>com</TLD>
                <status>Active</status>
                <statusid>1</statusid>
            </TransferOrder>
        </TP_GetOrder>
    </interface-response>
    XML;

    $mock = new MockClient([
        GetTransferOrder::class => MockResponse::make($xml),
    ]);

    Enom::fake($mock);

    $result = Enom::domains()->transfers()->get('12345');

    expect($result)
        ->toBeInstanceOf(TransferOrder::class)
        ->order->toBe('12345')
        ->sld->toBe('example')
        ->tld->toBe('com')
        ->status->toBe('Active')
        ->status_id->toBe('1')
        ->name()->toBe('example.com');

    $mock->assertSent(function (GetTransferOrder $request): bool {
        return $request->query()->get('Command') === 'TP_GetOrder'
            && $request->query()->get('TransferOrderID') === '12345';
    });
});

it('gets transfer orders for a domain', function (): void {
    $xml = <<<'XML'
    <interface-response>
        <TP_GetOrdersByDomain>
            <TransferOrder>
                <OrderID>12345</OrderID>
                <statusid>1</statusid>
                <status>Active</status>
            </TransferOrder>
            <TransferOrder>
                <OrderID>67890</OrderID>
                <statusid>6</statusid>
                <status>Completed</status>
            </TransferOrder>
        </TP_GetOrdersByDomain>
    </interface-response>
    XML;

    $mock = new MockClient([
        GetTransferOrdersByDomain::class => MockResponse::make($xml),
    ]);

    Enom::fake($mock);

    $result = Enom::domains()->transfers()->all('example', 'com');

    expect($result)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(2);

    expect($result[0])
        ->toBeInstanceOf(TransferOrder::class)
        ->order->toBe('12345')
        ->status->toBe('Active');

    $mock->assertSent(function (GetTransferOrdersByDomain $request): bool {
        return $request->query()->get('Command') === 'TP_GetOrdersByDomain'
            && $request->query()->get('SLD') === 'example'
            && $request->query()->get('TLD') === 'com';
    });
});

it('cancels a transfer order', function (): void {
    $mock = new MockClient([
        CancelTransferOrder::class => MockResponse::make('<interface-response><RRPCode>200</RRPCode></interface-response>'),
    ]);

    Enom::fake($mock);

    Enom::domains()->transfers()->cancel('12345');

    $mock->assertSent(function (CancelTransferOrder $request): bool {
        return $request->query()->get('Command') === 'TP_CancelOrder'
            && $request->query()->get('TransferOrderID') === '12345';
    });
});
