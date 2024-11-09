<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use Illuminate\Http\Request;

class HistoriasController extends Controller
{
    public function index()
    {
        return Inertia::render('User/Historias');
    }
}
