<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::orderBy('name')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([...$data, 'is_active' => true, 'email_verified_at' => now()]);
        AuditLogger::log('admin.user_created', $user, null, $user->only(['name', 'email', 'role']));

        return redirect()->route('admin.users.index')->with('success', "Account created for {$user->name}.");
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'is_active' => ['boolean'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $old = $user->only(['name', 'email', 'role', 'is_active']);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        // Prevent self-lockout: the last active super admin cannot be demoted/disabled.
        if ($user->isSuperAdmin() && (($data['role'] ?? $user->role) !== User::ROLE_SUPER_ADMIN || ! ($data['is_active'] ?? false))) {
            $others = User::where('role', User::ROLE_SUPER_ADMIN)->where('is_active', true)->where('id', '!=', $user->id)->count();
            if ($others === 0) {
                return back()->withErrors(['role' => 'At least one active Super Administrator account must remain.']);
            }
        }

        $data['is_active'] = $request->boolean('is_active');
        $user->update($data);

        AuditLogger::log('admin.user_updated', $user, $old, $user->only(['name', 'email', 'role', 'is_active']));

        return redirect()->route('admin.users.index')->with('success', 'Account updated.');
    }
}
