<?php
namespace Lsw\AutomaticUpdateBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;

/**
 * AutomaticUpdateDataCollector
 *
 * @author Maurits van der Schee <m.vanderschee@leaseweb.com>
 */
class AutomaticUpdateDataCollector extends DataCollector
{

    private $kernel;

    /**
     * Class constructor
     *
     * @param KernelInterface $kernel Kernel object
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {

        $rootDir = realpath($this->kernel->getRootDir() . '/../');
        $installed = json_decode(file_get_contents($rootDir.'/composer.lock'));
        $require = json_decode(file_get_contents($rootDir.'/composer.json'));
        $require = (array)$require->require;
        $packages = array();
        foreach ($installed->packages as $package)
        {
            $name = $package->name;
            $description = $package->description;
            $version = $package->source->reference;
            if (isset($require[$name])) {
                $required = $require[$name];
                $packages[] = compact('name','required','version','description');
            }
        }

        $this->data = compact('packages');

    }

    private function collectComposerUpdates($rootDir)
    {

    }

    /**
     * Method returns amount of logged API calls
     *
     * @return number
     */
    public function getUpdateCount()
    {
        return count($this->data['packages']);
    }

    /**
     * Method returns amount of logged API calls
     *
     * @return number
     */
    public function getPackages()
    {
        return $this->data['packages'];
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'update';
    }
}
