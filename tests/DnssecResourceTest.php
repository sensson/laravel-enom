<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sensson\Enom\Data\DnssecRecord;
use Sensson\Enom\Facades\Enom;
use Sensson\Enom\Requests\Dnssec\AddDnsSec;
use Sensson\Enom\Requests\Dnssec\DeleteDnsSec;
use Sensson\Enom\Requests\Dnssec\GetDnsSec;

it('gets dnssec records for a domain', function (): void {
    $xml = <<<'XML'
    <interface-response>
        <KeyTag>12345</KeyTag>
        <Algorithm>8</Algorithm>
        <DigestType>2</DigestType>
        <Digest>ABCDEF1234567890</Digest>
        <KeyTag>54321</KeyTag>
        <Algorithm>13</Algorithm>
        <DigestType>2</DigestType>
        <Digest>0987654321FEDCBA</Digest>
        <ErrCount>0</ErrCount>
    </interface-response>
    XML;

    $mock = new MockClient([
        GetDnsSec::class => MockResponse::make($xml),
    ]);

    Enom::fake($mock);

    $result = Enom::domain('example', 'com')->dnssec()->get();

    expect($result)
        ->toBeArray()
        ->toHaveCount(2);

    expect($result[0])
        ->toBeInstanceOf(DnssecRecord::class)
        ->key_tag->toBe(12345)
        ->algorithm->toBe(8)
        ->digest_type->toBe(2)
        ->digest->toBe('ABCDEF1234567890');

    expect($result[1])
        ->toBeInstanceOf(DnssecRecord::class)
        ->key_tag->toBe(54321)
        ->algorithm->toBe(13)
        ->digest->toBe('0987654321FEDCBA');

    $mock->assertSent(function (GetDnsSec $request): bool {
        return $request->query()->get('Command') === 'GetDnsSec'
            && $request->query()->get('SLD') === 'example'
            && $request->query()->get('TLD') === 'com';
    });
});

it('returns no dnssec records when none are set', function (): void {
    $mock = new MockClient([
        GetDnsSec::class => MockResponse::make('<interface-response><ErrCount>0</ErrCount></interface-response>'),
    ]);

    Enom::fake($mock);

    $result = Enom::domain('example', 'com')->dnssec()->get();

    expect($result)->toBeArray()->toBeEmpty();
});

it('adds a dnssec record to a domain', function (): void {
    $mock = new MockClient([
        AddDnsSec::class => MockResponse::make('<interface-response><ErrCount>0</ErrCount></interface-response>'),
    ]);

    Enom::fake($mock);

    $record = new DnssecRecord(key_tag: 12345, algorithm: 8, digest_type: 2, digest: 'ABCDEF1234567890');

    $result = Enom::domain('example', 'com')->dnssec()->add($record);

    expect($result)
        ->toBeInstanceOf(DnssecRecord::class)
        ->key_tag->toBe(12345);

    $mock->assertSent(function (AddDnsSec $request): bool {
        return $request->query()->get('Command') === 'AddDnsSec'
            && $request->query()->get('SLD') === 'example'
            && $request->query()->get('TLD') === 'com'
            && $request->query()->get('KeyTag') === 12345
            && $request->query()->get('Alg') === 8
            && $request->query()->get('DigestType') === 2
            && $request->query()->get('Digest') === 'ABCDEF1234567890';
    });
});

it('removes a dnssec record from a domain', function (): void {
    $mock = new MockClient([
        DeleteDnsSec::class => MockResponse::make('<interface-response><ErrCount>0</ErrCount></interface-response>'),
    ]);

    Enom::fake($mock);

    $record = new DnssecRecord(key_tag: 12345, algorithm: 8, digest_type: 2, digest: 'ABCDEF1234567890');

    Enom::domain('example', 'com')->dnssec()->remove($record);

    $mock->assertSent(function (DeleteDnsSec $request): bool {
        return $request->query()->get('Command') === 'DeleteDnsSec'
            && $request->query()->get('SLD') === 'example'
            && $request->query()->get('TLD') === 'com'
            && $request->query()->get('KeyTag') === 12345
            && $request->query()->get('Alg') === 8
            && $request->query()->get('DigestType') === 2
            && $request->query()->get('Digest') === 'ABCDEF1234567890';
    });
});
