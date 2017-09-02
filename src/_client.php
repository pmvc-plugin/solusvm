<?php

namespace PMVC\PlugIn\solusvm;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\Client';

class Client
{
    public function __invoke(
        $host,
        $key,
        $hash
    )
    {
        return new SolusvmClient(
            $host,
            $key,
            $hash
        );
    }
}

class SolusvmClient
{
    private $_host;
    private $_key;
    private $_hash;

    public function __construct(
        $host,
        $key,
        $hash
    )
    {
        $this->_host = $host;
        $this->_key = $key;
        $this->_hash = $hash;
    }

    // https://documentation.solusvm.com/display/DOCS/PHP+Code+Examples
    public function process($act) {
        $url = \PMVC\plug('url')->
            getUrl($this->_host);
        $url->set('/api/client/command.php');
        $store = null;
        $curl = \PMVC\plug('curl');
        $curl->post(
            $url,
            function($r) use (&$store) {
                if (empty($r->body)) {
                    return;
                }
                $pXml = \PMVC\plug('xml')->xml();
                $xml = '<root>'.$r->body.'</root>';
                $arr = $pXml->toArray($xml)['@children'];
                $arr = array_combine(array_column($arr, '@name'), array_column($arr, '@children'));
                $store = $arr;
            },
            [
                'key'   => $this->_key,
                'hash'  => $this->_hash,
                'action'=> $act 
            ]
        );
        $curl->process();
        return $store;
    }
}
