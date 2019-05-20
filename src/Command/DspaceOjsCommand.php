<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use App\Service\DSpace2OJSService;


class DspaceOjsCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:dspaceojs';
    private $dspace_ojs_service;
    public function __construct( DSpace2OJSService $dspace_ojs_service)
    {
        $this->dspace_ojs_service=$dspace_ojs_service;
        parent::__construct();
    }

    protected function configure()
    {
        $this
        // the short description shown while running "php bin/console list"
        ->setDescription('execute dspace app.')

        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('This command execute dspace app...')
        ->addArgument('filename', InputArgument::REQUIRED, 'The filename off csv.')
    ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {  
        echo $input->getArgument('filename');
        $files= $this->dspace_ojs_service->splitFileIntoMultipleCSV( $input->getArgument('filename'));
        var_dump($files);
        $options['into_section']='IMPORTED';
        $options['authors_group']='Autor';
        $options['limit']=-1;
        
        $this->dspace_ojs_service->processFiles($files,$options);
        $output->writeln([
            // echo files/user_4/10915-837/10915-836,
             $input->getArgument('filename'),
             getcwd()
        ]);
    
    }
}