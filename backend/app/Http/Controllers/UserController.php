<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Fetch all users or a single user by employeeID (query param).
     */
    public function index(Request $request)
    {
        $employeeID = $request->query('employeeID');

        if ($employeeID) {
            $user = User::where('employeeID', $employeeID)->first();

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            return response()->json($user);
        }

        return response()->json(User::all());
    }

    /**
     * Create a new user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employeeID' => 'required|string|unique:users,employeeID',
            'firstname'  => 'required|regex:/^[\pL\s\'-]+$/u|max:255',
            'lastname'   => 'required|regex:/^[\pL\s\'-]+$/u|max:255',
            'email'      => 'required|email|unique:users,email',
            'contact'    => 'nullable|regex:/^[0-9]+$/|size:11',
            'password'   => 'required|string|min:6',
            'role'       => 'nullable|string',
            'status'     => 'required|in:Active,Inactive',
        ], [
            'firstname.regex' => 'Firstname must contain only letters, spaces, hyphens, or apostrophes.',
            'lastname.regex'  => 'Lastname must contain only letters, spaces, hyphens, or apostrophes.',
            'contact.regex'   => 'Contact must contain only numbers.',
            'contact.size'    => 'Contact must be exactly 11 digits.'
        ]);

        $user = User::create([
            'employeeID' => $request->employeeID,
            'firstname'  => $request->firstname,
            'lastname'   => $request->lastname,
            'email'      => $request->email,
            'contact'    => $request->contact,
            'password'   => Hash::make($request->password),
            'role'       => $request->role ?? 'Employee',
            'status'     => $request->status,
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user'    => $user
        ], 201);
    }

    /**
     * Update user by ID.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'employeeID' => 'required|string|unique:users,employeeID,' . $id,
            'firstname'  => 'required|regex:/^[\pL\s\'-]+$/u|max:255',
            'lastname'   => 'required|regex:/^[\pL\s\'-]+$/u|max:255',
            'email'      => 'required|email|unique:users,email,' . $id,
            'contact'    => 'nullable|regex:/^[0-9]+$/|size:11',
            'password'   => 'nullable|string|min:6',
            'role'       => 'nullable|string',
            'status'     => 'required|in:Active,Inactive',
        ], [
            'firstname.regex' => 'Firstname must contain only letters, spaces, hyphens, or apostrophes.',
            'lastname.regex'  => 'Lastname must contain only letters, spaces, hyphens, or apostrophes.',
            'contact.regex'   => 'Contact must contain only numbers.',
            'contact.size'    => 'Contact must be exactly 11 digits.'
        ]);

        $user->update([
            'employeeID' => $request->employeeID,
            'firstname'  => $request->firstname,
            'lastname'   => $request->lastname,
            'email'      => $request->email,
            'contact'    => $request->contact,
            'role'       => $request->role ?? $user->role,
            'status'     => $request->status,
            'password'   => $request->filled('password') ? Hash::make($request->password) : $user->password,
        ]);

        return response()->json([
            'message' => 'User updated successfully',
            'user'    => $user
        ]);
    }

    /**
     * Delete user by ID.
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * Fetch user by employeeID (path param).
     */
    public function showByEmployeeID($employeeID)
    {
        $user = User::where('employeeID', $employeeID)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }
}
