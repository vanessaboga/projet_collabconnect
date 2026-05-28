<?php


class Request
{
    var $id = null;
    var $da = null;
    var $soa = null;
    var $date = null;
    var $next = null;
    var $canal = null;
    var $smscid = null;
    var $result = null;
    var $content = null;
    var $sessionId = null;

    var $sequence = null;
    var $action = null;
    var $data = null;
    var $request;
    var $setting;
    var $telephone;
    var $plateforme;

    public $logger;
    public $maintenant;


    public function __construct($request)
    {

        $this->request = $request;
        $this->date = date('Y-m-d H:i:s');
        $this->maintenant = @date('Y-m-d H:i:s');

        #$this->soa=(isset($this->request['SOA'])?Config::$COUNTRY_CODE.substr($this->request['SOA'],-Config::$MSISDN_WITHOUT_COUNTRY_CODE_LENGTH):null);
        $this->soa = (isset($this->request['SOA']) ? $this->request['SOA'] : "");
        $this->da = (isset($this->request['DA']) ? $this->request['DA'] : "");
        $this->next = (isset($this->request['next']) ? $this->request['next'] : 'menu');
        $this->smscid = (isset($this->request['smscid']) ? $this->request['smscid'] : "");
        $this->content = (isset($this->request['Content']) ? $this->request['Content'] : null);
        $this->canal = (isset($this->request['canal']) ? $this->request['canal'] : 'USSD');
        $this->plateforme = (isset($this->request['canal']) ? $this->request['canal'] : 'USSD');

        $this->telephone = $this->soa;
        $this->sessionId = (isset($this->request['sessionId']) ? $this->request['sessionId'] : null);
        $this->sequence = (isset($this->request['sequence']) ? $this->request['sequence'] : null);
        $this->action = (isset($this->request['action']) ? $this->request['action'] : null);

        $this->data = __CLASS__ . 'date=' . $this->date . '|next=' . $this->next . '|smscid=' . $this->smscid . '|canal=' . $this->canal . '|Content=' . $this->content . '|SOA=' . $this->soa . '|DA=' . $this->da . '|sessionId=' . $this->sessionId;
        $this->logger = new Logger($this);
    }

    public function data($methode, $debug = true)
    {
        $to_log = 'returning ***' . $methode . '*** to the app' . PHP_EOL . $this->data . PHP_EOL . "------------------------------------";
        if ($debug)
            echo json_encode($this->data);
        else
            @file_put_contents(__DIR__. '/log/service.log', $to_log . PHP_EOL, FILE_APPEND);
        return $this->data;
    }

}
