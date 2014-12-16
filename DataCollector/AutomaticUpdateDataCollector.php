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
        if (file_exists($rootDir.'/app/SymfonyRequirements.php')) {
            $lastUpdate = filemtime($rootDir.'/app/SymfonyRequirements.php');
        } elseif (file_exists($rootDir.'/var/SymfonyRequirements.php')) {
            $lastUpdate = filemtime($rootDir.'/var/SymfonyRequirements.php');
        }
        $packages = array();
        $packageCount=0;
        $unstablePackageCount=0;
        foreach ($installed->packages as $package)
        {
            $name = $package->name;
            $description = $package->description;
            $version = $package->source->reference;
            $required = isset($require[$name])?$require[$name]:'-';
            $unstable = strlen($version)==40;
            $packages[] = compact('name','required','version','unstable','description');
            // update counters
            $unstablePackageCount+=$unstable;
            $packageCount++;
        }

        $this->data = compact('lastUpdate','packages','packageCount','unstablePackageCount');

    }

    private function collectComposerUpdates($rootDir)
    {

    }

    /**
     * Method returns date of last update
     *
     * @return number
     */
    public function getLastUpdate()
    {
        return $this->data['lastUpdate'];
    }


    /**
     * Method returns days since last update
     *
     * @return number
     */
    public function getDays()
    {
        return round((time()-$this->data['lastUpdate'])/86400);
    }


    /**
     * Method returns amount of installed packages
     *
     * @return number
     */
    public function getPackageCount()
    {
        return $this->data['packageCount'];
    }

    /**
     * Method returns amount of installed unstable packages
     *
     * @return number
     */
    public function getUnstablePackageCount()
    {
        return $this->data['unstablePackageCount'];
    }

    /**
     * Method returns the installed packages
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
