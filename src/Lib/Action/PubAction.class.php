<?php
class PubAction extends CommonAction {
    private function alert_session_once($msg, $header) {
        session("pub-once-alert", json_encode(array("msg" => $msg, "header" => $header)));
    }
    
    private function assign_alert_info($array) {
        $this->assign("once_alert", (array)$array);
    }
    
    public function __construct() {
        parent::__construct();
        
        if(session("?pub-once-alert")) {
            $json_text = session("pub-once-alert");
            $json_array = json_decode($json_text);
            
            $this->assign_alert_info($json_array);
            session("pub-once-alert", null);
        }
        
        if(!session("?douban-userinfo") || !session("?douban-token")) {
            if(strtolower(ACTION_NAME) != "login" && strtolower(ACTION_NAME) != "chklogin") {
                if(strtolower(__EXT__) !== "json") {
                    $this->alert_session_once("You must login first.", "Error");
                    $this->redirect("pub/login");
                    return;
                } else {
                    $json["status"] = 0;
                    $json["content"] = "You haven't logged in yet. Please <a href='" + U("pub/login") + "'>Log in</a> first.";
                    echo json_encode($json);
                    return;
                }
            }
        }
        
        $this->assign("userinfo", session("douban-userinfo"));
    }
    
    /**
     * 首页
     */
    public function index() {
        $this->assign("title", "Publishing Home");
        $this->display();
    }
    
    public function problem() {
        $this->assign("title", "Problem");
        
        $this->display();
    }
    
    public function edit_problem_markdown() {
        $id = $_POST["id"];
        $index = $_POST["index"];
        $md = $_POST["md"];
        
        if(!edit_problem_markdown($id, $index, $md)) {
            $json["status"] = 0;
            $json["content"] = "System error";
            echo json_encode($json);
            return;
        } else {
            $json["status"] = 1;
            $json["content"] = get_problem($id, $index);
            echo json_encode($json);
            return;
        }
    }
    
    public function edit_problem_code() {
        $id = $_POST["id"];
        $index = $_POST["index"];
        $lang = $_POST["lang"];
        $code = $_POST["code"];
        
        if(!edit_problem_code($id, $index, $code, $lang)) {
            $json["status"] = 0;
            $json["content"] = "System error";
            echo json_encode($json);
            return;
        } else {
            $json["status"] = 1;
            $json["content"] = get_problem($id, $index);
            echo json_encode($json);
            return;
        }
    }
    
    /**
     * 修改题目摘要
     * @return type
     */
    public function edit_problem_summary() {
        $id = $_POST["id"];
        $index = $_POST["index"];
        $title = $_POST["title"];
        $url = $_POST["url"];
        $tags = $_POST["tags"];
        $problem = get_problem($id, $index);
        if(false === $problem) {
            $json["status"] = 0;
            $json["content"] = "Invalid value...";
            echo json_encode($json);
            return;
        }
        
        if(!edit_problem_summary($id, $index, $title, $url, $tags)) {
            $json["status"] = 0;
            $json["content"] = "System error";
            echo json_encode($json);
            return;
        } else {
            $json["status"] = 1;
            $json["content"] = get_problem($id, $index);
            echo json_encode($json);
            return;
        }
    }
    
    /**
     * 获取比赛
     */
    public function contest() {
        $this->assign("title", "Contest");
        $this->display();
    }
    
    public function add_contest() {
        $title = trim($_POST["title"]);
        
        if($title === "") {
            $json["status"] = 0;
            $json["content"] = "You must input the contest name...";
            echo json_encode($json);
            return;
        }
        
        $id = add_contest($title);
        if(!$id) {
            $json["status"] = 0;
            $json["content"] = "System error...";
            echo json_encode($json);
            return;
        } else {
            $contest = get_contest_info($id);
            $json["status"] = 1;
            $json["content"] = $contest;
            echo json_encode($json);
            return;
        }
    }
    
    public function del_contest() {
        $id = $_GET["id"];
        $contest = get_contest_info($id);
        if(false === $contest) {
            $json["status"] = 0;
            $json["content"] = "Invalid contest id...";
            echo json_encode($json);
            return;
        }
        
        if(!delete_contest($id)) {
            $json["status"] = 0;
            $json["content"] = "System error...";
            echo json_encode($json);
            return;
        } else {
            $json["status"] = 1;
            echo json_encode($json);
            return;
        }
    }
    
    /**
     * 修改比赛内容
     */
    public function edit_content_summary() {
        $id = $_POST["id"];
        $contest = get_contest_info($id);
        if(false === $contest) {
            $json["status"] = 0;
            $json["content"] = "Invalid contest id...";
            echo json_encode($json);
            return;
        }
        
        $contest["title"] = $_POST["title"];
        $contest["url"] = $_POST["url"];
        $contest["md"] = $_POST["md"];
        $contest["time"] = strtotime($_POST["time"]);
        
        $result = set_contest_info($contest);
        if(false === $result) {
            $json["status"] = 0;
            $json["content"] = "System error.";
            echo json_encode($json);
            return;
        }
        
        $json["status"] = 1;
        echo json_encode($json);
        return;
    }
    
