<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Suspension;
use App\Models\Report;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Display the Administration Panel
     *
     * @return View
     */
    public function show()
    {
        $this->authorize('show', Admin::class);

        $admin = Admin::find(Auth::id());
        if (is_null($admin))
            return abort(404, 'User not found');

        $adminInfo = [
            'id' => $admin->id,
            'name' => $admin->name,
            'avatar' => $admin->avatar,
            'country' => $admin->country,
            'city' => $admin->city,
        ];

        return view('pages.admin.admin_panel',[
            'admin' => $adminInfo,
        ]);
    }

    /**
     * Display the list of suspended users
     *
     * @return View
     */
    public function suspensions()
    {
        $this->authorize('suspensions', Admin::class);

        $suspendedUserList = User::where('is_suspended', true)->get();

        $suspendedUsers = $suspendedUserList->map(function ($user) {
            $history = $user->suspensions->map(function ($suspension) {

                $admin = Admin::find($suspension->admin_id);
                if (isset($admin)) 
                    $adminInfo = [
                        'id' => $suspension->admin_id,
                        'name' => $admin->name,
                    ];
                else $adminInfo = null;

                return [
                    'reason' => $suspension->reason,
                    'sort_date' => gmdate('d-m-Y', strtotime($suspension->start_time)),
                    'start_date' => date('F j, Y', strtotime($suspension->start_time)),
                    'end_date' => date('F j, Y', strtotime($suspension->end_time)),
                    'admin' => $adminInfo,
                ];
            })->sortByDesc('sort_date');

            return [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar,
                'country' => $user->country,
                'is_admin' => $user->is_admin,
                'history' => $history,
                // The admin could select the user and see his history or see all suspensions
            ];
        });

        $suspensionHistory = DB::table('suspension')->get()->map(function ($suspension) {


            $admin = Admin::find($suspension->admin_id);
            if (isset($admin)) 
                $adminInfo = [
                    'id' => $suspension->admin_id,
                    'name' => $admin->name,
                ];
            else $adminInfo = null;

            $user = User::find($suspension->user_id)
                ->only('id', 'name', 'avatar', 'country', 'is_admin');

            return [
                'reason' => $suspension->reason,
                'sort_date' => gmdate('d-m-Y', strtotime($suspension->start_time)),
                'start_date' => date('F j, Y', strtotime($suspension->start_time)),
                'end_date' => date('F j, Y', strtotime($suspension->end_time)),
                'admin' => $adminInfo,
                'user' => $user,
            ];
        })->sortByDesc('sort_date');

        return view('pages.admin.suspensions', [
            'suspendedUsers' => $suspendedUsers,
            'suspensionHistory' => $suspensionHistory,
        ]);
    }

    /**
     * Suspends a user
     * 
     * @param  Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function suspendUser(Request $request, int $id)
    {  

        $user = User::find($id);
        if (is_null($user))
            return response()->json([
                'status' => 'Not Found',
                'msg' => 'User not found, id: '.$id,
                'errors' => ['user' => 'User not found, id: '.$id]
            ], 404);

        $this->authorize('suspendUser', $user);

        $validator = Validator::make($request->all(),[
            'reason' => 'required|string|min:5|max:200',
            'end_time' => 'required|string|date_format:Y-m-d',
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'Bad Request',
                'msg' => 'Failed to suspend user. Bad request',
                'errors' => $validator->errors(),
            ], 400);

        $start_timestamp = time();
        $end_timestamp = strtotime($request->end_time);

        if ($start_timestamp >= $end_timestamp)
            return response()->json([
                'status' => 'Bad Request',
                'msg' => 'Failed to suspend user. Bad request',
                'errors' => ['end_time' => 'End time must be after now'],
            ], 400);

        $suspension = new Suspension();
        $suspension->reason = $request->reason;
        $suspension->start_time = gmdate('Y-m-d H:i:s', $start_timestamp);
        $suspension->end_time = gmdate('Y-m-d H:i:s', $end_timestamp);
        $suspension->user_id = $id;
        $suspension->admin_id = Auth::id();

        $user->is_suspended = true;

        $suspension->save();
        $user->save();

        return response()->json([
            'status' => 'OK',
            'msg' => 'Successfully suspensed user '.$user->name,
            'id' => $suspension->id,
        ], 200);
    }

    /**
     * Suspends a user
     * 
     * @param  Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function unsuspendUser(int $id)
    {
        $user = User::find($id);
        if (is_null($user))
            return response()->json([
                'status' => 'Not Found',
                'msg' => 'User not found, id: '.$id,
                'errors' => ['user' => 'User not found, id: '.$id]
            ], 404);

        $this->authorize('suspendUser', $user);

        if (!$user->is_suspended)
            return response()->json([
                'status' => 'OK',
                'msg' => 'User '.$user->name.' was alreay unsuspended',
            ], 200);

        Suspension::where('user_id', $id)
            ->where('end_time', '>', gmdate('Y-m-d H:i:s'))
            ->update(['end_time' => gmdate('Y-m-d H:i:s')]);

        $user->is_suspended = false;
        $user->save();

        return response()->json([
            'status' => 'OK',
            'msg' => 'Successfully unsuspended user'
        ], 200);
    }

    /**
     * Page with information about all the reports
     * 
     * @return View
     */
    public function reports()
    {
        $this->authorize('reports', Admin::class);

        $reportsInfo = Report::orderByDesc('reported_at')->get()
            ->map(function ($report) {

                $reportedInfo = $report->reported
                    ->only('id', 'name', 'avatar', 'country', 'is_admin', 'is_suspended');

                if (isset($report->reporter))
                    $reporterInfo = $report->reporter->only('id', 'name');

                else $reporterInfo = null;

                return [
                    'id' => $report->id,
                    'reason' => $report->reason,
                    'reported_at' => date('F j, Y', strtotime($report->reported_at)),
                    'is_closed' => $report->is_closed,
                    'reported' => $reportedInfo,
                    'reporter' => $reporterInfo,
                ];
            });

        return view('pages.admin.reports', [
            'reports' => $reportsInfo,
        ]);
    }

    /**
     * Page where the admin can accept or refuse tags
     * 
     * @return View
     */
    public function tags()
    {
        $this->authorize('tags', Admin::class);

        $tags_pending = $this->getTagsByState('PENDING');

        $tags_accepted = $this->getTagsByState('ACCEPTED');

        $tags_rejected = $this->getTagsByState('REJECTED');

        return view('pages.admin.manageTags', [
            'tags_pending' => $tags_pending,
            'tags_accepted' => $tags_accepted,
            'tags_rejected' => $tags_rejected,
        ]);
    }

    private function getTagsByState(string $state) {
        $tags = Tag::listTagsByState($state)->map(function ($tag) {
            if (isset($tag->user))
                $userInfo = $tag->user->only('id', 'name', 'avatar');

            else $userInfo = null;

            return [
                'id' => $tag->id,
                'name' => $tag->name,
                'proposed_at' => $tag->proposed_at,
                'state' => $tag->state,
                'user' => $userInfo
            ];
        });

        return $tags;
    }

    public function closeReport(int $id)
    {
        $report = Report::find($id);
        if (is_null($report))
            return response()->json([
                'status' => 'Not Found',
                'msg' => 'Report not found, id: '.$id,
                'errors' => ['report' => 'Report not found, id: '.$id]
            ], 404);

        $this->authorize('closeReport', $report);

        if ($report->is_closed)
            return response()->json([
                'status' => 'OK',
                'msg' => 'Report was already closed',
            ], 200);

        $report->is_closed = true;
        $report->save();

        return response()->json([
            'status' => 'OK',
            'msg' => 'Report was successfully closed',
        ], 200);
    }
}
