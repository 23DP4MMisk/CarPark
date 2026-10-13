<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\RedirectResponse;

class LoginResponse implements Responsable
{
    public function toResponse($request): RedirectResponse
    {
        return new RedirectResponse(static::url());
    }

    public static function url(): string
    {
        $default = auth()->user()?->is_admin ? '/admin/cars' : '/';

        // Same as redirect()->intended(); Livewire swaps the Redirector so it can't be used here.
        return url(session()->pull('url.intended', $default));
    }
}
