<?php

return new Sami\Sami(__DIR__ . '/src', array(
    'title' => 'SRU client',
    'build_dir' => __DIR__ . '/api_docs',
    'cache_dir' => __DIR__ . '/cache',
    'default_opened_level' => 2,
));

