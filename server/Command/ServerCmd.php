<?php

declare(strict_types=1);
/**
 * This file is part of server.
 */
namespace Server\Command;

use Server\Config;
use Server\Server;
use Swoole\Process;
use Swoole\Runtime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ServerCmd extends Command
{

    public function __construct($name = null)
    {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            // 命令的名称
            ->setName('server')
            // 简短描述
            ->setDescription('swoole server')
            // 运行命令时使用 "--help" 选项时的完整命令描述
            ->setHelp('Create curd')
            //->addArgument('optional_argument', InputArgument::REQUIRED, 'this is a optional argument');
            ->setDefinition([
                new InputArgument('option', InputArgument::REQUIRED, 'options[start|stop|restart|reload]'),
            ]);
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $option = $input->getArgument('option');
        if( !method_exists($this,$option) ){
            $output->writeln($option." not exist");
            return 1;
        }
        return $this->$option($output);
    }

    public function start(OutputInterface $output){
        Runtime::enableCoroutine();
        try {
            Config::load();
            $Server = new Server();
            $Server->start();
        } catch (\Exception $e) {
            print_r($e->getMessage());
        } catch (\Throwable $throwable) {
            print_r($throwable->getMessage());
        }
        return 0;
    }
    public function stop(OutputInterface $output){
        $pid = $this->getPid();
        if( $pid === null ){
            $output->writeln("进程未启动");
        }else{
            Process::kill( $pid , SIGTERM   );
        }
        return 1;
    }
    public function restart(OutputInterface $output){
        $pid = $this->getPid();
        if( $pid === null ){
            $output->writeln("进程未启动");
        }else{
            Process::kill( $pid , SIGTERM   );
            while( Process::kill( $pid , 0   ) !== false ){
                usleep(100000);
            }
        }
        $this->start( $output);
        return 1;
    }
    public function reload(OutputInterface $output){
        $pid = $this->getPid();
        if( $pid === null ){
            $output->writeln("进程未启动，请使用start");
        }else{
            Process::kill( $pid , SIGUSR1   );
        }
        return 1;
    }

    public function getPid(){
        $pidPath = BASE_PATH.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR."swoole.pid";
        if( file_exists($pidPath) ){
            $pid = file_get_contents($pidPath);
            if( !empty($pid) ){
                return intval($pid);
            }
        }
        return null;
    }

}
