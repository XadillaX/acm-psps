<?php
class CommonAction extends Action {
    public function __construct() {
        parent::__construct();
        $this->assign("title_suffix", "NBUT ACM Problem Solution Publishing System");
        $this->assign("webroot", C("WEB_ROOT"));
    }
}
