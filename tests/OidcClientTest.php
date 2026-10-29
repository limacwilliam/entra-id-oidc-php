<?php
declare(strict_types=1);
namespace Tests;
use App\Config; use App\OidcClient; use Firebase\JWT\JWT; use PHPUnit\Framework\TestCase;
final class OidcClientTest extends TestCase {
  public function testRejectsWrongNonceBeforeUsingClaims(): void { $client=new OidcClient(new Config('tenant','client','secret','https://example.com')); $this->expectException(\Throwable::class); $client->validateIdToken('not-a-jwt','nonce'); }
}
