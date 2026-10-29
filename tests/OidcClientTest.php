<?php
declare(strict_types=1);
namespace Tests;
use App\Config; use App\TokenValidator; use Firebase\JWT\JWT; use PHPUnit\Framework\TestCase;
final class OidcClientTest extends TestCase {
  public function testValidatesTokenSignedByGeneratedTestKey(): void {
    $key=openssl_pkey_new(['private_key_type'=>OPENSSL_KEYTYPE_RSA,'private_key_bits'=>2048]); openssl_pkey_export($key,$private); $detail=openssl_pkey_get_details($key);
    $b64=static fn(string $value): string => rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    $config=new Config('tenant','client','secret','https://example.com'); $now=time();
    $jwt=JWT::encode(['iss'=>$config->issuer(),'aud'=>'client','nonce'=>'nonce','iat'=>$now,'exp'=>$now+60],$private,'RS256','test-key');
    $jwks=['keys'=>[['kty'=>'RSA','kid'=>'test-key','use'=>'sig','alg'=>'RS256','n'=>$b64($detail['rsa']['n']),'e'=>$b64($detail['rsa']['e'])]]];
    self::assertSame('client',TokenValidator::validate($jwt,$jwks,$config,'nonce')['aud']);
  }
  public function testRejectsWrongNonce(): void { $this->expectException(\RuntimeException::class); TokenValidator::validate('not-a-jwt',['keys'=>[]],new Config('tenant','client','secret','https://example.com'),'nonce'); }
}
