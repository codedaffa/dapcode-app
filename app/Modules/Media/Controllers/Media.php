<?php

namespace App\Modules\Media\Controllers;

use App\Http\Controllers\Core\MediaControllers;
use Illuminate\Http\Request;

class Media extends MediaControllers
{
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => __('modules.media.title'),
            'subtitle' => __('modules.media.subtitle'),
        ]);
    }
}