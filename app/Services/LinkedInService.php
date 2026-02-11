<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class LinkedInService
{
    protected Client $client;
    protected string $baseUrl = 'https://api.linkedin.com/v2';
    protected string $oidcUserInfoUrl = 'https://api.linkedin.com/v2/userinfo';

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Restli-Protocol-Version' => '2.0.0',
            ],
        ]);
    }

    public function getUserProfile(string $accessToken): array
    {
        try {
            $response = $this->client->get($this->oidcUserInfoUrl, [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'id' => $data['sub'] ?? null,
                'first_name' => $data['given_name'] ?? '',
                'last_name' => $data['family_name'] ?? '',
            ];
        } catch (RequestException $e) {
            Log::error('LinkedIn API - Get Profile Error: ' . $e->getMessage());
            throw new Exception('Failed to fetch LinkedIn profile: ' . $e->getMessage());
        }
    }

    public function uploadImage(string $accessToken, string $ownerUrn, string $absoluteImagePath): string
    {
        try {
            // Step 1: Register upload
            $registerResponse = $this->client->post("{$this->baseUrl}/assets?action=registerUpload", [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                ],
                'json' => [
                    'registerUploadRequest' => [
                        'recipes' => [
                            'urn:li:digitalmediaRecipe:feedshare-image'
                        ],
                        'owner' => $ownerUrn,
                        'serviceRelationships' => [
                            [
                                'relationshipType' => 'OWNER',
                                'identifier' => 'urn:li:userGeneratedContent'
                            ]
                        ]
                    ]
                ]
            ]);

            $registerData = json_decode($registerResponse->getBody()->getContents(), true);
            $uploadUrl = $registerData['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'];
            $assetUrn = $registerData['value']['asset'];

            // Step 2: Upload image
            $imageContent = file_get_contents($absoluteImagePath);
            if ($imageContent === false) {
                throw new Exception('Failed to read campaign image from disk.');
            }
            
            $this->client->put($uploadUrl, [
                'headers' => [
                    'Content-Type' => 'application/octet-stream',
                ],
                'body' => $imageContent,
            ]);

            return $assetUrn;
        } catch (RequestException $e) {
            Log::error('LinkedIn API - Upload Image Error: ' . $e->getMessage());
            throw new Exception('Failed to upload image: ' . $e->getMessage());
        } catch (Exception $e) {
            Log::error('LinkedIn API - Upload Image Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createPost(string $accessToken, string $authorUrn, string $text, ?string $imageUrn = null): array
    {
        try {
            $postData = [
                'author' => $authorUrn,
                'lifecycleState' => 'PUBLISHED',
                'specificContent' => [
                    'com.linkedin.ugc.ShareContent' => [
                        'shareCommentary' => [
                            'text' => $text
                        ],
                        'shareMediaCategory' => 'NONE'
                    ]
                ],
                'visibility' => [
                    'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC'
                ]
            ];

            if ($imageUrn) {
                $postData['specificContent']['com.linkedin.ugc.ShareContent']['shareMediaCategory'] = 'IMAGE';
                $postData['specificContent']['com.linkedin.ugc.ShareContent']['media'] = [
                    [
                        'status' => 'READY',
                        'description' => [
                            'text' => 'Campaign image'
                        ],
                        'media' => $imageUrn,
                        'title' => [
                            'text' => 'Campaign'
                        ]
                    ]
                ];
            }

            $response = $this->client->post("{$this->baseUrl}/ugcPosts", [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                ],
                'json' => $postData,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('LinkedIn API - Create Post Error: ' . $e->getMessage());
            throw new Exception('Failed to create post: ' . $e->getMessage());
        }
    }

    public function refreshToken(string $refreshToken): array
    {
        try {
            $response = $this->client->post('https://www.linkedin.com/oauth/v2/accessToken', [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                    'client_id' => config('services.linkedin.client_id'),
                    'client_secret' => config('services.linkedin.client_secret'),
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('LinkedIn API - Refresh Token Error: ' . $e->getMessage());
            throw new Exception('Failed to refresh token: ' . $e->getMessage());
        }
    }

    public function isTokenValid(string $accessToken): bool
    {
        try {
            $response = $this->client->get($this->oidcUserInfoUrl, [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                ],
            ]);

            return $response->getStatusCode() === 200;
        } catch (RequestException $e) {
            return false;
        }
    }
}
