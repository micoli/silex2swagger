<?php

/*
* This file is part of the silex2swagger library.
*
* (c) Martin Rademacher <mano@radebatz.net>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Radebatz\Silex\Swagger\Processors;

use SplObjectStorage;
use Swagger\Analysis;
use Radebatz\Silex\Swagger\Silex2SwaggerConverter;
use Radebatz\Silex\Swagger\Annotations\CustomAnnotation;

/**
 * Process custom annotations.
 */
class CustomAnnotations
{
    protected $silex2swagger;

    /**
     * Create instance.
     */
    public function __construct(Silex2SwaggerConverter $silex2swagger)
    {
        $this->silex2swagger = $silex2swagger;
    }

    /**
     * Invoke processor.
     */
    public function __invoke(Analysis $analysis)
    {
        $add = new SplObjectStorage();
        $remove = new SplObjectStorage();
        foreach ($analysis->annotations as $annotation) {
            if ($annotation instanceof CustomAnnotation) {
                // migrate & replace
                foreach ($this->silex2swagger->migrateAnnotation($annotation) as $migrated) {
                    $add->attach($migrated);
                }

                // remove custom annotation
                $remove->attach($annotation);

            }
        }

        $analysis->annotations->removeAll($remove);
        $analysis->annotations->addAll($add);
    }
}
