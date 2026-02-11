<?php

namespace App\Http\Controllers;

use App\Services\LinkedInService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class LinkedInPostController extends Controller
{
    protected LinkedInService $linkedInService;

    public function __construct(LinkedInService $linkedInService)
    {
        $this->linkedInService = $linkedInService;
    }

    public function confirm()
    {
        if (!Auth::check() || !Auth::user()->linkedinAccount) {
            return redirect()->route('login');
        }

        $defaultCaption = "Thrilled to be part of this milestone moment with Dexcom Academy.
A new learning journey is about to begin — empowering healthcare through smarter glucose monitoring and continuous education.
Excited for what’s ahead.
#dexcomacademy #Diabetes #DexcomMEA #CGM";

        return view('confirm-post', [
            'user' => Auth::user(),
            'caption' => $defaultCaption,
            'campaignImage' => asset('images/campaign-image.png'),
        ]);
    }

    public function publish(Request $request)
    {
        $request->validate([
            'caption' => 'required|string|max:3000',
        ]);

        try {
            $user = Auth::user();
            $linkedinAccount = $user->linkedinAccount;

            if (!$linkedinAccount) {
                throw new Exception('LinkedIn account not connected');
            }

            if ($linkedinAccount->isExpired()) {
                throw new Exception('LinkedIn access token has expired. Please reconnect your account.');
            }

            $imageAbsolutePath = public_path('images/campaign-image.png');
            $dedupHash = hash('sha256', $request->caption.'|'.(file_exists($imageAbsolutePath) ? sha1_file($imageAbsolutePath) : 'no-image'));
            if ($linkedinAccount->last_post_hash === $dedupHash) {
                throw new Exception('Duplicate post detected. Please change the caption or wait before posting again.');
            }

            $imageUrn = null;

            $authorUrn = 'urn:li:person:' . $linkedinAccount->linkedin_id;
            if (file_exists($imageAbsolutePath)) {
                $imageUrn = $this->linkedInService->uploadImage($linkedinAccount->access_token, $authorUrn, $imageAbsolutePath);
            }

            $result = $this->linkedInService->createPost(
                $linkedinAccount->access_token,
                $authorUrn,
                $request->caption,
                $imageUrn
            );

            $linkedinAccount->forceFill([
                'last_post_hash' => $dedupHash,
                'last_posted_at' => now(),
            ])->save();

            Session::put('post_published', true);
            Session::put('post_id', $result['id'] ?? null);

            return redirect()->route('post.success')->with('success', 'Post published successfully!');

        } catch (Exception $e) {
            return redirect()->route('post.error')->with('error', $e->getMessage());
        }
    }

    public function success()
    {
        if (!Session::has('post_published')) {
            return redirect()->route('post.confirm');
        }

        $postId = Session::get('post_id');
        Session::forget(['post_published', 'post_id']);

        return view('success', [
            'postId' => $postId,
            'user' => Auth::user(),
        ]);
    }

    public function error()
    {
        $errorMessage = session('error', 'An unknown error occurred');
        return view('error', [
            'error' => $errorMessage,
            'user' => Auth::user(),
        ]);
    }
}
