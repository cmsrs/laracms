<?php

namespace Cmsrs\Laracms\Models;

use Illuminate\Support\Facades\Http;

class Inspire {
    public function justDoIt() {
        $response = Http::get('https://inspiration.goprogram.ai/');
        //$response = [];
        //$response['quote'] = 'test quote11';  $response['author'] = 'rs';

        return $response['quote'] . ' -' . $response['author'];
    }
}
