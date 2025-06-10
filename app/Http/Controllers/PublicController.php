<?php


namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class PublicController extends Controller
{

    public function index(){
        return  Inertia::render('Index');
    }

    public function shippingAddress(){
        return  Inertia::render('ShippingAddress');
    }

    public function jerseySet(){
        return  Inertia::render('JerseySet');
    }
    public function orderPreview(){
        return  Inertia::render('OrderPreview');
    }
    public function thankYou(){
        return  Inertia::render('ThankYou');
    }
}