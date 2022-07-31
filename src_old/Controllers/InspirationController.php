<?php
namespace Cmsrs\Laracms\Controllers;

use Illuminate\Http\Request;
use Cmsrs\Laracms\Inspire;

class InspirationController
{
    public function __invoke(Inspire $inspire) {
        $quote = $inspire->justDoIt();

        return view('laracms::index', compact('quote'));
    }
}
