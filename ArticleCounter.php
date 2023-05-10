<?php

namespace App\Controllers\Api;

use App\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Timber\Timber;

class ArticleCounter extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        if ($request->get('loggedIn') === 'true') {
            return response()->json([
                'success' => false,
                'message' => 'User is logged in',
            ]);
        }

        $article = Timber::get_post($request->id);

        if ($article === null) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found',
            ], 500);
        }

        if ($request->get('seznam') && $request->get('seznam') == 'true') {
            $this->updateSeznamViews($article);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function updateSeznamViews($article): void
    {
        if (ff_seznam_first_visit()) {
            $article->updateMeta('ff_seznam_newsfeed', true);
        }

        if (ff_seznam_session()) {
            $count = (int) $article->meta('ff_seznam_newsfeed_views') ?? 0;
            $article->updateMeta('ff_seznam_newsfeed_views', $count + 1);
        }
    }
}
