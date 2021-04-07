<?php

require 'vendor/autoload.php';

use App\Result;
use App\ResultItem;

use App\Engine\Wikipedia\WikipediaEngine;
use App\Engine\Wikipedia\WikipediaParser;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\Table;

class SearchCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('Termo')
            ->setDescription('Busca pelo termo')
            ->addArgument('name', InputArgument::REQUIRED, 'The person name');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $wikipedia = new WikipediaEngine(new WikipediaParser(), HttpClient::create());

       try{ 
        $result = $wikipedia->search($input->getArgument('name'));


        if(count($result) === 0)
        {
            $output->writeln("<fg=red>". str_pad("",198,"=")."</>");
            $output->writeln("<fg=red>". " Nenhum resultado encontrado para o termo '".$input->getArgument('name')."' no 'wikipedia'</>");
            $output->writeln("<fg=red>". str_pad("",198,"=")."</>");
            return 0;  
       }
   
       }catch(Exceptio $e){
        return 0;  
       }

        $output->writeln("<fg=yellow>". str_pad("",198,"=")."</>");
        $output->writeln("<fg=yellow>". $result->count() . " resultados encontrado(s) para o termo '".$input->getArgument('name')."' no 'wikipedia'</>");
        $output->writeln("<fg=yellow>". str_pad("",198,"=")."</>");

        $output->writeln("Mostrando primeiro ". $result->countItemsOnPage() . " resultado(s):");

        foreach($result as $resultItem){
                $rows[] = [$resultItem->getTitle(), $resultItem->getPreview()];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['Título','Preview'])
            ->setRows($rows);
        $table->render();

        return 0;
    }
}
$app = new Application();
$app->add(new SearchCommand());
$app->run();

?>