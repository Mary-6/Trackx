<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class ApiTokenController extends Controller
{
    public function index(): View
    {
        return view('admin.api-tokens.index', [
            'tokens' => PersonalAccessToken::with('tokenable')->latest()->paginate(15),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'expires_at' => ['nullable', 'date', 'after:today'],
        ]);

        $token = $request->user()->createToken(
            $data['name'],
            ['update-location'],
            isset($data['expires_at']) ? Carbon::parse($data['expires_at']) : null,
        );

        AuditLogger::log('admin.api_token_created', null, null, ['name' => $data['name']]);

        return back()
            ->with('success', 'Integration token created. Copy it now — it will not be shown again.')
            ->with('plainTextToken', $token->plainTextToken);
    }

    public function destroy(PersonalAccessToken $token): RedirectResponse
    {
        AuditLogger::log('admin.api_token_revoked', null, null, ['name' => $token->name]);
        $token->delete();

        return back()->with('success', 'Token revoked.');
    }
}
