<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sensson\Enom\Data\Nameserver;
use Sensson\Enom\Facades\Enom;
use Sensson\Enom\Requests\Nameservers\GetNameservers;
use Sensson\Enom\Requests\Nameservers\UpdateNameservers;

it('gets nameservers for a domain', function (): void {
    $xml = <<<'XML'
    <interface-response>
        <GetDNS>
            <dns>
                <NS1>ns1.example.com</NS1>
                <NS2>ns2.example.com</NS2>
            </dns>
        </GetDNS>
    </interface-response>
    XML;

    $mock = new MockClient([
        GetNameservers::class => MockResponse::make($xml),
    ]);

    Enom::fake($mock);

    $result = Enom::domains()->nameservers()->get('example', 'com');

    expect($result)->toBeArray()->toHaveCount(2);
    expect($result[0])->toBeInstanceOf(Nameserver::class);
    expect(array_map(fn (Nameserver $nameserver): string => $nameserver->host, $result))
        ->toContain('ns1.example.com')
        ->toContain('ns2.example.com');

    $mock->assertSent(function (GetNameservers $request): bool {
        return $request->query()->get('Command') === 'GetDNS'
            && $request->query()->get('SLD') === 'example'
            && $request->query()->get('TLD') === 'com';
    });
});

it('updates nameservers for a domain', function (): void {
    $mock = new MockClient([
        UpdateNameservers::class => MockResponse::make('<interface-response><RRPCode>200</RRPCode></interface-response>'),
    ]);

    Enom::fake($mock);

    $result = Enom::domains()->nameservers()->update('example', 'com', [
        new Nameserver('ns1.myhost.com'),
        new Nameserver('ns2.myhost.com'),
    ]);

    expect($result)->toBeArray();
    expect(array_map(fn (Nameserver $nameserver): string => $nameserver->host, $result))
        ->toContain('ns1.myhost.com')
        ->toContain('ns2.myhost.com');

    $mock->assertSent(function (UpdateNameservers $request): bool {
        return $request->query()->get('Command') === 'ModifyNS'
            && $request->query()->get('NS1') === 'ns1.myhost.com'
            && $request->query()->get('NS2') === 'ns2.myhost.com';
    });
});
