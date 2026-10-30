<?php

declare(strict_types=1);

namespace Tests;

use App\Config;
use App\OidcClient;
use PHPUnit\Framework\TestCase;

final class StateTest extends TestCase
{
    public function testInvalidStateIsRejectedAndConsumed(): void
    {
        $_SESSION = ['oidc' => ['state' => 'expected', 'nonce' => 'nonce', 'verifier' => 'verifier']];
        $client = new OidcClient(new Config('tenant', 'client', 'secret', 'https://example.com'));
        try {
            $client->exchangeAndValidate('code', 'wrong');
            self::fail('Expected invalid state to be rejected');
        } catch (\RuntimeException $exception) {
            self::assertSame('Invalid state', $exception->getMessage());
            self::assertArrayNotHasKey('oidc', $_SESSION);
        }
    }
}
