<?php

namespace App\Http\Controllers\Feed;

use App\Models\Feed;
use App\Models\Like;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FeedController extends Controller
{
    public function index()
    {
        $user_id = Auth::id();
        $feeds = Feed::with('user', 'likes')->latest()->get();
        foreach ($feeds as $feed) {
            $liked = $feed->likes->contains('user_id', $user_id);
            $feed->liked = $liked;
        }

        return response([
            'feeds' => $feeds,
        ], 200);
    }


    public function store(PostRequest $request)
    {
        $request->validated();
        auth()->user()->feeds()->create([
            'content' => $request->content
        ]);
        return response([
            'message' => 'success'
        ], 201);
    }

    public function likePost($feed_id)
    {
        // select feed with feed_id
        $feed = Feed::whereId($feed_id)->first();
        if (!$feed) {
            return response([
                'message' => '404 Not found'
            ], 500);

        }
        // Unlike post
        $unlike_post = Like::where('user_id', auth()->id())->where('feed_id', $feed_id)->delete();
        if ($unlike_post) {
            return response([
                'message' => 'Unliked'
            ], 200);
        }
        // Like post
        $like_post = Like::create([
            'user_id' => auth()->id(),
            'feed_id' => $feed_id
        ]);
        if ($like_post) {
            return response([
                'message' => 'liked'
            ], 200);
        }
    }

    public function comment(Request $request, $feed_id)
    {
        $request->validate([
            'body' => 'required'
        ]);
        $comment = Comment::create([
            'user_id' => auth()->id(),
            'feed_id' => $feed_id,
            'body' => $request->body
        ]);
        return response([
            'comment' => $comment
        ], 200);
    }
    public function getComments($feed_id)
    {
        $comments = Comment::with('feed')->with('user')->whereFeedId($feed_id)->latest()->get();
        return response([
            '   comments' => $comments
        ], 200);
    }

    public function search(Request $request)
    {
        // Valider la requête de recherche
        $request->validate([
            'query' => 'required|string|max:255',
        ]);

        // Récupérer la requête de recherche
        $query = $request->input('query');

        // Rechercher dans les feeds en fonction du contenu
        $feeds = Feed::with('user', 'likes')
            ->where('content', 'like', '%' . $query . '%')
            ->latest()
            ->get();

        // Ajouter une propriété liked à chaque feed pour indiquer si l'utilisateur l'a aimé
        $user_id = Auth::id();
        foreach ($feeds as $feed) {
            $liked = $feed->likes->contains('user_id', $user_id);
            $feed->liked = $liked;
        }

        // Retourner les résultats de la recherche
        return response([
            'feeds' => $feeds,
        ], 200);
    }



}
