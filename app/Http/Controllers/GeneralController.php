<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class GeneralController extends Controller
{
    public function changeLanguage($locale){
        try {
            if (array_key_exists($locale, config("locale.languages"))) {
            session()->put("locale", $locale);
            App::setLocale($locale);
                return redirect()->back();
            }
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back();
        }
    }
}
