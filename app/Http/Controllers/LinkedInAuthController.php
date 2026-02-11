<?php

namespace App\Http\Controllers;

use App\Models\LinkedInAccount;
use App\Models\User;
use App\Services\LinkedInService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class LinkedInAuthController extends Controller
{
    protected LinkedInService $linkedInService;

    public function __construct(LinkedInService $linkedInService)
    {
        $this->linkedInService = $linkedInService;
    }

    public function redirect()
    {
        return Socialite::driver('linkedin')
            ->scopes(['openid', 'profile', 'email', 'w_member_social'])
            ->redirect();
    }

    public function callback()
    {
        try {
            $linkedInUser = Socialite::driver('linkedin')->user();
            
            $profile = $this->linkedInService->getUserProfile($linkedInUser->token);

            $user = User::firstOrCreate([
                'email' => $linkedInUser->getEmail(),
            ], [
                'name' => $profile['first_name'] . ' ' . $profile['last_name'],
                'password' => bcrypt(str()->random(24)),
            ]);

            LinkedInAccount::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'linkedin_id' => $profile['id'],
                    'access_token' => $linkedInUser->token,
                    'expires_at' => now()->addSeconds($linkedInUser->expiresIn ?? 3600),
                    'first_name' => $profile['first_name'],
                    'last_name' => $profile['last_name'],
                    'email' => $linkedInUser->getEmail(),
                    'profile_picture' => null,
                ]
            );

            Auth::login($user);

            return redirect()->route('post.confirm');
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Failed to authenticate with LinkedIn: ' . $e->getMessage());
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
