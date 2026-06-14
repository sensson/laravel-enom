<?php

declare(strict_types=1);

use Sensson\Enom\Data\DnssecRecord;
use Sensson\Enom\Facades\Enom;

$sld = 'sensson-test-domain';
$tld = 'com';

$record = new DnssecRecord(
    key_tag: 12345,
    algorithm: 8,
    digest_type: 2,
    digest: 'E2D3C916F6DEEAC73294E8268FB5885044A833FC5459588F4A9184CFC41A5766',
);

it('lists dnssec records for a domain', function () use ($sld, $tld): void {
    $records = Enom::domain($sld, $tld)->dnssec()->get();

    expect($records)->toBeArray();
});

it('adds a dnssec record', function () use ($sld, $tld, $record): void {
    $result = Enom::domain($sld, $tld)->dnssec()->add($record);

    expect($result)->toBeInstanceOf(DnssecRecord::class);
})->depends('it lists dnssec records for a domain');

it('finds the added dnssec record', function () use ($sld, $tld, $record): void {
    $records = Enom::domain($sld, $tld)->dnssec()->get();

    $keyTags = array_map(fn (DnssecRecord $dnssecRecord): int => $dnssecRecord->key_tag, $records);

    expect($keyTags)->toContain($record->key_tag);
})->depends('it adds a dnssec record');

it('removes a dnssec record', function () use ($sld, $tld, $record): void {
    Enom::domain($sld, $tld)->dnssec()->remove($record);

    $records = Enom::domain($sld, $tld)->dnssec()->get();

    $keyTags = array_map(fn (DnssecRecord $dnssecRecord): int => $dnssecRecord->key_tag, $records);

    expect($keyTags)->not->toContain($record->key_tag);
})->depends('it finds the added dnssec record');
