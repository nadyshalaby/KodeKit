<?php

return [
    'twig' => function () {
        $loader = new Twig_Loader_Filesystem(__DIR__ . "/../resourses/views/");
        $twig = new Twig_Environment($loader);
        return $twig;
    },
];
