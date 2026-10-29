<?php
declare(strict_types=1);
namespace App;

final readonly class Config {
  public function __construct(public string $tenantId, public string $clientId, public string $clientSecret, public string $appUrl) {}
  public static function fromEnv(): self {
    foreach (['OIDC_TENANT_ID','OIDC_CLIENT_ID','OIDC_CLIENT_SECRET','APP_URL'] as $key) if (!getenv($key)) throw new \RuntimeException("Missing $key");
    return new self(getenv('OIDC_TENANT_ID'), getenv('OIDC_CLIENT_ID'), getenv('OIDC_CLIENT_SECRET'), rtrim(getenv('APP_URL'), '/'));
  }
  public function issuer(): string { return "https://login.microsoftonline.com/{$this->tenantId}/v2.0"; }
  public function authorizeUrl(): string { return "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/authorize"; }
  public function tokenUrl(): string { return "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token"; }
  public function jwksUrl(): string { return "https://login.microsoftonline.com/{$this->tenantId}/discovery/v2.0/keys"; }
}
