<?php
class PubjsonAction extends CommonAction {
    public function __construct() {
        parent::__construct();
        
        if(!session("?douban-userinfo") || !session("?douban-token")) {
            $json["status"] = 0;
            $json["content"] = "You must login first.";
            die(json_encode($json));
        }
    }
    
    public function contest_list() {
        $list = get_contest_list();
        
        $json["status"] = 1;
        $json["content"] = $list;
        
        echo json_encode($json);
    }
    
    public function contest_info() {
        $id = $_GET["id"];
        $list = get_contest_list();
        
        $in = false;
        foreach($list as $key => $val) {
            if($key == $id) {
                $in = true;
                break;
            }
        }
        
        /**
         * 不存在的话
         */
        if(!$in) {
            $json["status"] = 0;
            $json["content"] = "Invalid ID.";
            echo json_encode($json);
            return;
        }
        
        $array = get_contest_info($id);
        if(false === $array) {
            $json["status"] = 0;
            $json["content"] = "Broken contest information.";
            echo json_encode($json);
            return;
        }
        
        $json["status"] = 1;
        $json["content"] = $array;
        echo json_encode($json);
    }
    
    public function problem_info() {
        $id = $_GET["id"];
        $index = $_GET["index"];
        
        $problem = get_problem($id, $index);
        if(false === $problem) {
            $json["status"] = 0;
            $json["content"] = "Invalid value.";
            echo json_encode($json);
            return;
        }
        
        $json["status"] = 1;
        $json["content"] = $problem;
        
        echo json_encode($json);
        return;
    }
}
