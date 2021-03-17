<?php

namespace App\Services;

use Carbon\Carbon;
use App\Article;
use App\Tag;

class Blog
{

    const MONTH_AGO_DEFAULT=4;

    private $month_ago;

    private $inserted;  // for return in results
    private $updated;   // for return in results
    private $cursor;    // date of last article

    public function __construct($month_ago = self::MONTH_AGO_DEFAULT)
    {
        $this->month_ago = (int)$month_ago;
    }


    public function parser()
    {

        $date_from = Carbon::now()->subMonths(4);

        $this->cursor = Carbon::now();

        \Log::info($date_from);

        $page = 0;
        $this->inserted = 0;
        $this->updated  = 0;

        \Log::info('days: ' . $this->cursor->diffInDays($date_from,false));

        while($this->cursor->diffInDays($date_from,false) < 1)
        {
            \Log::info('------------------ start page '.($page+1).'-------------');
            \Log::info($this->cursor);
            \Log::info('days: ' . $this->cursor->diffInDays($date_from,false));
            $this->one_page(++$page);
        }


        return ['inserted' => $this->inserted, 'updated' => $this->updated,'page' => $page];
    }

  /******************************
   *   p r i v a t e
   ****************************** */

    private function one_page($page)
        {
            $url = 'https://laravel-news.com/blog?page='.$page;

            $html = file_get_contents($url);

            $re = '/\<main.*\<\/main\>/s';

            if(!preg_match($re,$html,$m))
                return;

            libxml_use_internal_errors(true); // to pass HTML-5 tags
            $dom = new \DOMDocument;
            $dom->loadHTML($m[0]);
            libxml_clear_errors();

            $uls = $dom->getElementsByTagName('ul');

            foreach ($uls as $ul) {
                if( $this->check_ul($ul))
                {
                    $childs = $ul->childNodes;
                    foreach($childs as $child)
                    {
                        if($child->nodeType == 1)
                        {
                            if($child->nodeName == 'li' )
                                $this->li( $child);
                        }

                    }
                    break;
                }
            }
        }


        private function li($li)
        {
            $childs = $li->childNodes;
            foreach($childs as $a)
            {
                if($a->nodeType == 1)
                {
                    if($a->nodeName == 'a' )
                    {

                        $href = $a->attributes->getNamedItem("href")->nodeValue;
                        $slug = preg_replace('/^\//','',$href);

                        $childs2 = $a->childNodes;
                        foreach($childs2 as $div)
                        {
                            if($div->nodeType == 1)
                                if($div->nodeName == 'div')
                                {

                                    $className = $div->attributes->getNamedItem("class")->nodeValue ;

                                    if ($className == 'col-span-12' || $className == 'flex flex-col') {
                                        $text = $div->textContent;

                                        if (preg_match('/News/m', $text)) {
                                            $array = explode("\n", $text);
                                            $array2 = array_filter($array);
                                            $lines = [];
                                            foreach ($array2 as $line)
                                                if(preg_match('/\w/',$line))
                                                    $lines[] = $line;

                                            \Log::info($lines);

                                            $title = $lines[2];

                                            $ds = preg_replace('/th/', '', $lines[1]);

                                            $d = date_parse_from_format("F j, Y",$ds);

                                            \Log::info($d);

                                            $this->cursor = Carbon::createFromFormat('Y-n-d', $d['year'] . '-' . $d['month'] . '-' . $d['day']);

                                            $author = $this->one_article($slug);

                                            $this->save($slug, $title, $author, $this->cursor);

                                        }
                                        break;
                                    }
                                }
                        }
                        break;
                    }

                }

            }


        }

    private function one_article($slug)
    {
        $author = 'John Doe';
        $url = 'https://laravel-news.com/'.$slug;
        $html = file_get_contents($url);

        $re = '/itemprop="author"\>
\<a href="[^"]+"\>([^\<]+)\<\/a\>
<\/p>/m';

        if(preg_match($re,$html,$m))
            $author = $m[1];

        return $author;
    }


    private function save($slug,$title,$author,$cursor)
    {
        $id = 0;

        $result = Article::where('slug','=',$slug)->first();
        if($result)
            $id = (int)$result->id;

        $article = new Article();

        if($id != 0)
            $article = Article::find($id);

        $article->slug = $slug;
        $article->title = $title;
        $article->author = $author;
        $article->content= '';
        $article->published_at = $cursor->format('Y-m-d');
        $article->save();

        if($id == 0)
        {
            $tags = $this->get_tags($slug);
            $article->tags()->saveMany($tags);
        }


        if($id == 0)
            $this->inserted++;
        else
            $this->updated++;

    }


    private function check_ul($ul)
    {
        $check = false;
        $className = '';
        foreach($ul->attributes as $one)
            if($one->name == 'class')
                $className =  $one->nodeValue;

        return ($className == 'md:gap-16 xl:col-span-6 xl:col-start-2 lg:-mt-24 grid grid-cols-12 col-span-10 col-start-2 gap-6 pb-24 -mt-16');
    }

    private function get_tags($slug)
    {//STUB  Dont understand how th get tags
        return [
            new Tag(['tag'=>'laravel']),
            new Tag(['tag'=>'news'])
            ];

    }
}
