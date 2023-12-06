<?php

namespace Imanghafoori\LaravelMicroscope\Features\CheckGenericDocBlocks;

use Illuminate\Console\Command;
use Imanghafoori\LaravelMicroscope\ForPsr4LoadedClasses;
use Imanghafoori\LaravelMicroscope\Traits\LogsErrors;

class CheckGenericDocBlocks extends Command
{
    use LogsErrors;

    protected $signature = 'check:generic_docblocks {--f|file=} {--d|folder=} {--nofix}';

    protected $description = 'Removes generic doc-blocks from your controllers.';

    protected $customMsg = 'Doc-blocks were removed.';

    public function handle()
    {
        $this->info('Removing generic doc-blocks...');

        $this->setConformer();

        ForPsr4LoadedClasses::check([GenericDocblocks::class], [], ltrim($this->option('file'), '='), ltrim($this->option('folder'), '='));

        $this->info(GenericDocblocks::$foundCount.' generic doc-blocks were found.');
        $this->info(GenericDocblocks::$removedCount.' of them were removed.');

        return GenericDocblocks::$foundCount > 0 ? 1 : 0;
    }

    private function getQuestion($absFilePath)
    {
        return 'Do you want to remove doc-blocks from: <fg=yellow>'.basename($absFilePath).'</>';
    }

    private function setConformer()
    {
        if ($this->option('nofix')) {
            GenericDocblocks::$confirmer = function () {
                return false;
            };
        } else {
            GenericDocblocks::$confirmer = function ($absPath) {
                return $this->confirm($this->getQuestion($absPath), true);
            };
        }
    }
}
