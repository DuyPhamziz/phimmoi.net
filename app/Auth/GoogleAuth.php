<?php

namespace App\Auth;

use League\OAuth2\Client\Provider\Google;

class GoogleAuth {
    protected $provider;

    public function __construct()
    {
        $this->provider = new Google([
            'clientId' => $_ENV['GOOGLE_CLIENT_ID'],
            'clientSecret' => $_ENV['GOOGLE_CLIENT_SECRET'],
            'redirectUri' => $_ENV['GOOGLE_REDIRECT_URI'],
        ]);

    }
    public function getAuthUrl(): string
    {
        $options = [
            'prompt' => 'select_account' // ép Google hiện lại màn hình chọn tài khoản
        ];
        return $this->provider->getAuthorizationUrl($options);
    }

    public function getAccessToken(string $code) {
        return $this->provider->getAccessToken('authorization_code', ['code' => $code]);
    }
    public function getUserInfo($token) {
        return $this->provider->getResourceOwner($token);
    }
    public function getState() {
        return $this->provider->getState();
    }
    public function extractUserInfo($token): array
    {
        $user = $this->getUserInfo($token);
        $data = $user->toArray();
        return [
            'id' => $user->getId(),
            'email' => $data['email'] ?? null,
            'name' => $data['name'] ?? null,
            'avatar' => $data['picture'] ?? null,
        ];
    }
}