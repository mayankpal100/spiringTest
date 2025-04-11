<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateQrCode;
use App\Models\User;
use Illuminate\Http\Request;

/**
 *
 */
class UserController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = User::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('sortBy')) {
            $query->orderBy($request->sortBy??"points", $request->get('sortOrder', 'asc'));
        }

        return response()->json($query->get());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string',
            'age' => 'required|integer',
            'email' => 'required|email',
            'address' => 'required|string'
        ]);
        $data['password'] = "password";
        $user = User::query()->create($data);
        GenerateQrCode::dispatch($user);

        return response()->json($user, 201);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): \Illuminate\Http\Response
    {
        User::query()->findOrFail($id)->delete();
        return response()->noContent();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function increment($id): \Illuminate\Http\JsonResponse
    {
        $user = User::query()->findOrFail($id);
        $user->increment('points');
        return response()->json($user);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function decrement($id): \Illuminate\Http\JsonResponse
    {
        $user = User::query()->findOrFail($id);
        $user->decrement('points');
        return response()->json($user);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id): \Illuminate\Http\JsonResponse
    {
        return response()->json(User::query()->findOrFail($id));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function groupedByScore(): \Illuminate\Http\JsonResponse
    {
        $grouped = User::all()->groupBy('points')->map(function ($group) {
            return [
                'names' => $group->pluck('name'),
                'average_age' => $group->avg('age')
            ];
        });

        return response()->json($grouped);
    }
}
