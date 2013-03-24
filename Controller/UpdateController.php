<?php

namespace Lsw\AutomaticUpdateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Process\Process;
use Symfony\Component\HttpFoundation\Response;
use Lsw\AutomaticUpdateBundle\Ansi\Translator;
/**
 * Manage updates information
 */
class UpdateController extends Controller
{

    /**
     * Update all packages
     *
     * @return Response
     */
    public function executeAction()
    {
        $success = true;
        $path = realpath($this->get('kernel')->getRootDir() . '/../');
        $options = $this->container->getParameter('automatic_update.options');
        $commands = array();
        $secret = isset($_POST['secret'])?$_POST['secret']:false;
        if ($secret == $options['secret']) {
            foreach ($options['execute_commands'] as $command) {
                $object = $this->runCommand($path, $command);
                $commands[] = $object;
                if ($object->result) {
                    $success = false;
                    break;
                }
            }
        }
        $user = posix_getpwuid(posix_geteuid());
        $username = $user['name'];
        $hostname = trim(file_get_contents('/etc/hostname'));
        return $this->render(
            'LswAutomaticUpdateBundle:Update:execute.html.twig',
            compact('username', 'hostname', 'path', 'commands', 'success')
        );
    }

    /**
     * Update all packages (dry run)
     *
     * @return Response
     */
    public function dryRunAction()
    {
        $success = true;
        $path = realpath($this->get('kernel')->getRootDir() . '/../');
        $options = $this->container->getParameter('automatic_update.options');
        $commands = array();
        $secret = isset($_POST['secret'])?$_POST['secret']:false;
        if ($secret == $options['secret']) {
            foreach ($options['dry_run_commands'] as $command) {
                $object = $this->runCommand($path, $command);
                $commands[] = $object;
                if ($object->result) {
                    $success = false;
                    break;
                }
            }
        }
        $user = posix_getpwuid(posix_geteuid());
        $username = $user['name'];
        $hostname = trim(file_get_contents('/etc/hostname'));
        return $this->render(
            'LswAutomaticUpdateBundle:Update:dry_run.html.twig',
            compact('username', 'hostname', 'path', 'commands', 'success', 'secret')
        );
    }

    private function runCommand($path, $command)
    {
        $process = new Process('cd '.$path.';'.$command);
        $process->run();
        $stdout = $process->getOutput();
        $stderr = $process->getErrorOutput();
        $result = $process->getExitCode();
        return (object) compact('command','stdout','stderr','result');
    }

}
