<?php

namespace Exan\Migrations\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    'create',
    'Run migrations',
    ['migrations:create']
)]
class Create extends Command
{
    protected function configure()
    {
        $this->addArgument('directory', InputArgument::REQUIRED);
        $this->addArgument('name', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migrationName = date('Y_m_d__H_i_s_') . $input->getArgument('name');

        $migrationPath = $input->getArgument('directory') . '/' . $migrationName;

        mkdir($migrationPath, recursive: true);

        file_put_contents($migrationPath . '/up.php', '<?php' . PHP_EOL);
        file_put_contents($migrationPath . '/down.php', '<?php' . PHP_EOL);

        $output->writeln('<info>Created migration ' . $input->getArgument('name') . '</info>');

        return Command::SUCCESS;
    }
}
