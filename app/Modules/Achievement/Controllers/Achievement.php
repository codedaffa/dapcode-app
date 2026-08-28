<?php

namespace App\Modules\Achievement\Controllers;

use App\Http\Controllers\Core\AchievementControllers;
use Illuminate\Http\Request;

class Achievement extends AchievementControllers
{
    /**
     * Display the index page for Achievement module.
     * Accessible via: /Achievement
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => 'HMVC Achievement Module'
        ]);
    }
}