    /**
     * Add a problem
     * @return type
     */
    public function add_contest_problem() {
        $contestid = $_POST["id"];
        $index = trim($_POST["index"]);
        $title = trim($_POST["title"]);
        
        /**
         * Find the contest.
         */
        $contest = get_contest_info($contestid);
        if(false === $contest) {
            $json["status"] = 0;
            $json["content"] = "Invalid contest.";
            echo json_encode($json);
            return;
        }
        
        /**
         * Is the problem index valid.
         */
        if(!is_problem_index_valid($index)) {
            $json["status"] = 0;
            $json["content"] = "Invalid problem ID.";
            echo json_encode($json);
            return;
        }
        
        /**
         * Is the problem index exists.
         */
        foreach($contest["prob"] as $key => $val) {
            if(strtolower($key) === strtolower($index)) {
                $json["status"] = 0;
                $json["content"] = "This problem id already exists";
                echo json_encode($json);
                return;
            }
        }
        
        if(!add_problem($contestid, $index, $title)) {
            $json["status"] = 0;
            $json["content"] = "System error.";
            echo json_encode($json);
            return;
        }
        
        $json["status"] = 1;
        $json["content"] = array(
            "index"         => $index,
            "title"         => $title
        );
        
        echo json_encode($json);
        return;
    }
    
    public function del_contest_problem() {
        $contestid = $_POST["id"];
        $index = $_POST["index"];
        if(!delete_problem($contestid, $index)) {
            $json["status"] = 0;
            $json["content"] = "System error.";
            echo json_encode($json);
            return;
        }
        
        $json["status"] = 1;
        $json["content"]["contestid"] = $contestid;
        $json["content"]["index"] = $index;
        
        echo json_encode($json);
        return;
    }
    
    /**
     * 登录
     */
    public function login() {
        $request_url = "https://www.douban.com/service/auth2/auth/?";
        $request_url .= "client_id=" . C("DOUBAN_CLIENT_ID") . "&";
        $request_url .= "redirect_uri=" . C("BASE_HTTP") . "/pub/chklogin&";
        $request_url .= "response_type=code";
        
        $this->assign("title", "Login to Publish");
        $this->assign("request_url", $request_url);
        $this->display();
    }
    
    /**
     * 验证登录
     */
    public function chklogin() {
        if(isset($_GET["code"])) {
            session("pub-autorization-code", $_GET["code"]);
            
            echo "<div style='text-align: center; margin-top: 20px;'>获取登录信息中...</div>";
            
            $url = "https://www.douban.com/service/auth2/token";
            
            $postdata = "";
            $postdata .= "client_id=" . C("DOUBAN_CLIENT_ID") . "&";
            $postdata .= "client_secret=" . C("DOUBAN_CLIENT_SECRET") . "&";
            $postdata .= "grant_type=authorization_code&";
            $postdata .= ("code=" . $_GET["code"] . "&");
            $postdata .= "redirect_uri=" . C("BASE_HTTP") . "/pub/chklogin";
            
            $ch = curl_init();
            $opts = array(
                CURLOPT_CONNECTTIMEOUT  => 10,
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_SSL_VERIFYPEER  => false,
                CURLOPT_TIMEOUT         => 60,
                CURLOPT_USERAGENT       => 'nbutpsps',
                CURLOPT_URL             => $url,
                CURLOPT_CUSTOMREQUEST   => "POST",
                CURLOPT_POSTFIELDS      => $postdata
            );
            curl_setopt_array($ch, $opts);
            
            $json = curl_exec($ch);

            if(curl_errno($ch)) {
                $this->alert_session_once(curl_error($ch), "Douban OAuth Failure");
                curl_close($ch);
                $this->redirect("pub/login");
            } else {
                $json_array = (array)json_decode($json);
                
                if(isset($json_array["code"])) {
                    $this->alert_session_once("Error " . $json_array["code"] . ": " . $json_array["msg"], "Douban OAuth Failure");
                    curl_close($ch);
                    $this->redirect("pub/login");
                    return;
                }
                
                curl_close($ch);
                $ch = curl_init();
                $opts = array(
                    CURLOPT_CONNECTTIMEOUT  => 10,
                    CURLOPT_RETURNTRANSFER  => true,
                    CURLOPT_SSL_VERIFYPEER  => false,
                    CURLOPT_TIMEOUT         => 60,
                    CURLOPT_USERAGENT       => 'nbutpsps',
                    CURLOPT_URL             => "https://api.douban.com/v2/user/~me",
                    CURLOPT_CUSTOMREQUEST   => "GET",
                    CURLOPT_HTTPHEADER      => array(
                        "Authorization: Bearer " . $json_array["access_token"]
                    )
                );
                curl_setopt_array($ch, $opts);
                $userinfo = curl_exec($ch);
                
                if(curl_errno($ch)) {
                    $this->alert_session_once(curl_error($ch), "Douban OAuth Failure");
                    curl_close($ch);
                    $this->redirect("pub/login");
                } else {
                    $user_array = (array)json_decode($userinfo);
                    if(isset($user_array["code"])) {
                        $this->alert_session_once("Error " . $user_array["code"] . ": " . $user_array["msg"], "Douban OAuth Failure");
                        curl_close($ch);
                        $this->redirect("pub/login");
                        return;
                    }
                    
                    /**
                     * 判断是否在管理列表中
                     */
                    curl_close($ch);
                    $list = C("ADMIN");
                    $list_size = count($list);
                    for($i = 0; $i < $list_size; $i++) {
                        if(strtolower($list[$i]) === strtolower($user_array["uid"])) {
                            session("douban-userinfo", $user_array);
                            session("douban-token", $json_array);
                            $this->redirect("pub/index");
                            return;
                        }
                    }

                    $this->alert_session_once("This user has no manager permission.", "PSPS Login Failure");
                    $this->redirect("pub/login");
                }
            }
        } else {
            $this->alert_session_once($_GET["error"], "Douban OAuth Failure");
            $this->redirect("pub/login");
        }
    }
}
