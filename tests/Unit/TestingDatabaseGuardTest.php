<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tests\Support\TestingDatabaseGuard;

class TestingDatabaseGuardTest extends TestCase
{
    #[DataProvider('safeDatabaseNames')]
    public function test_it_allows_database_names_containing_test_case_insensitively(string $databaseName): void
    {
        TestingDatabaseGuard::ensureSafe($databaseName);

        $this->addToAssertionCount(1);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function safeDatabaseNames(): array
    {
        return [
            'lowercase' => ['bang_testing'],
            'uppercase' => ['BANG_TEST'],
            'mixed case' => ['bang_Testing'],
        ];
    }

    #[DataProvider('unsafeDatabaseNames')]
    public function test_it_rejects_database_names_without_test(?string $databaseName): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("The database name must contain 'test'.");

        TestingDatabaseGuard::ensureSafe($databaseName);
    }

    /**
     * @return array<string, array{string|null}>
     */
    public static function unsafeDatabaseNames(): array
    {
        return [
            'development database' => ['bang'],
            'production database' => ['bang_production'],
            'unknown database' => [null],
        ];
    }
}
