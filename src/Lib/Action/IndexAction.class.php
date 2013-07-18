<?php
class IndexAction extends CommonAction {
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        $lastestid = get_max_contest_id();
        if(false === $lastestid) {
            $this->assign("lastest", null);
        } else {
            $contest = get_contest_info($lastestid);
            if($contest === false) {
                $this->assign("lastest", null);
            } else {
                $this->assign("lastest", $contest);
            }
        }
        
        $this->assign("title", "Index");
        $this->display();
    }
}
