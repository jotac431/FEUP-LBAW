<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\User;
use App\Models\Content;
use App\Models\Feedback;
use App\Models\FeedbackNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContentController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function giveFeedback(Request $request, int $id)
    {
        $content = Content::find($id);
        if (is_null($content)) 
            return response()->json([
                'status' => 'Not Found',
                'msg' => 'Content not found, id: '.$id,
                'errors' => ['content' => 'content not found, id: '.$id]
            ], 404);

        $this->authorize('updateFeedback', $content);

        $feedback = Feedback::where('user_id', '=', Auth::id())->where('content_id', '=', $id)->first();

        if(!is_null($feedback)) {
            $deleted = $feedback->delete();

            if (!$deleted)
                return response()->json([
                    'status' => 'Internal Error',
                    'msg' => 'Could not rmeove feedback from content: '.$id,
                    'errors' => ['error' => 'Could not remove feedback from content: '.$id]
                ], 500);
        } 

        $feedback = new Feedback;
        $feedback->user_id = Auth::id();
        $feedback->content_id = $id;
        $feedback->is_like = $request->is_like;

        $feedback->save();

        $updatedContent = Content::find($id);

        $isArticle = Article::find($id) ? true : false;

        if ($feedback->is_like)
            FeedbackNotification::notify(
                Auth::user(),
                $content,
                $isArticle
            );

        return response()->json([
            'status' => 'OK',
            'msg' => 'Successfully make feedback on content: '.$id,
            'likes' => $updatedContent->likes,
            'dislikes' => $updatedContent->dislikes,
            'is_like' => $feedback->is_like
        ], 200);
    }

    /**
     * Removes the Feedback of a content.
     *
     * @param  \App\Models\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function removeFeedback(Request $request, int $id)
    {   
        if (Auth::guest())
            return redirect('/login');

        $content = Content::find($id);
        if (is_null($content)) 
            return response()->json([
                'status' => 'Not Found',
                'msg' => 'Content not found, id: '.$id,
                'errors' => ['content' => 'content not found, id: '.$id]
            ], 404);

        $this->authorize('updateFeedback', $content);

        // buscar o feedback e remove-lo da base de dados
        $feedback = Feedback::where('user_id', '=', Auth::id())->where('content_id', '=', $id)->first();

        if(!is_null($feedback)) {
            $deleted = $feedback->delete();

            if (!$deleted)
                return response()->json([
                    'status' => 'Internal Error',
                    'msg' => 'Could not remove feedback from content: '.$id,
                    'errors' => ['error' => 'Could not remove feedback from content: '.$id]
                ], 500);
            else {
                $content = Content::find($id);
            }
        }

        return response()->json([
            'status' => 'OK',
            'msg' => 'Feedback was already deleted from content: '.$id,
            'likes' => $content->likes,
            'dislikes' => $content->dislikes,
            'is_like' => $feedback->is_like
        ], 200);
    }
}
