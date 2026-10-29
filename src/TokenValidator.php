<?php
declare(strict_types=1);
namespace App;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;

final class TokenValidator {
  /** @param array<string, mixed> $jwks
   * @return array<string, mixed>
   */
  public static function validate(string $jwt, array $jwks, Config $config, string $nonce): array {
    $claims = (array) JWT::decode($jwt, JWK::parseKeySet($jwks));
    if (!isset($claims['exp']) || !is_int($claims['exp']) || $claims['exp'] <= time()) throw new \RuntimeException('Missing or expired token');
    if (($claims['iss'] ?? '') !== $config->issuer()) throw new \RuntimeException('Invalid issuer');
    $aud = $claims['aud'] ?? '';
    if (!(is_string($aud) ? hash_equals($config->clientId, $aud) : in_array($config->clientId, $aud, true))) throw new \RuntimeException('Invalid audience');
    if (!hash_equals($nonce, (string)($claims['nonce'] ?? ''))) throw new \RuntimeException('Invalid nonce');
    return $claims;
  }
}
