<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class HomeController extends Controller
{
    private const ARTICLE_LIMIT = 10;

    /**
     * Displays the home page
     * 
     * @return View
     */
    public function show()
    {
        $type = Auth::check() ? 'recommended' : 'trending';
        $results = $this->filterByType($type, 0, $this::ARTICLE_LIMIT);

        $tags = Tag::listTagsByState('ACCEPTED')->map(fn ($tag) => $tag->only('id', 'name'));

        return view('pages.home', [
            'articles' => $results['articles'],
            'canLoadMore' => $results['canLoadMore'],
            'tags' => $tags,
        ]);
    }

    /**
     * Return a partial with the filtered articles
     * 
     * @param  Illuminate\Http\Request  $request
     * @return Response
     */
    public function filter(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'type' => ['nullable', 'string', Rule::in(['trending', 'recent', 'recommended'])],
            'tags' => 'nullable|array',
            'tags.*' => [
                'integer',
                Rule::exists('tag', 'id')->where('state', 'ACCEPTED')
            ],
            'minDate' => 'nullable|string|date_format:Y-m-d|before_or_equal:'.date('Y-m-d'),
            'maxDate' => 'nullable|string|date_format:Y-m-d|before_or_equal:'.date('Y-m-d'),
            'offset' => 'nullable|integer|min:0',
            'limit' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->messages() as $key => $value) {
                if (str_contains($key, 'tags'))
                    $errors['tags'] = "The selected tags are invalid";
                else
                    $errors[$key] = is_array($value) ? implode(',', $value) : $value;
            }

            return response()->json([
                'status' => 'Bad Request',
                'msg' => 'Failed to filter articles. Bad request',
                'errors' => $errors,
            ], 400);
        }

        if (isset($request->minDate)) $minTimestamp = strtotime($request->minDate);
        if (isset($request->maxDate)) // Allow articles posted in the same day
        $maxTimestamp = strtotime($request->maxDate . '+1 day');

        if (isset($request->minDate) && isset($request->maxDate) && $maxTimestamp < $minTimestamp)
            return response()->json([
                'status' => 'Bad Request',
                'msg' => 'Failed to filter articles. Bad request',
                'errors' => ['maxDate' => 'Max date cannot be after Min date'],
            ], 400);

        $articles = Article::lazy();

        if (isset($request->tags)) {
            $articles = $articles->filter(function ($article) use ($request) {
                $tags = $article->articleTags;
                foreach ($tags as $tag) {
                    if (in_array($tag->id, $request->tags)) return true;
                }
                return false;
            });
        }

        if (isset($request->minDate))
            $articles = $articles->filter(function ($article) use ($minTimestamp) {
                $timestamp = strtotime($article->published_at);
                return $timestamp >= $minTimestamp;
            });

        if (isset($request->maxDate))
            $articles = $articles->filter(function ($article) use ($maxTimestamp) {
                $timestamp = strtotime($article->published_at);
                return $timestamp <= $maxTimestamp;
            });

        $results = $this->filterByType($request->type, $request->offset, $request->limit, $articles);

        return response()->json([
            'html' => view('partials.content.articles', [
                'articles' => $results['articles']
            ])->render(),
            'canLoadMore' => $results['canLoadMore']
        ], 200);
    }

    private function filterByType($type = 'trending', $offset = 0, $limit = null, $articles = null)
    {
        if (is_null($articles))
            $articles = Article::lazy();

        // Sort by top posts of the day
        if ($type === 'trending' || ($type === 'recommended' && Auth::guest()))
        {
            $sortedArticles = $articles->sortBy([
                fn ($a, $b) => $this->compareArticleDates($a, $b),
                fn ($a, $b) => $this->compareArticleFeedback($a, $b)
            ]);
        }

        else if ($type === 'recommended')
        {
            $sortedArticles = $articles->sortBy([
                function ($a, $b) {
                    if (is_null($a->author)) return 1;
                    if (is_null($b->author)) return -1;

                    $isAFollowed = Auth::user()->followers->pluck('id')->contains($a->author->id);
                    $isBFollowed = Auth::user()->followers->pluck('id')->contains($b->author->id);

                    return $isBFollowed <=> $isAFollowed;
                },
                function ($a, $b) {
                    $favTagsA = $a->articleTags->filter(
                        fn ($tag) => Auth::user()->favoriteTags->pluck('id')->contains($tag->id)
                    );

                    $favTagsB = $b->articleTags->filter(
                        fn ($tag) => Auth::user()->favoriteTags->pluck('id')->contains($tag->id)
                    );

                    return count($favTagsB) <=> count($favTagsA);
                },
                fn ($a, $b) => $this->compareArticleDates($a, $b),
                fn ($a, $b) => $this->compareArticleFeedback($a, $b)
            ]);
        }

        else if ($type === 'recent')
            $sortedArticles = $articles->sortByDesc('published_at');

        else $sortedArticles = $articles;

        $sortedArticles = $sortedArticles->skip($offset);
        $canLoadMore = is_null($limit) ? false : $sortedArticles->count() > $limit;
        $results = $sortedArticles->take($limit)->map(fn ($article) => $article
            ->only('id', 'title', 'thumbnail', 'body', 'published_at', 'likes', 'dislikes'));

        return [
            'articles' => $results,
            'canLoadMore' => $canLoadMore,
        ];
    }

    private function compareArticleDates($a, $b)
    {
        $d1 = date_create($a->published_at);
        $d2 = date_create($b->published_at);

        $daysDiff = date_diff($d1, $d2)->format("%a");
        if ($daysDiff === "0") return 0;
        return $d2 <=> $d1;
    }

    private function compareArticleFeedback($a, $b)
    {
        $karma1 = $a->likes - $a->dislikes;
        $karma2 = $b->likes - $b->dislikes;
        return $karma2 <=> $karma1;
    }
}
