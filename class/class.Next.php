<?php


class Next
{
    var $next = null;
    var $id_consultation = null;
    var $page = null;
    var $data = null;
    

   

    public function __construct($info = null)
    {
        if ($info != null) {
            $this->next = isset($info['next']) ? $info['next'] : null;
            $this->page = isset($info['page']) ? $info['page'] : null;
            $this->data = isset($info['data']) ? $info['data'] : null;
            $this->id_consultation = isset($info['id_consultation']) ? $info['id_consultation'] : null;
           
        }
    }
}
