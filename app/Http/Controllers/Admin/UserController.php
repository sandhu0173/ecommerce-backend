<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('is_admin', false)->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('orders');
        return view('admin.users.show', compact('user'));
    }

    public function toggleAdmin(User $user)
    {
        $user->update(['is_admin' => !$user->is_admin]);
        return redirect()->back()->with('success', 'User admin status updated');
    }

    public function destroy(User $user)
    {
        if ($user->is_admin) {
            return redirect()->back()->with('error', 'Cannot delete admin users');
        }

        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully');
    }
}
