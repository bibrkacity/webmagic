<?php

namespace App\Http\Controllers;

use App\Services\Blog;

class ParserController extends Controller
{
    public function start()
    {

        ini_set('max_execution_time',180);
        $blog = new Blog();
        $result = $blog->parser();

        return response()->json($result,200);
    }
}
