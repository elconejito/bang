<?php

namespace Tests\Unit\Data\Magazines;

use App\Data\Magazines\MagazineGroupKey;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MagazineGroupKeyTest extends TestCase
{
    public function test_it_encodes_equivalent_group_facts_canonically(): void
    {
        $first = MagazineGroupKey::make('  Heckler   & Koch ', '  VP9  ', 17, [8, 3, 8]);
        $second = MagazineGroupKey::make('heckler & koch', 'vp9', 17, ['3', '8']);

        $this->assertSame($first->encode(), $second->encode());
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9_-]+$/', $first->encode());
        $this->assertStringNotContainsString('=', $first->encode());
    }

    public function test_it_round_trips_normalized_group_facts(): void
    {
        $encoded = MagazineGroupKey::make('  Smith   & Wesson ', null, 30, [12, 2])->encode();
        $decoded = MagazineGroupKey::decode($encoded);

        $this->assertSame('smith & wesson', $decoded->manufacturer);
        $this->assertNull($decoded->modelName);
        $this->assertSame(30, $decoded->capacity);
        $this->assertSame([2, 12], $decoded->caliberIds);
        $this->assertSame($encoded, $decoded->encode());
    }

    public function test_null_and_empty_model_names_remain_distinct(): void
    {
        $nullModel = MagazineGroupKey::make('Glock', null, 17, [1]);
        $emptyModel = MagazineGroupKey::make('Glock', '', 17, [1]);

        $this->assertNotSame($nullModel->encode(), $emptyModel->encode());
    }

    public function test_it_rejects_a_capacity_below_one(): void
    {
        $this->expectException(InvalidArgumentException::class);

        MagazineGroupKey::make('Glock', 'OEM', 0, [1]);
    }

    #[DataProvider('invalidKeys')]
    public function test_it_rejects_malformed_or_noncanonical_keys(string $encoded): void
    {
        $this->expectException(InvalidArgumentException::class);

        MagazineGroupKey::decode($encoded);
    }

    /** @return array<string, array{string}> */
    public static function invalidKeys(): array
    {
        return [
            'empty' => [''],
            'invalid alphabet' => ['not+a+key'],
            'invalid json' => [self::encodeRaw('not json')],
            'unsupported version' => [self::encodeRaw('{"v":2,"manufacturer":"glock","model_name":null,"capacity":17,"caliber_ids":[1]}')],
            'unsorted calibers' => [self::encodeRaw('{"v":1,"manufacturer":"glock","model_name":null,"capacity":17,"caliber_ids":[2,1]}')],
            'unknown property' => [self::encodeRaw('{"v":1,"manufacturer":"glock","model_name":null,"capacity":17,"caliber_ids":[1],"extra":true}')],
        ];
    }

    private static function encodeRaw(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
