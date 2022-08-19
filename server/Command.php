<?php

declare(strict_types=1);
/**
 * This file is part of server.
 */
namespace Server;
use Server\Core\File;
use Symfony\Component\Console\Application;

class Command
{

    public function __invoke(){
        $application = new Application();
        $CommandPath = BASE_PATH . DIRECTORY_SEPARATOR . 'server' . DIRECTORY_SEPARATOR . 'Command';
        $files = File::tree( $CommandPath , "/.php$/" );
        if(!empty($files)){
            foreach ($files as $path=>$fileNameArr){
                foreach ($fileNameArr as $fileName){
                    $fileName = str_replace('.php','',$fileName);
                    $fileName = '\Server\Command\\'.$fileName;
                    $application->add(new $fileName());
                }
            }
        }
        $application->run();
    }

}
