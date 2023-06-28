<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Country;
use App\Models\Report;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private const USER_ARTICLES_LIMIT = 5;

    /**
     * Display the User profile.
     *
     * @param  int $id Id of the user
     * @return View
     */
    public function show(int $id)
    {
        $user = User::find($id);
        if (is_null($user))
            return abort(404, 'User not found, id: ' . $id);

        $userInfo = [
            'id' => $id,
            'name' => $user->name,
            'email' => $user->email,
            'birthDate' => $user->birth_date,
            'isAdmin' => $user->is_admin,
            'description' => $user->description,
            'avatar' => $user->avatar,
            'country' => $user->country->getInfo(),
            'city' => $user->city,
            'isSuspended' => $user->is_suspended,
            'reputation' => $user->reputation,
        ];

        $follows = false;
        $isOwner = false;
        if (Auth::check()) {
            $follows = Auth::user()->isFollowing($id);
            $isOwner = Auth::id() == $userInfo['id'];
        }


        $areasExpertise = $user->topAreasExpertise();

        $followerCount = count($user->followers);

        $articles = $user->articles()->map(fn ($article) => $article
            ->only('id', 'title', 'thumbnail', 'body', 'published_at', 'likes', 'dislikes'))
            ->sortByDesc('published_at');

        $canLoadMore = count($articles) > $this::USER_ARTICLES_LIMIT;

        return view('pages.user.profile', [
            'user' => $userInfo,
            'follows' => $follows,
            'topAreasExpertise' => $areasExpertise,
            'followerCount' => $followerCount,
            'articles' => $articles->take($this::USER_ARTICLES_LIMIT),
            'canLoadMore' => $canLoadMore,
            'birthDate' => date('F j, Y', strtotime($userInfo['birthDate'])),
            'age' => date_diff(date_create($userInfo['birthDate']), date_create(date('d-m-Y')))->format('%y'),
            'isOwner' => $isOwner,
        ]);
    }

    /**
     * Show the form for editing the user profile.
     *
     * @param  int $id
     * @return View
     */
    public function edit(int $id)
    {
        $user = User::find($id);
        if (is_null($user))
            return abort(404, 'User not found, id: ' . $id);

        $this->authorize('update', $user);

        $userInfo = [
            'id' => $id,
            'name' => $user->name,
            'email' => $user->email,
            'birthDate' => $user->birth_date,
            'isAdmin' => $user->is_admin,
            'description' => $user->description,
            'avatar' => $user->avatar,
            'country' => $user->country,
            'city' => $user->city,
            'isSuspended' => $user->is_suspended,
            'reputation' => $user->reputation,
        ];

        $areasExpertise = $user->topAreasExpertise();

        $followerCount = count($user->followers);

        $countries = Country::get();

        $favoriteTags = $user->favoriteTags->map(fn ($tag) => $tag->only('id'));

        $tags = Tag::listTagsByState(TagController::tagStates['accepted'])
            ->map(fn ($tag) => $tag->only('id', 'name'));

        return view('pages.user.editProfile', [
            'user' => $userInfo,
            'topAreasExpertise' => $areasExpertise,
            'followerCount' => $followerCount,
            'birthDate' => date('d-m-Y', strtotime($userInfo['birthDate'])),
            'countries' => $countries,
            'tags' => $tags,
            'favoriteTags' => $favoriteTags,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $user = User::find($id);
        if (is_null($user))
            return redirect()->back()->withErrors(['user' => 'User not found, id: ' . $id]);

        $this->authorize('update', $user);

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:authenticated_user',
            'password' => 'required_with:new_password,email|string|password',
            'new_password' => 'nullable|string|min:6|confirmed',
            // Minimum: 12 years old
            'birthDate' => 'nullable|string|date_format:Y-m-d|before_or_equal:'.date('Y-m-d', strtotime('-12 years')),
            'country' => 'nullable|string|exists:country,name',
            'avatar' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:4096', // max 5MB
            'description' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'favoriteTags' => 'nullable|array',
            'favoriteTags.*' => [
                'integer',
                Rule::exists('tag', 'id')->where('state', 'ACCEPTED')
            ],
        ], ['before_or_equal' => 'You must be at least 12 years old']);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->messages() as $key => $value) {
                if (str_contains($key, 'favoriteTags'))
                    $key = 'favoriteTags';
                $errors[$key] = is_array($value) ? implode(',', $value) : $value;
            }

            // Go back to form and refill it
            return redirect()->back()->withInput()->withErrors($errors);
        }

        if (isset($request->name)) $user->name = $request->name;
        if (isset($request->email)) $user->email = $request->email;
        if (isset($request->new_password)) $user->password = bcrypt($request->new_password);
        if (isset($request->birthDate)) $user->birth_date = $request->birthDate;
        if (isset($request->country)) $user->country_id = Country::getIdByName($request->country);
        if (isset($request->description)) $user->description = $request->description;
        if (isset($request->city)) $user->city = $request->city;

        if (isset($request->avatar)) {
            $newAvatar = $request->avatar;
            $oldAvatar = $user->avatar;

            $imgName = round(microtime(true)*1000) . '.' . $newAvatar->extension();
            $newAvatar->storeAs('public/avatars', $imgName);
            $user->avatar = $imgName;

            if (!is_null($oldAvatar))
                Storage::delete('public/thumbnails/' . $oldAvatar);
        }

        $user->save();
        $user->favoriteTags()->sync($request->favoriteTags);

        return redirect("/user/${id}");
    }

    /**
     * Deletes a user account.
     *
     * @param  Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, int $id): RedirectResponse
    {
        $user = User::find($id);
        if (is_null($user))
            return redirect()->back()->withErrors(['user' => 'User not found, id: ' . $id]);

        $this->authorize('delete', $user);

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|password'
        ]);

        if ($validator->fails())
            return redirect()->back()->withErrors($validator->errors());

        $deleted = $user->delete();
        if ($deleted)
            return redirect('/');
        else
            return redirect()->back()->withErrors(['user' => 'Failed to delete user account. Try again later']);
    }

    /**
     * Reports a user account.
     * 
     * @param  Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function report(Request $request, int $id)
    {
        $user = User::find($id);
        if (is_null($user))
            return response()->json([
                'status' => 'Not Found',
                'msg' => 'User not found, id: ' . $id,
                'errors' => ['user' => 'User not found, id: ' . $id]
            ], 404);

        $this->authorize('report', $user);

        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|min:5|max:200',
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'Bad Request',
                'msg' => 'Reason must have a number of characters between 5 and 200.',
                'errors' => $validator->errors(),
            ], 400);

        $report = new Report();
        $report->reason = $request->reason;
        $report->reported_id = $id;
        $report->reporter_id = Auth::id();
        $report->reported_at = gmdate('Y-m-d H:i:s');
        $report->save();

        return response()->json([
            'status' => 'OK',
            'msg' => 'Successful user report',
            'id' => $report->id
        ], 200);
    }

    /**
     * Information on the user's suspension
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function suspension(int $id)
    {
        $user = User::find($id);
        if (is_null($user))
            return response()->json([
                'status' => 'Not Found',
                'msg' => 'User not found, id: ' . $id,
                'errors' => ['user' => 'User not found, id: ' . $id]
            ], 404);

        $this->authorize('suspension', $user);

        if (!$user->is_suspended)
            return response()->json([
                'status' => 'Conflict',
                'msg' => 'User is not suspended, id: ' . $id,
                'errors' => ['user' => 'User is not suspended, id: ' . $id]
            ], 409);

        return $user->suspensionEndInfo();
    }

    /**
     * Display the given user's followed users
     * 
     * @param int $id
     * @return View
     */
    public function followed(int $id)
    {
        $user = User::find($id);
        if (is_null($user))
            return abort(404, 'User not found, id: ' . $id);

        $this->authorize('followed', $user);

        $followedUsers = $user->following->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar,
                'description' => $user->description,
                'country' => $user->country->getInfo(),
                'isAdmin' => $user->is_admin,
                'topAreasExpertise' => $user->topAreasExpertise(),
                'city' => $user->city,
                'reputation' => $user->reputation,
                'isSuspended' => $user->is_suspended,
                'followed' => true,
            ];
        });

        return view('pages.user.followedUsers', [
            'users' => $followedUsers,
        ]);
    }

    /**
     * Return html with the user's written articles
     * 
     * @param  Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response|View
     */
    public function articles(Request $request, int $id)
    {
        $user = User::find($id);
        if (is_null($user))
            return response()->json([
                'status' => 'Not Found',
                'msg' => 'User not found, id: ' . $id,
                'errors' => ['user' => 'User not found, id: ' . $id]
            ], 404);

        $validator = Validator::make($request->all(), [
            'offset' => 'nullable|integer|min:0',
            'limit' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'Bad Request',
                'msg' => 'Failed to fetch user\'s articles. Bad request',
                'errors' => $validator->errors(),
            ], 400);

        if (!isset($request->offset)) $request->offset = 0;

        $userArticles = $user->articles()->map(fn ($article) => $article
            ->only('id', 'title', 'thumbnail', 'body', 'published_at', 'likes', 'dislikes'))
            ->sortByDesc('published_at')->skip($request->offset);

        $canLoadMore = isset($request->limit) ? count($userArticles) > $request->limit : false;
        $articles = $userArticles->take($request->limit);

        return response()->json([
            'html' => view('partials.content.articles', ['articles' => $articles])->render(),
            'canLoadMore' => $canLoadMore
        ], 200);
    }

    public function follow(int $id)
    {
        $userToFollow = User::find($id);
        if (is_null($userToFollow))
            return response()->json([
                'status' => 'Not Found',
                'msg' => 'User not found, id: ' . $id,
                'errors' => ['user' => 'User not found, id: ' . $id]
            ], 404);

        $this->authorize('follow', $userToFollow);

        if (Auth::user()->isFollowing($id))
            return response()->json([
                'status' => 'OK',
                'msg' => 'User already followed',
                'id' => $id,
            ], 200);

        $userToFollow->followers()->attach(Auth::id());

        return response()->json([
            'status' => 'OK',
            'msg' => 'Successful user follow',
            'id' => $id,
        ], 200);
    }

    public function unfollow(int $id)
    {
        $userToUnfollow = User::find($id);
        if (is_null($userToUnfollow))
            return response()->json([
                'status' => 'Not Found',
                'msg' => 'User not found, id: ' . $id,
                'errors' => ['user' => 'User not found, id: ' . $id]
            ], 404);

        $this->authorize('unfollow', $userToUnfollow);

        if (!Auth::user()->isFollowing($id))
            return response()->json([
                'status' => 'OK',
                'msg' => 'User already not followed',
                'id' => $id,
            ], 200);

        $userToUnfollow->followers()->detach(Auth::id());

        return response()->json([
            'status' => 'OK',
            'msg' => 'Successful user unfollow',
            'id' => $id,
        ], 200);
    }
}
