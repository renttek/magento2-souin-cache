<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
                ->withPaths([__DIR__ . '/src'])
                ->withPreparedSets(
                    psr12            : true,
                    symplify         : true,
                    arrays           : true,
                    comments         : true,
                    docblocks        : true,
                    spaces           : true,
                    namespaces       : true,
                    controlStructures: true,
                    phpunit          : true,
                    strict           : true,
                    cleanCode        : true,
                )
                ->withSkip(
                    [
                        \PhpCsFixer\Fixer\Whitespace\TypeDeclarationSpacesFixer::class,
                        \PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer::class,
                        \PhpCsFixer\Fixer\Basic\BracesPositionFixer::class,
                        \PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer::class,
                        \PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer::class,
                    ],
                );
