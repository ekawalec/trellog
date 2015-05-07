<?php
/**
 * This file is part of trellog.
 * 
 * @author Edmund Kawalec <edmund.kawalec@idealia.pl>
 * @since 1.0.4
 * 
 */
namespace Bigwhoop\Trellog\Console\Commands;

use Bigwhoop\Trellog\Config\TrellogConfig;
use Bigwhoop\Trellog\Trello\Client;
use Bigwhoop\Trellog\Trello\URLs as TrelloURLs;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Reinit extends Command
{
    protected function configure()
    {
        $this->setName('reinit')
             ->setDescription('Initialize trellog and create a .trellog.yml configuration file.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configFile = $this->getConfigPath($input);
        
        $existsNote = file_exists($configFile) ? "\n<warning>The existing file will be overwritten.</warning>" : '';
        
        $output->writeln(<<<TXT
Welcome to <info>trellog</info>.

This wizard will help you configure a trellog configuration file.
The file will be created at <info>$configFile</info>.$existsNote

You can change the path using the <info>--config</info> option.

TXT
        );
        
        $config = new TrellogConfig();

        $config = $this->getConfig($input, $output);

        $client = new Client($config->auth->apiKey, $config->auth->accessToken);
        $trello = new Client($config->auth->apiKey, $config->auth->accessToken);
        
        // Grab board ID
        $boards = $trello->getMyBoardsAsArray();
        $output->writeln("3) <info>Please select the board that contains the CHANGELOG list:</info>");
        $config->source->boardId = $this->presentSelection($input, $output, $boards);
        $output->writeln('');
        
        // Grab list
        $lists = $trello->getListsForBoardAsArray($config->source->boardId);
        $output->writeln("4) <info>Finally select the list that represents the CHANGELOG:</info>");
        $config->source->listId = $this->presentSelection($input, $output, $lists);
        $output->writeln('');
        
        $config->save($configFile);
        
        $output->writeln("Successfully wrote configuration to <info>$configFile</info>.");
        $output->writeln("Use <info>trellog generate > CHANGELOG.md</info> to generate your first CHANGELOG.");
    }
}
