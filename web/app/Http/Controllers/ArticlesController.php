<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Article;

class ArticlesController extends Controller
{
    public function index(Request $request)
    {

        $field = (isset($request->f)) ? $request->f : 'author';  //current field for sorting
        $direction = (isset($request->d)) ? $request->d : 'asc'; //current direction for sorting

        $articles = Article::orderBy($field,$direction)->paginate(15);

        $arrows_title = $this->arrows('title',$field,$direction);
        $arrows_date = $this->arrows('published_at',$field,$direction);
        $arrows_author = $this->arrows('author',$field,$direction);

        return view('articles.index',
            [
                 'articles'         => $articles
                ,'field'            => $field
                ,'direction'        => $direction
                ,'arrows_title'     => $arrows_title
                ,'arrows_date'      => $arrows_date
                ,'arrows_author'    => $arrows_author

            ]);
    }


    /**
        Generate arrows for select sort mode
     * $name - name of field for arrows
     * $field - current field for sorting
     * $direction - current direction for sorting
     *
     */
    private function arrows($name, $field, $direction):String
    {
        $html = '<div class="arrows">';

        $html .="<div>";

        if( ($name == $field) && ($direction == 'asc'))
            $html.='&#9652';
        else
            $html.='<a href="?f='.$name.'&d=asc">&#9652</a>';

        $html .="</div>";

        $html .="<div>";
        if( ($name == $field) && ($direction == 'desc'))
            $html.='&#9662';
        else
            $html.='<a href="?f='.$name.'&d=desc">&#9662</a>';
        $html .="</div>";

        $html .= '</div>';

        return $html;
    }
}
