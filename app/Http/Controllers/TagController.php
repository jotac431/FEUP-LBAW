<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    const tagStates = [
        'accepted' => 'ACCEPTED',
        'rejected' => 'REJECTED',
        'pending' => 'PENDING'
    ];

    /**
     * Accept a Tag
     *
     * @return \Illuminate\Http\Response
     */
    public function accept(int $tag_id)
    {
        $this->authorize('accept', Tag::class);

        $tag = Tag::find($tag_id);
        if (is_null($tag)) return Response()->json([
            'status' => 'NOT FOUND',
            'msg' => 'There is no Tag with id' . $tag_id,
            'tag_id' => $tag_id
        ], 404);

        if ($tag->state == 'ACCEPTED')
            return Response()->json([
                'status' => 'OK',
                'msg' => 'Tag was already accepted',
                'tag_id' => $tag_id,
            ], 200);

        $tag->state = 'ACCEPTED';
        $tag->save();   

        return Response()->json([
            'status' => 'OK',
            'msg' => 'Successfully accepted tag: '.$tag['name'],
            'tag_id' => $tag_id,
            'tag_name' => $tag['name'],
        ], 200);
    }

    /**
     * Reject a Tag
     *
     * @return \Illuminate\Http\Response
     */
    public function reject(int $tag_id)
    {
        $this->authorize('reject', Tag::class);

        $tag = Tag::find($tag_id);
        if (is_null($tag)) return Response()->json([
            'status' => 'NOT FOUND',
            'msg' => 'There is no Tag with id' . $tag_id,
            'tag_id' => $tag_id
        ], 404);

        if ($tag->state == 'REJECTED')
            return Response()->json([
                'status' => 'OK',
                'msg' => 'Tag was already rejected',
                'tag_id' => $tag_id
            ], 200);

        $tag->state = 'REJECTED';
        $tag->save();

        return Response()->json([
            'status' => 'OK',
            'msg' => 'Successfully rejected tag: '.$tag['name'],
            'tag_id' => $tag_id,
            'tag_name' => $tag['name'],
        ], 200);
    }

    /**
     * Add a tag to the currently authenticated user's favorite tags
     * @return \Illuminate\Http\Response 
     */
    public function addUserFavorite($tag_id)
    {
        $this->authorize('addFavorite', Tag::class);

        $tag = Tag::find($tag_id);
        if (is_null($tag)) return Response()->json([
            'status' => 'NOT FOUND',
            'msg' => 'There is no Tag with id' . $tag_id,
            'tag_id' => $tag_id
        ], 404);

        $user = Auth::user();

        if ($tag->isFavorite($user->id))
            return Response()->json([
                'status' => 'OK',
                'msg' => 'Tag already added to favorites, id: ' . $tag_id,
            ], 200);



        $tag->favoriteUsers()->attach($user->id);

        return Response()->json([
            'status' => 'OK',
            'msg' => 'Successfully added tag to user favorites',
            'tag_id' => $tag_id,
        ], 200);
    }

    /**
     * Remove a tag from the currently authenticated user's favorite tags
     * @return \Illuminate\Http\Response 
     */
    public function removeUserFavorite($tag_id)
    {
        $this->authorize('removeFavorite', Tag::class);

        $tag = Tag::find($tag_id);
        if (is_null($tag)) return Response()->json([
            'status' => 'NOT FOUND',
            'msg' => 'There is no Tag with id' . $tag_id,
            'tag_id' => $tag_id
        ], 404);

        $user = Auth::user();

        if (!$tag->isFavorite($user->id))
            return Response()->json([
                'status' => 'OK',
                'msg' => 'Tag was not a favorite, id: ' . $tag_id,
            ], 200);



        $tag->favoriteUsers()->detach($user->id);

        return Response()->json([
            'status' => 'OK',
            'msg' => 'Successfully removed tag from user favorites',
            'tag_id' => $tag_id,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $this->authorize('destroy', Tag::class);

        $tag = Tag::find($id);

        if (is_null($tag))
            return Response()->json([
                'status' => 'NOT FOUND',
                'msg' => 'There is no Tag with id' . $id,
                'tag_id' => $id
            ], 404);

        $deleted = $tag->delete();
        if (!$deleted)
            return Response()->json([
                'status' => 'Internal Server Error',
                'msg' => 'There was an error deleting the tag with id ' . $id,
            ], 500);

        return Response()->json([
            'status' => 'OK',
            'msg' => 'Successfully removed tag: '.$tag['name'],
            'tag_id' => $id,
            'tag_name' => $tag['name'],
        ], 200);
    }

    /**
     * Propose a new Tag.
     *
     * @return \Illuminate\Http\Response
     */
    public function propose(Request $request)
    {
        $this->authorize('propose', Tag::class);

        $validator = Validator::make($request->all(), [
            'tagName' => 'required|string|min:2|unique:tag,name'
        ]);

        if ($validator->fails()) {
            return Response()->json([
                'status' => 'Bad Request',
                'msg' => 'Failed to propose a new tag. Bad Request',
                'errors' => $validator->errors(),
            ], 400);
        }

        $tag = new Tag;
        $tag->name = $request->tagName;
        $tag->user_id = Auth::id();
        $tag->save();

        return Response()->json([
            'status' => 'OK',
            'msg' => 'Successfully proposed tag',
            'tagName' => $request->tagName,
        ], 200);
    }
}
