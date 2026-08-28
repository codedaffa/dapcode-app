<?php

namespace App\Modules\Achievement\Controllers;

use App\Http\Controllers\Core\AchievementControllers;
use Illuminate\Http\Request;

class Achievement extends AchievementControllers
{
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => __('modules.achievement.title'),
            'subtitle' => __('modules.achievement.subtitle'),
        ]);
    }
}