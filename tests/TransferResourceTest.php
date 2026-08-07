<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
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
                <orderdate>2/24/2006 12:11:22 PM</orderdate>
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
        ->ordered_at->toBe('2/24/2006 12:11:22 PM')
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

it('parses transfer orders using the documented enom field names', function (): void {
    $xml = <<<'XML'
    <interface-response>
        <TP_GetOrdersByDomain>
            <TransferOrder>
                <transferorderid>163475</transferorderid>
                <orderdate>2/24/2006 12:11:22 PM</orderdate>
                <orderstatus>Order In Processing</orderstatus>
                <statusid>13</statusid>
            </TransferOrder>
        </TP_GetOrdersByDomain>
    </interface-response>
    XML;

    $mock = new MockClient([
        GetTransferOrdersByDomain::class => MockResponse::make($xml),
    ]);

    Enom::fake($mock);

    $result = Enom::domains()->transfers()->all('example', 'com');

    expect($result)->toHaveCount(1);

    expect($result[0])
        ->order->toBe('163475')
        ->status->toBe('Order In Processing')
        ->status_id->toBe('13')
        ->ordered_at->toBe('2/24/2006 12:11:22 PM');

    expect($result[0]->orderedAt())
        ->toBeInstanceOf(CarbonImmutable::class)
        ->format('Y-m-d H:i:s')->toBe('2006-02-24 12:11:22');
});

it('gets the latest transfer order regardless of response order', function (bool $newestFirst): void {
    $mock = new MockClient([
        GetTransferOrdersByDomain::class => MockResponse::make(transferOrdersXml($newestFirst)),
    ]);

    Enom::fake($mock);

    $result = Enom::domains()->transfers()->latest('example', 'com');

    expect($result)
        ->toBeInstanceOf(TransferOrder::class)
        ->order->toBe('100')
        ->status->toBe('Active');
})->with(['newest first' => true, 'oldest first' => false]);

it('prefers a dated transfer order over undated ones', function (): void {
    $xml = <<<'XML'
    <interface-response>
        <TP_GetOrdersByDomain>
            <TransferOrder>
                <transferorderid>300</transferorderid>
                <orderstatus>Cancelled</orderstatus>
            </TransferOrder>
            <TransferOrder>
                <transferorderid>200</transferorderid>
                <orderdate>1/15/2024 3:22:41 PM</orderdate>
                <orderstatus>Completed</orderstatus>
            </TransferOrder>
            <TransferOrder>
                <transferorderid>400</transferorderid>
                <orderstatus>Cancelled</orderstatus>
            </TransferOrder>
        </TP_GetOrdersByDomain>
    </interface-response>
    XML;

    $mock = new MockClient([
        GetTransferOrdersByDomain::class => MockResponse::make($xml),
    ]);

    Enom::fake($mock);

    expect(Enom::domains()->transfers()->latest('example', 'com'))
        ->order->toBe('200');
});

it('prefers the documented field names over the legacy ones', function (): void {
    $xml = <<<'XML'
    <interface-response>
        <TP_GetOrdersByDomain>
            <TransferOrder>
                <transferorderid>100</transferorderid>
                <OrderID>999</OrderID>
                <orderstatus>Active</orderstatus>
                <status>Legacy</status>
            </TransferOrder>
        </TP_GetOrdersByDomain>
    </interface-response>
    XML;

    $mock = new MockClient([
        GetTransferOrdersByDomain::class => MockResponse::make($xml),
    ]);

    Enom::fake($mock);

    expect(Enom::domains()->transfers()->all('example', 'com')[0])
        ->order->toBe('100')
        ->status->toBe('Active');
});

it('returns null for a missing, empty or unparseable order date', function (?string $date): void {
    $order = new TransferOrder(
        order: '100',
        sld: 'example',
        tld: 'com',
        ordered_at: $date,
    );

    expect($order->orderedAt())->toBeNull();
})->with([
    'missing' => null,
    'empty' => '',
    'whitespace' => ' ',
    'unparseable' => 'not-a-date',
]);

it('returns null when there are no transfer orders', function (): void {
    $xml = <<<'XML'
    <interface-response>
        <TP_GetOrdersByDomain />
    </interface-response>
    XML;

    $mock = new MockClient([
        GetTransferOrdersByDomain::class => MockResponse::make($xml),
    ]);

    Enom::fake($mock);

    expect(Enom::domains()->transfers()->latest('example', 'com'))->toBeNull();
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

function transferOrdersXml(bool $newestFirst): string
{
    // The newest order deliberately has the lowest id: selection must use
    // the order date, not the response position or a numeric id.
    $newest = <<<'XML'
    <TransferOrder>
        <transferorderid>100</transferorderid>
        <orderdate>6/1/2026 10:00:00 AM</orderdate>
        <orderstatus>Active</orderstatus>
        <statusid>1</statusid>
    </TransferOrder>
    XML;

    $oldest = <<<'XML'
    <TransferOrder>
        <transferorderid>999</transferorderid>
        <orderdate>1/15/2024 3:22:41 PM</orderdate>
        <orderstatus>Completed</orderstatus>
        <statusid>6</statusid>
    </TransferOrder>
    XML;

    $orders = $newestFirst ? $newest.$oldest : $oldest.$newest;

    return "<interface-response><TP_GetOrdersByDomain>{$orders}</TP_GetOrdersByDomain></interface-response>";
}
