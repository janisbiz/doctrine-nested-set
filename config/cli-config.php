<?php

require_once sprintf('%s/../vendor/autoload.php', __DIR__);

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Janisbiz\DoctrineNestedSet\Tests\Features\Bootstrap\FeatureContext;

return ConsoleRunner::createHelperSet((new FeatureContext())->getEntityManager());
