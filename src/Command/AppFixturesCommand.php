<?php

namespace App\Command;

use App\Story\InitialStateStory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:fixtures',
    description: 'Enables the load of the fixtures',
)]
class AppFixturesCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title("Launching the load of the fixtures");
        InitialStateStory::load();

        $io->success('All fixtures have been successfully regenerated.');

        return Command::SUCCESS;
    }
}
