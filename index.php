<?php
use Kirby\Filesystem\F;


F::loadClasses([
	'Leobard\\KirbyLinkedData\LinkedDataForPage' =>  'src/LinkedDataForPage.php'
], __DIR__);

Kirby::plugin(
    name: 'leobard/kirby-linkeddata',
    extends: [
        'snippets'  => [
            'linkeddata/forpage' => __DIR__ . '/snippets/forpage.php',
        ],
    ]
);
