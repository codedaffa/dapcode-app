<?php

namespace App\Http\Controllers;

use App\Services\HMVC\HMVC;
use Illuminate\Http\Request;

class HMVCController extends Controller
{
    /**
     * @var HMVC
     */
    protected $hmvc;

    public function __construct(HMVC $hmvc)
    {
        $this->hmvc = $hmvc;
    }

    /**
     * Handle dynamic HMVC request dispatching.
     *
     * @param Request $request
     * @param string $module
     * @param string|null $segment2
     * @param string|null $segment3
     * @param string|null $params
     * @return mixed
     */
    public function handle(Request $request, string $module, ?string $segment2 = null, ?string $segment3 = null, ?string $params = null)
    {
        return $this->hmvc->dispatch($request, $module, $segment2, $segment3, $params);
    }
}
