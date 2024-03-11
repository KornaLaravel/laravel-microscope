<?php

namespace Imanghafoori\LaravelMicroscope\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Facade;
use Imanghafoori\LaravelMicroscope\ErrorReporters\ErrorPrinter;
use Imanghafoori\LaravelMicroscope\SearchReplace\FullNamespaceIs;
use Imanghafoori\LaravelMicroscope\SearchReplace\NamespaceIs;
use Imanghafoori\LaravelMicroscope\SearchReplace\IsSubClassOf;
use Imanghafoori\LaravelMicroscope\Traits\LogsErrors;
use Imanghafoori\SearchReplace\Filters;
use Imanghafoori\LaravelMicroscope\Features\CheckImports\Reporters;

class EnforceHelpers extends Command
{
    use LogsErrors;
    use PatternApply;

    protected $signature = 'enforce:helper_functions {--f|file=} {--d|folder=}';

    protected $description = 'Enforces helper functions over laravel internal facades.';

    protected $customMsg = 'No facade was found to be replaced by helper functions.  \(^_^)/';

    public function handle(ErrorPrinter $errorPrinter)
    {
        event('microscope.start.command');
        $this->info('Soaring like an eagle...');

        $errorPrinter->printer = $this->output;

        $fileName = ltrim($this->option('file'), '=');
        $folder = ltrim($this->option('folder'), '=');
        Filters::$filters['is_sub_class_of'] = IsSubClassOf::class;
        Filters::$filters['full_namespace_pattern'] = FullNamespaceIs::class;
        Filters::$filters['namespace_pattern'] = NamespaceIs::class;

        $errorPrinter->printer = $this->output;

        Reporters\Psr4Report::$callback = function () use ($errorPrinter) {
            $errorPrinter->flushErrors();
        };

        $patterns = $this->getPatterns();
        $this->appliesPatterns($patterns, $fileName, $folder);

        $this->finishCommand($errorPrinter);

        $errorPrinter->printTime();

        return $errorPrinter->hasErrors() ? 1 : 0;
    }

    private function getPatterns(): array
    {
        $mutator = function ($matches) {
            $matches[0][1] = strtolower($matches[0][1]);

            return $matches;
        };

        return [
            'full_facade_paths' => [
                'search' => '<class_ref>::',
                'replace' => '<1>()->',
                'filters' => [
                    1 => [
                        'full_namespace_pattern' => 'Illuminate\\Support\\*',
                        'is_sub_class_of' => Facade::class,
                        'in_array' => ['Auth', 'Session', 'Config', 'Cache', 'Redirect', 'Request'],
                    ],
                ],
                'mutator' => $mutator,
            ],
            'facade_aliases' => [
                'search' => '<class_ref>::',
                'replace' => '<1>()->',
                'filters' => [
                    1 => [
                        'namespace_pattern' => '',
                        'in_array' => ['Auth', 'Session', 'Config', 'Cache', 'Redirect', 'Request'],
                    ],
                ],
                'mutator' => $mutator,
            ],
        ];
    }
}
