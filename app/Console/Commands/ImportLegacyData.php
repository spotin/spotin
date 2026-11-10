<?php

namespace App\Console\Commands;

use App\Support\LegacyImport;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use RuntimeException;

/**
 * Import the users and spots of the previous application. See the how-to guide "Migrate the data of the previous
 * application" for the export of its tables and the mapping of its users to the organizations.
 */
#[Signature('spotin:import-legacy
    {directory : The directory of the exported tables, `users.json` and `spots.json`}
    {--mapping= : The CSV file of the organizations of the old users, `email,organization,role`, by default `organizations.csv` in the directory}
    {--dry-run : Tell what the import would do, without importing anything}')]
#[Description('Import the users and spots of the previous Spot in® application')]
class ImportLegacyData extends Command
{
    public function handle(): int
    {
        $directory = rtrim($this->argument('directory'), '/');

        try {
            $import = new LegacyImport($directory, $this->option('mapping') ?? $directory.'/organizations.csv');
        } catch (RuntimeException|\JsonException $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        if ($problems = $import->problems()) {
            $this->components->error('Nothing was imported. Fix these problems first:');
            $this->list($problems);

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $import->run($dryRun);

        $this->components->info($dryRun ? 'Dry run: nothing was imported. The import would do this:' : 'The legacy data were imported.');

        foreach ($import->counts() as $kind => $count) {
            $this->components->twoColumnDetail(ucfirst($kind), (string) $count);
        }

        if ($warnings = $import->warnings()) {
            $this->newLine();
            $this->components->warn('To check:');
            $this->list($warnings);
        }

        return self::SUCCESS;
    }

    /**
     * A list, one item per line, not wrapped: to copy them as they are.
     *
     * @param  list<string>  $items
     */
    private function list(array $items): void
    {
        foreach ($items as $item) {
            $this->line("  - {$item}");
        }

        $this->newLine();
    }
}
