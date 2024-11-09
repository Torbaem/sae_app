<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ContactanosController extends Controller
{
    public function index()
    {
        return Inertia::render('User/Contactanos');
    }
}
