<?php

namespace Exan\Migrations\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    'rollback',
    'Run migrations',
    ['migrations:rollback']
)]
class Rollback extends Command
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

        $output->writeln('<comment>Starting rollback..</comment>');
        foreach ($migrationPaths as $migration) {
            $ran = $this->downMigration($migration);

            $output->writeln(sprintf(
                '- %s %s',
                $ran ? 'Finished' : 'Skipped',
                $migration
            ));
        }

        return Command::SUCCESS;
    }

    private function downMigration(string $migration): bool
    {
        $migrationInfo = scandir($migration);

        $hasDown = in_array('down.php', $migrationInfo);
        if (!$hasDown) {
            return false;
        }

        $hasRun = in_array('.migrated', $migrationInfo);
        if (!$hasRun) {
            return false;
        }

        require($migration . '/down.php');

        unlink($migration . '/.migrated');

        return true;
    }
}
