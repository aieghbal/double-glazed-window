<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    public function __invoke(string $locale): RedirectResponse
    {
        abort_unless(array_key_exists($locale, config('locales.supported', [])), 404);

        session(['locale' => $locale]);

        return redirect()->back();
    }
}
