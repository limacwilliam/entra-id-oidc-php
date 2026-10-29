<?php
declare(strict_types=1);
namespace App;

final class OidcClient {
  public function __construct(private Config $config) {}
  public function begin(): string {
    $state = bin2hex(random_bytes(32)); $nonce = bin2hex(random_bytes(32)); $verifier = rtrim(strtr(base64_encode(random_bytes(64)), '+/', '-_'), '=');
    $_SESSION['oidc'] = compact('state', 'nonce', 'verifier');
    $challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
    return $this->config->authorizeUrl().'?'.http_build_query(['client_id'=>$this->config->clientId,'response_type'=>'code','redirect_uri'=>$this->config->appUrl.'/callback.php','response_mode'=>'query','scope'=>'openid profile email','state'=>$state,'nonce'=>$nonce,'code_challenge'=>$challenge,'code_challenge_method'=>'S256']);
  }
  /** @return array<string, mixed> */
  public function exchangeAndValidate(string $code, string $state): array {
    $flow = $_SESSION['oidc'] ?? null;
    if (!$flow || !hash_equals($flow['state'], $state)) throw new \RuntimeException('Invalid state');
    $response = $this->post($this->config->tokenUrl(), ['client_id'=>$this->config->clientId,'client_secret'=>$this->config->clientSecret,'code'=>$code,'redirect_uri'=>$this->config->appUrl.'/callback.php','grant_type'=>'authorization_code','code_verifier'=>$flow['verifier']]);
    $claims = $this->validateIdToken($response['id_token'], $flow['nonce']); unset($_SESSION['oidc']); return $claims;
  }
  /** @return array<string, mixed> */
  public function validateIdToken(string $jwt, string $nonce): array {
    $jwks = json_decode($this->get($this->config->jwksUrl()), true, 512, JSON_THROW_ON_ERROR);
    return TokenValidator::validate($jwt, $jwks, $this->config, $nonce);
  }
  /** @param array<string, string> $data
   * @return array<string, string>
   */
  private function post(string $url, array $data): array { $ctx=stream_context_create(['http'=>['method'=>'POST','header'=>'Content-Type: application/x-www-form-urlencoded','content'=>http_build_query($data),'ignore_errors'=>true,'timeout'=>10]]); $body=file_get_contents($url,false,$ctx); if($body===false) throw new \RuntimeException('Token request failed'); $result=json_decode($body,true); if(!isset($result['id_token'])) throw new \RuntimeException('Provider returned no ID token'); return $result; }
  private function get(string $url): string { $body=file_get_contents($url,false,stream_context_create(['http'=>['timeout'=>10]])); if($body===false) throw new \RuntimeException('JWKS request failed'); return $body; }
}
