<?php

namespace App\Data\Magazines;

use Illuminate\Support\Str;
use InvalidArgumentException;
use JsonException;

final readonly class MagazineGroupKey
{
    private const VERSION = 1;

    /**
     * @param  list<int>  $caliberIds
     */
    private function __construct(
        public string $manufacturer,
        public ?string $modelName,
        public int $capacity,
        public array $caliberIds,
    ) {}

    /**
     * @param  iterable<int|string>  $caliberIds
     */
    public static function make(
        string $manufacturer,
        ?string $modelName,
        int $capacity,
        iterable $caliberIds,
    ): self {
        $normalizedManufacturer = self::normalize($manufacturer);

        if ($normalizedManufacturer === '') {
            throw new InvalidArgumentException('A magazine group manufacturer is required.');
        }

        if ($capacity < 1) {
            throw new InvalidArgumentException('A magazine group capacity must be at least one.');
        }

        $normalizedCaliberIds = [];

        foreach ($caliberIds as $caliberId) {
            if (filter_var($caliberId, FILTER_VALIDATE_INT) === false || (int) $caliberId <= 0) {
                throw new InvalidArgumentException('Magazine group caliber IDs must be positive integers.');
            }

            $normalizedCaliberIds[] = (int) $caliberId;
        }

        $normalizedCaliberIds = array_values(array_unique($normalizedCaliberIds));
        sort($normalizedCaliberIds, SORT_NUMERIC);

        return new self(
            $normalizedManufacturer,
            $modelName === null ? null : self::normalize($modelName),
            $capacity,
            $normalizedCaliberIds,
        );
    }

    public static function decode(string $encoded): self
    {
        if ($encoded === '' || preg_match('/^[A-Za-z0-9_-]+$/', $encoded) !== 1) {
            throw new InvalidArgumentException('The magazine group key is malformed.');
        }

        $paddingLength = (4 - strlen($encoded) % 4) % 4;
        $json = base64_decode(strtr($encoded.str_repeat('=', $paddingLength), '-_', '+/'), true);

        if ($json === false) {
            throw new InvalidArgumentException('The magazine group key is malformed.');
        }

        try {
            $payload = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidArgumentException('The magazine group key is malformed.', previous: $exception);
        }

        if (! is_array($payload) || array_keys($payload) !== ['v', 'manufacturer', 'model_name', 'capacity', 'caliber_ids']) {
            throw new InvalidArgumentException('The magazine group key payload is invalid.');
        }

        if ($payload['v'] !== self::VERSION
            || ! is_string($payload['manufacturer'])
            || (! is_string($payload['model_name']) && $payload['model_name'] !== null)
            || ! is_int($payload['capacity'])
            || ! is_array($payload['caliber_ids'])) {
            throw new InvalidArgumentException('The magazine group key payload is invalid.');
        }

        $key = self::make(
            $payload['manufacturer'],
            $payload['model_name'],
            $payload['capacity'],
            $payload['caliber_ids'],
        );

        if (! hash_equals($key->encode(), $encoded)) {
            throw new InvalidArgumentException('The magazine group key is not canonical.');
        }

        return $key;
    }

    public function encode(): string
    {
        try {
            $json = json_encode([
                'v' => self::VERSION,
                'manufacturer' => $this->manufacturer,
                'model_name' => $this->modelName,
                'capacity' => $this->capacity,
                'caliber_ids' => $this->caliberIds,
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        } catch (JsonException $exception) {
            throw new InvalidArgumentException('The magazine group key could not be encoded.', previous: $exception);
        }

        return rtrim(strtr(base64_encode($json), '+/', '-_'), '=');
    }

    private static function normalize(string $value): string
    {
        return Str::of($value)->squish()->lower()->toString();
    }
}
