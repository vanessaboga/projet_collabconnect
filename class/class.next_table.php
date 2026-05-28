<?php


class next_table {
    var $id=null;
    var $next=null;
    var $date=null;
    var $numero=null;
    var $diff=null;
    var $url=null;
    var $sessionId;
    var $next_sms;
    var $date_sms;
    //si on appel une url exterieur;
    var $next_ext;
    var $contenu_ext;
    var $freeFlow_ext;
    var $diff_sms;
    public function __construct($info=null){
        if($info!=null){
            $this->id=isset($info['id'])?$info['id']:null;
            $this->next=isset($info['next'])?$info['next']:null;
            $this->next_ext=isset($info['next_ext'])?$info['next_ext']:null;
            $this->date=isset($info['date'])?$info['date']:null;
            $this->numero=isset($info['numero'])?$info['numero']:null;
            $this->diff=isset($info['diff'])?$info['diff']:null;
            $this->url=isset($info['url'])?$info['url']:null;
            $this->sessionId=isset($info['sessionId'])?$info['sessionId']:null;
            $this->date_sms=isset($info['date_sms'])?$info['date_sms']:null;
            $this->next_sms=isset($info['next_sms'])?$info['next_sms']:null;
            $this->diff_sms=isset($info['diff_sms'])?$info['diff_sms']:null;
        }
    }
} 