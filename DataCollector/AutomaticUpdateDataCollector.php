<?php
namespace Lsw\AutomaticUpdateBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AutomaticUpdateDataCollector
 *
 * @author Maurits van der Schee <m.vanderschee@leaseweb.com>
 */
class AutomaticUpdateDataCollector extends DataCollector
{
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array('updates'=>array());
    }

    /**
     * Method returns amount of logged API calls
     *
     * @return number
     */
    public function getUpdateCount()
    {
        return count($this->data['updates']);
    }

    /**
     * Method returns amount of logged API calls
     *
     * @return number
     */
    public function getUpdates()
    {
        return $this->data['updates'];
    }

}
