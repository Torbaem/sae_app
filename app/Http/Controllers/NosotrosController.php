<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use Illuminate\Http\Request;

class NosotrosController extends Controller
{
    public function index()
    {
        return Inertia::render('User/Nosotros');
    }
}
