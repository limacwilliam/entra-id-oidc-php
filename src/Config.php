<?php
declare(strict_types=1);
namespace App;

final readonly class Config {
  public function __construct(public string $tenantId, public string $clientId, public string $clientSecret, public string $appUrl) {}
  public static function fromEnv(): self {
    $get = static function (string $key): string { $value = getenv($key); if ($value === false || $value === '') throw new \RuntimeException("Missing $key"); return $value; };
    return new self($get('OIDC_TENANT_ID'), $get('OIDC_CLIENT_ID'), $get('OIDC_CLIENT_SECRET'), rtrim($get('APP_URL'), '/'));
  }
  public function issuer(): string { return "https://login.microsoftonline.com/{$this->tenantId}/v2.0"; }
  public function authorizeUrl(): string { return "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/authorize"; }
  public function tokenUrl(): string { return "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token"; }
  public function jwksUrl(): string { return "https://login.microsoftonline.com/{$this->tenantId}/discovery/v2.0/keys"; }
}
