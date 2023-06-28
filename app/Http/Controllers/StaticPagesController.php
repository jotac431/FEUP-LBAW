<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticPagesController extends Controller
{
    /**
     * Displays about us page
     * 
     * @return View
     */
    public function getAboutUs()
    {
        return view('pages.staticPages.aboutUs');
    }

    /**
     * Displays about us page
     * 
     * @return View
     */
    public function getGuidelines()
    {
        return view('pages.staticPages.guidelines');
    }

    /**
     * Displays about us page
     * 
     * @return View
     */
    public function getFaq()
    {
        return view('pages.staticPages.faq');
    }
}
