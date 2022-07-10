<?php

require __DIR__.'/../vendor/autoload.php';

use EvoNext\Tracy\Tracy;

// before outout
$tracy = Tracy::instance();

function sql($sql)
{
    $tracy = Tracy::instance();
    $databasePanel = $tracy->getPanel('database');
    $databasePanel->logQuery($sql);
}

sql('select * from users');
sql('select * from news');
sql('select * from products');
