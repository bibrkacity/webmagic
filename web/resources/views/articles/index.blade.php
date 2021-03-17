<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style type="text/css">

        div#table{
            display:table;
            border:4px double black;
        }

        div#table > div{
            display:table-row;
        }


        div#table > div > div{
            display:table-cell;
            padding:4px;
            border:1px solid black;
        }

        div#table > div:first-of-type > div{
            font-weight:bold;
        }

        li{
            display:inline;
            list-style: none;
        }

        div.arrows
        {
            display:inline-block;
            width:10px;
            margin-left:20px;
            margin-top:5px;
        }

        div.arrows > div
        {
            color:red;
            font-size:14pt;
            padding:0px;
        }

        div.arrows > div > a
        {
            color:blue;
            text-decoration:none;
        }

    </style>
</head>
<body>

<p><a href="/">Contents</a></p>

<h1>News</h1>

<div id="table">
    <div>
        <div>id</div>
        <div>Title {!! $arrows_title !!} </div>
        <div>Author{!! $arrows_author !!}</div>
        <div>Date{!! $arrows_date !!}</div>
        <div>Slug</div>
        <div>Tags</div>
    </div>

    @inject('carbon','Carbon\Carbon')

    @foreach($articles as $article)
            <div>
                <div>{{ $article->id }}</div>
                <div>{{ $article->title }}</div>
                <div>{{ $article->author }}</div>

                @php

                $d=$carbon::createFromFormat('Y-m-d',$article->published_at);
                $tags=[];
                foreach($article->tags as $tag)
                    $tags[] = $tag->tag;

                @endphp
                <div>{{ $d->format('d.m.Y') }}</div>
                <div><a href="https://laravel-news.com/{{ $article->slug }}">{{ $article->slug }}</a></div>
                <div>{{ implode(', ',$tags) }}</div>
            </div>
    @endforeach

</div>

{{$articles->appends(['f' => $field,'d'=>$direction])->links() }}

</body>
</html>
