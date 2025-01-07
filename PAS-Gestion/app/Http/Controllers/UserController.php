<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // GET: Récupérer tous les users
    public function index()
    {
        $Users = User::all();
        return response()->json($users);
    }

    // POST: Créer un nouvel user
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:50|unique:users,email',
            'motDePasse' => 'required|string|max:255',
        ]);

        $user = User::create($request->all());

        return response()->json($user, 201);
    }

    // GET: Récupérer un user spécifique
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    // PUT/PATCH: Mettre à jour un user
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->update($request->all());

        return back()->with('success', 'User updated successfully');
    }

    public function updatePassword(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }       

        /*$validated = $request->validate([
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);*/

        $user->update([
            'password' => Hash::make($request['password']),
        ]);

        return response()->json($request);
    }

    // DELETE: Supprimer un user
    public function destroy($id)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
