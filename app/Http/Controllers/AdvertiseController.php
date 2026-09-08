<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdvertiseController extends Controller
{
    public function index()
    {
        // ✅ Your Google Form URL
        $formUrl = 'https://docs.google.com/forms/d/e/1FAIpQLSdjBKL7D6YIgArdeVVJyLno9uJm1yaWo_6DyU6oOPUbso1D2g/viewform?usp=publish-editor';
        
        return view('pages.advertise', compact('formUrl'));
    }
}