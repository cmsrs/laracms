<?php
namespace Cmsrs\Laracms\Controllers;

use Illuminate\Http\Request;
use Cmsrs\Laracms\Models\Inspire;

class InspirationController
{
    public function __invoke(Inspire $inspire) {
        $quote = $inspire->justDoIt();

        //die( $quote  );
        return view('laracms::index', compact('quote'));
    }

    public function trs() {
        die('___________trs___');
    }
}
