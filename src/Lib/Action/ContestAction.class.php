<?php
class ContestAction extends CommonAction {
    public function __construct() {
        parent::__construct();
    }
    
    public function view() {
        $id = $_GET["_URL_"][2];
        if(!is_numeric($id)) {
            $this->redirect("contest/list");
        }
        
        $contest = get_contest_info($id);
        if($contest === false) {
            $this->assign("contest", null);
        } else {
            $contest["html"] = get_contest_summary_html($id);
            
            /** Get the problems */
            foreach($contest["prob"] as $key => $val) {
                $contest["prob"][$key] = get_problem($id, $key);
                $contest["prob"][$key]["html"] = get_problem_summary_html($id, $key);
                $contest["prob"][$key]["tags"] = explode(",", $contest["prob"][$key]["tags"]);
                $tagcount = count($contest["prob"][$key]["tags"]);
                for($i = 0; $i < $tagcount; $i++) {
                    $contest["prob"][$key]["tags"][$i] = trim($contest["prob"][$key]["tags"][$i]);
                }
                if($tagcount === 1 && $contest["prob"][$key]["tags"][0] === "") {
                    $contest["prob"][$key]["tags"][0] = "Not set yet";
                }
            }
            
            $this->assign("contest", $contest);
        }
        
        $this->assign("title", $contest["title"]);
        $this->display();
    }
    
    public function code() {
        $id = $_GET["_URL_"][2];
        $index = $_GET["_URL_"][3];
        $lang = $_GET["_URL_"][4];
        
        $problem = get_problem($id, $index);
        if(false === $problem) {
            $this->assign("code", "Invalid code page.");
        } else {
            $this->assign("problem", $problem);
            $code = get_problem_code($id, $index, $lang);
            if($lang === "gcc") $lang = "C";
            if($lang === "gpp") $lang = "C++";
            if($lang === "fpc") $lang = "Pascal";
            $this->assign("lang", $lang);
            if(false === $code) {
                $this->assign("code", "Invalid code page.");
            } else {
                $this->assign("code", $code);
            }
        }
        
        $this->display();
    }
    
    public function markdown() {
        $id = $_GET["_URL_"][2];
        $index = $_GET["_URL_"][3];
        
        $problem = get_problem($id, $index);
        if(false === $problem) {
            $this->assign("md", "Invalid problem.");
        } else {
            $this->assign("problem", $problem);
            $this->assign("md", get_problem_markdown($id, $index));
        }
        
        $this->display();
    }
    
    public function page() {
        $perpage = 1;
        
        $contest_list = get_contest_list();
        $contest_count = count($contest_list);
        $totalpage = (int)((int)($contest_count) / (int)($perpage));
        if($contest_count % $perpage) $contest_count++;
        if($totalpage === 0) $totalpage = 1;
        
        $page = $_GET["_URL_"][2];
        if($page === "") $page = 1;
        if($page <= 0 || $page > $totalpage) redirect(C("WEB_ROOT") . "/contest/page/1");
        
        krsort($contest_list);
        $i = 0;
        $needi = ($page - 1) * $perpage;
        $array = array();
        foreach($contest_list as $key => $val) {
            if($i >= $needi) {
                if($i - $needi < $perpage) {
                    $array[$key] = get_contest_info($key);
                } else {
                    break;
                }
            }
            
            $i++;
        }
        
        import("@.Plugin.XPage");
        $pager = new XPage();
        $pager->item_count = $contest_count;
        $pager->per_page = $perpage;
        $pager->link_str = C("WEB_ROOT") . "/contest/page/%d";
        $pager->first_page = "First";
        $pager->last_page = "Last";
        $pager->prev_page = "Prev";
        $pager->next_page = "Next";
        $pager->a_class = "badge badge-important";
        $pager->cur_class = "badge badge-inverse";
        $pager->no_link_class = "badge";
        $pager->cur_page = $page;
        $str = $pager->create_links();
        
        $this->assign("list", $array);
        $this->assign("title", "Page " . $page . " - Contest List");
        $this->assign("pager", $str);
        
        $this->display();
    }
}
