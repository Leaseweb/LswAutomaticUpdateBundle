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
        $bundles = array();
        foreach ($installed->packages as $bundle)
        {
            $name = $bundle->name;
            $description = $bundle->description;
            $version = $bundle->source->reference;
            $required = isset($require[$name])?$require[$name]:'-';
            $bundles[] = compact('name','required','version','description');
        }

        $this->data = compact('bundles');

    }

    private function collectComposerUpdates($rootDir)
    {

    }

    /**
     * Method returns amount of installed bundles
     *
     * @return number
     */
    public function getBundleCount()
    {
        return count($this->data['bundles']);
    }

    /**
     * Method returns the installed bundles
     *
     * @return number
     */
    public function getBundles()
    {
        return $this->data['bundles'];
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'update';
    }
}
