<?php

namespace Exan\Migrations\Commands;

use Exan\Migrations\Exceptions\NoUpMigrationException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    'migrate',
    'Run migrations',
    ['migrations:run']
)]
class Migrate extends Command
{
    protected function configure()
    {
        $this->addArgument('directory', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = $input->getArgument('directory');

        $migrations = array_values(array_filter(scandir($directory), fn ($dir) => !in_array($dir, ['.', '..'])));

        $migrationPaths = array_map(fn ($migration) => $directory . '/' . $migration, $migrations);

        $output->writeln('<info>Starting migrations..</info>');
        foreach ($migrationPaths as $migration) {
            $ran = $this->runMigration($migration);

            $output->writeln(sprintf(
                '- %s %s',
                $ran ? 'Finished' : 'Skipped',
                $migration
            ));
        }

        return Command::SUCCESS;
    }

    private function runMigration(string $migration): bool
    {
        $migrationInfo = scandir($migration);

        $hasUp = in_array('up.php', $migrationInfo);
        if (!$hasUp) {
            throw new NoUpMigrationException();
        }

        $hasRun = in_array('.migrated', $migrationInfo);
        if ($hasRun) {
            return false;
        }

        require($migration . '/up.php');

        file_put_contents($migration . '/.migrated', '');

        return true;
    }
}
