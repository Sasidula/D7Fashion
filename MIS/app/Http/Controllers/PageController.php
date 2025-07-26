<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;

class PageController extends Controller
{
    public function show($page)
    {
        $view = 'pages.' . str_replace('/', '.', $page);

        if (View::exists($view)) {
            return view($view); // The view like pages.add-employee
        }

        abort(404); // Show 404 if the view doesn't exist
    }
}
