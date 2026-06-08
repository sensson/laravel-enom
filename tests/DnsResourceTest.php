<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sensson\Enom\Data\DnsRecord;
use Sensson\Enom\Enums\DnsRecordType;
use Sensson\Enom\Facades\Enom;
use Sensson\Enom\Requests\Dns\GetDnsHosts;
use Sensson\Enom\Requests\Dns\SetDnsHosts;

it('gets dns records for a domain', function (): void {
    $xml = <<<'XML'
    <interface-response>
        <GetHosts>
            <Host>
                <HostName>www</HostName>
                <RecordType>A</RecordType>
                <Address>1.2.3.4</Address>
                <MXPref>0</MXPref>
                <TTL>300</TTL>
            </Host>
            <Host>
                <HostName>mail</HostName>
                <RecordType>MX</RecordType>
                <Address>mail.example.com</Address>
                <MXPref>10</MXPref>
                <TTL>300</TTL>
            </Host>
        </GetHosts>
    </interface-response>
    XML;

    $mock = new MockClient([
        GetDnsHosts::class => MockResponse::make($xml),
    ]);

    Enom::fake($mock);

    $result = Enom::domains()->dns('example', 'com')->get();

    expect($result)
        ->toBeArray()
        ->toHaveCount(2);

    expect($result[0])
        ->toBeInstanceOf(DnsRecord::class)
        ->hostname->toBe('www')
        ->type->toBe(DnsRecordType::A)
        ->address->toBe('1.2.3.4')
        ->ttl->toBe(300);

    expect($result[1])
        ->toBeInstanceOf(DnsRecord::class)
        ->hostname->toBe('mail')
        ->type->toBe(DnsRecordType::MX)
        ->mx_preference->toBe(10);

    $mock->assertSent(function (GetDnsHosts $request): bool {
        return $request->query()->get('Command') === 'GetDNSHost'
            && $request->query()->get('SLD') === 'example'
            && $request->query()->get('TLD') === 'com';
    });
});

it('sets dns records for a domain', function (): void {
    $mock = new MockClient([
        SetDnsHosts::class => MockResponse::make('<interface-response><RRPCode>200</RRPCode></interface-response>'),
    ]);

    Enom::fake($mock);

    $records = [
        new DnsRecord(hostname: 'www', type: DnsRecordType::A, address: '1.2.3.4', ttl: 300),
        new DnsRecord(hostname: '@', type: DnsRecordType::MX, address: 'mail.example.com', ttl: 300, mx_preference: 10),
    ];

    $result = Enom::domains()->dns('example', 'com')->update($records);

    expect($result)->toBeArray()->toHaveCount(2);

    $mock->assertSent(function (SetDnsHosts $request): bool {
        return $request->query()->get('Command') === 'SetDNSHost'
            && $request->query()->get('HostName1') === 'www'
            && $request->query()->get('RecordType1') === 'A'
            && $request->query()->get('Address1') === '1.2.3.4'
            && $request->query()->get('HostName2') === '@'
            && $request->query()->get('RecordType2') === 'MX'
            && $request->query()->get('MXPref2') === 10;
    });
});
