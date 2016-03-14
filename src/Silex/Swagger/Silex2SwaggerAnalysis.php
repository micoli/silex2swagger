<?php

/*
* This file is part of the silex2swagger library.
*
* (c) Martin Rademacher <mano@radebatz.net>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Radebatz\Silex\Swagger;

use Swagger\Analyser;
use Swagger\Analysis;
use Silex\Application;
use Radebatz\Silex\Swagger\Annotations\CustomAnnotation;
use Radebatz\Silex\Swagger\Processors\CustomAnnotations;

/**
 * Silex 2 swagger analysis to wrap Silex annotations.
 */
class Silex2SwaggerAnalysis extends Analysis
{
    protected $customAnnotationsProcessor;

    /**
     * {@inheritdoc}
     */
    public function __construct($annotations = [], $context = null, Silex2SwaggerConverter $silex2SwaggerConverter = null, array $namespaces = [])
    {
        parent::__construct($annotations, $context);

        Analyser::$whitelist[] = 'DDesrosiers\SilexAnnotations\Annotations';
        foreach ($namespaces as $namespace) {
            Analyser::$whitelist[] = $namespace;
        }

        $this->customAnnotationsProcessor = new CustomAnnotations($silex2SwaggerConverter);
    }

    /**
     * {@inheritdoc}
     */
    public function addAnnotation($annotation, $context)
    {
        if (!$annotation instanceof AbstractAnnotation) {
            $annotation = new CustomAnnotation(['_context' => $context], $annotation);
        }

        return parent::addAnnotation($annotation, $context);
    }

    public function process($processors = null)
    {
        if ($processors === null) { // Use the default and registered processors.
            $processors = self::processors();
        }
        if (is_array($processors) === false && is_callable($processors)) {
            $processors = [$processors];
        }
        // prepend our custom annotation converter
        array_unshift($processors, $this->customAnnotationsProcessor);

        parent::process($processors);
    }
}
