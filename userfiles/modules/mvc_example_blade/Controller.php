<?php

namespace mvc_example_blade;


class Controller
{

    function __construct()
    {
        $views_dir = __DIR__ . DS . 'views' . DS;
        \View::addNamespace('mvc_example_blade', $views_dir);


    }

    function index()
    {
        $posts = \Content::orderBy('id', 'desc')->paginate(10)->get();
        $posts = \Content::all();

          //$view = \View::make('mvc_example_blade::index')->withPosts($posts)->render();
       // dd($posts);
         // $view = \View::make('mvc_example_blade::index')->with('posts', $posts)->render();
          $view = \View::make('mvc_example_blade::index')->with('posts', $posts);

      print $view;
//return $view;
//        print $view;
//
//        print 11111111;
    }


}

