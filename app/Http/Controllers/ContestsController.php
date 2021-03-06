<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContestRequest;
use App\Models\Contest;
use App\Models\Problem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ContestsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['index'],
        ]);
    }

    public function index(Request $request)
    {

        $contest = Contest::getModel();
        return $contest->get();
    }

    public function show(Request $request,Contest $contest)
    {   
        return response()->json($contest);
    }

    public function create()
    {
        //$this->authorize('contest_create');
        return view('contests.create');
    }

    public function edit(Contest $contest)
    {
        //$this->authorize('contest_edit');
        return view('contests.edit', compact('contest'));
    }

    public function update()
    {
        //$this->authorize('contest_edit');
    }

    public function store(ContestRequest $request)
    {
        //$this->authorize('contest_create');
        $contest = Contest::create([
            'title' => $request->title,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'lock_board_time' => $request->lock_board_time,
            'owner' => Auth::user()->username,
            'isprivate' => $request->is_private == 2 ? true : false,
            'hide_other' => $request->hide_other == 2 ? true : false,
            'password' => $request->is_private == 2 ? bcrypt($request->password) : null,
            'description' => $request->description,
            'create_time' => now(),
        ]);
        return redirect()->route('contests.show', $contest->id);
    }


    public function add_problem(Contest $contest, Problem $problem)
    {
        $contest->problems()->attach($problem->id);
        return redirect()->route('contests.show', $contest->id);
    }

    public function add_user_by_admin(Contest $contest, User $user)
    {
        $contest->users()->attach($user->id);
        return redirect()->route('contests.show', $contest->id);
    }

    public function add_user_by_password(Contest $contest, Request $request)
    {
        $user = Auth::user();
        if (Hash::check($request->password, $contest->password)) {
            $contest->users()->attach($user->id);
        }
        return redirect()->route('contests.show', $contest->id);
    }
    public function remove_user(Contest $contest, User $user)
    {

        $contest->users()->detach($user->id);
        return redirect()->route('contests.show', $contest->id);
    }
    public function add_reject_user(Contest $contest, Request $request)
    {
        $user = User::where('username', $request->username)->first();
        if ($user == null) {
            return response()->json(['message' => 'not have this user',
            ], 200);
        }
        $contest->reject_users()->attach($user->id);
        return redirect()->route('contests.show', $contest->id);
    }
    public function remove_reject_user(Contest $contest, User $user)
    {
        $contest->reject_users()->detach($user->id);
        return redirect()->route('contests.show', $contest->id);
    }

    public function getProblems(Contest $contest,Request $request)
    {   
        $problem = $contest->problems();
        $perPage = request()->get('perPage') ?: 15;
        $page = request()->get('page') ?: 1;
        return $problem->orderByDesc('id')->paginate($perPage, ['*'], 'page', $page);
        // return $problem->get();
    }
    public function getProblem(Request $request)
    {
        $contest_id=$request->cid;
        $keychar=$request->keychar;
        $problem=\DB::table('contest_problem')->where('contest_id',$contest_id)->where('keychar',$keychar)->first();
        return $problem->problem_id;
    }
    public function getUser(Contest $contest)
    {   
        $users = $contest->users();
        
        return $users->get();
    }
    public function getRejectUser(Contest $contest)
    {   
        $reject_users = $contest->reject_users();
        
        return $reject_users->get();
    }
    public function getStatus(Contest $contest,Request $request)
    {
        $status = $contest->status();
        $perPage = $request->get('perPage') ?: 15;
        $page = $request->get('page') ?: 1;

        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $status = $status->orWhere('id', 'like', $search);
            $status = $status->orWhere('username', 'like', $search);
            $status = $status->orWhere('pid', 'like', $search);
        }

        if ($request->get('user')) {
            $status = $status->where('username', $request->get('username'));
        }

        if ($request->get('prob')) {
            $status = $status->where('pid', $request->get('prob'));
        }

        if ($request->get('res')) {
            $res = json_decode($request->get('res'));
            if (count($res)) {
                $status = $status->whereIn('result', $res);
            }
        }

        if ($request->get('lang')) {
            $lang = json_decode($request->get('lang'));
            if (count($lang)) {
                $status = $status->whereIn('lang', $lang);
            }
        }

        return $status->orderByDesc('id')->paginate($perPage, ['*'], 'page', $page);
        return $status->get();
    }
    public function getTopics(Contest $contest,Request $request)
    {
        $topics = $contest->topics();
        // if (!$request->wantsJson()) {
        //     abort(404);
        // }

        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $topics = $topics->orWhere('id', 'like', $search);
            $topics = $topics->orWhere('username', 'like', $search);
            $topics = $topics->orWhere('pid', 'like', $search);
        }

        if ($request->get('user')) {
            $topics = $topics->where('username', $request->get('username'));
        }

        if ($request->get('prob')) {
            $topics = $topics->where('pid', $request->get('prob'));
        }

        return $topics->get();
    }
}
