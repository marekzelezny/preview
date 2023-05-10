<?php

namespace App\Controllers\Api;

use App\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Kodjunkie\OnesignalPhpSdk\Exceptions\OneSignalException;
use Kodjunkie\OnesignalPhpSdk\OneSignal;
use Timber\Timber;

class ArticleSendNotification extends Controller
{
    public function __invoke(Request $request, OneSignal $oneSignal): JsonResponse
    {
        $article = Timber::get_post($request->id);

        if (! $article) {
            return response()->json([
                'success' => false,
                'message' => 'Článek nebyl nalezen.',
            ]);
        }

        try {
            $notification = $oneSignal->notification()->create([
                'headings' => [
                    'en' => $article->title(),
                    'cs' => $article->title(),
                ],
                'contents' => [
                    'en' => $article->excerpt(['chars' => 60]),
                    'cs' => $article->excerpt(['chars' => 60]),
                ],
                'big_picture' => $article->thumbnail()->src('large'),
                'chrome_web_image' => $article->thumbnail()->src('large'),
                'chrome_big_picture' => $article->thumbnail()->src('large'),
                'url' => route('article.index', $article->slug),
                'included_segments' => [
                    'Redakce',
                    'Engaged Users',
                ],
            ]);
        } catch (OneSignalException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
