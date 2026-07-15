<?php

namespace Tests\Support;

use RuntimeException;

final class TestingDatabaseGuard
{
    public static function ensureSafe(?string $databaseName): void
    {
        if ($databaseName === null || stripos($databaseName, 'test') === false) {
            $activeDatabase = $databaseName ?? '(unknown)';

            throw new RuntimeException(
                "Refusing to run database tests against [{$activeDatabase}]. The database name must contain 'test'.",
            );
        }
    }
}
