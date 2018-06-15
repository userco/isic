<?php

namespace ISICBundle\Jobs;

use BCC\ResqueBundle\Job;
use DateTime;
use ISICBundle\Command\XMLCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use BCC\ResqueBundle\ContainerAwareJob;
class XMLJob extends ContainerAwareJob
{

    public function run($args)
    {
    	
    	
    $container = $this->getContainer();
    $command = new XMLCommand();
    $command->setContainer($container);
    
    $input = new ArrayInput(array());
    $output = new ConsoleOutput();

    $command->run($input, $output);
   
  
    }
    
}

