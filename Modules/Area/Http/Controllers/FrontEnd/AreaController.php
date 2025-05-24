<?php

namespace Modules\Area\Http\Controllers\FrontEnd;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Area\Repositories\FrontEnd\AreaRepository as Area;

class AreaController extends Controller
{
    protected $area;

    function __construct(Area $area)
    {
        $this->area = $area;
    }

    public function getChildAreaByParent(Request $request)
    {
        $items = $this->area->getChildAreaByParent($request);
        return response()->json(['success' => true, 'data' => $items]);
    }
}
