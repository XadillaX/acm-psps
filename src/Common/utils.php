<?php
/**
 +-----------------------------------------------------------------------------------------
 * 删除目录及目录下所有文件或删除指定文件
 +-----------------------------------------------------------------------------------------
 * @param str $path   待删除目录路径
 * @param int $delDir 是否删除目录，1或true删除目录，0或false则只删除文件保留目录（包含子目录）
 +-----------------------------------------------------------------------------------------
 * @return bool 返回删除状态
 +-----------------------------------------------------------------------------------------
 */
function delDirAndFile($path, $delDir = false) {
    if ($handle = opendir("$path")) {
        while (false !== ( $item = readdir($handle) )) {
            if ($item != "." && $item != "..")
                is_dir("$path/$item") ? delDirAndFile("$path/$item", $delDir) : unlink("$path/$item");
        }
        closedir($handle);

        if ($delDir)
            return rmdir($path);
    }else {
        return unlink($path);
    }
}

function format_php_str($str) {
    $str = str_replace('"', '\\"', $str);
    $str = str_replace("\n", "\\\n", $str);
    $str = str_replace("'", "'", $str);
    
    return $str;
}

/**
 * Compile the markdown file to html file.
 * @param string $input
 * @param string $output
 * @param string $basepath
 */
function compile_pandoc($input, $output, $basepath = "") {
    if($basepath !== "") {
        $blen = strlen($basepath);
        if($basepath[$blen - 1] !== '/' && $basepath[$blen - 1] !== '\\') {
            $basepath .= '/';
        }
    }
    
    $input = $basepath . $input;
    $output = $basepath . $output;
    
    $cmd = "pandoc \"" . format_php_str($input) . "\" --mathjax -o \"" . format_php_str($output) . "\"";
    exec($cmd);
}

/**
 * Get the contest list.
 * @return array
 */
function get_contest_list() {
    $path = C("MARKDOWN_PATH");
    
    require $path . "/list.php";
    
    return $list;
}

/**
 * Get the contest info.
 * @param int $id
 * @return boolean|array
 */
function get_contest_info($id) {
    if(!is_numeric($id)) return false;
    
    $path = C("MARKDOWN_PATH");
    $filename = "{$path}/{$id}/summary.php";

    if(!file_exists($filename)) {
        $array = array(
            "id"        => $id,
            "title"     => "No name",
            "url"       => "http://acm.nbut.cn/",
            "time"      => time(),
            "md"        => ""
        );
        
        if(!set_contest_info($array)) return false;
    }
    
    require $filename;
    
    $md = "{$path}/{$id}/summary.md";
    if(!file_exists($md)) {
        $array["md"] = "";
    } else {
        $array["md"] = file_get_contents($md);
    }
    
    $array["time1"] = date("jS M, Y", $array["time"]);
    $array["time2"] = date("m/d/Y", $array["time"]);
    $array["id"] = $id;
    
    return $array;
}

/**
 * Write contest info to the file system.
 * @param array $array
 * @return boolean
 */
function set_contest_info($array) {
    if(!update_contest_list($array["id"], $array["title"])) return false;
    
    $path = C("MARKDOWN_PATH") . "/{$array['id']}";
    if(!file_exists($path)) {
        if(mkdir($path) === false) return false;
    }
    
    $array["title"] = format_php_str($array["title"]);
    $array["url"] = format_php_str($array["url"]);
    
    $str = "<?php\n";
    $str .= "\$array = array(\n";
    $str .= ("    \"title\"     => \"" . $array["title"] . "\",\n");
    $str .= ("    \"url\"       => \"" . $array["url"] . "\",\n");
    $str .= ("    \"time\"      => " . $array["time"] . ",\n");
    $str .= ("    \"prob\"      => array(\n");
    
    foreach($array["prob"] as $key => $val) {
        $str .= ("        \"" . format_php_str($key) . "\" => \"" . $val . "\",\n");
    }
    
    $str .= "    )\n";
    $str .= ");\n";
    
    $filename = $path . "/summary.php";
    $fp = fopen($filename, "w+");
    if(false === $fp) return false;
    
    fwrite($fp, $str);
    fclose($fp);
    
    $filename = $path . "/summary.md";
    $fp = fopen($filename, "w+");
    if(false === $fp) return false;
    
    fwrite($fp, $array["md"]);
    fclose($fp);
    
    $html_filename = $path . "/summary.html";
    
    //exec("pandoc \"" . format_php_str($filename) . "\" -o \"" . format_php_str($html_filename) . "\"");
    compile_pandoc($filename, $html_filename);
    
    return true;
}

/**
 * 
 * @param int $id
 * @param string $name
 * @param int $type 0: update 1: del 2: add
 * @return boolean
 */
function update_contest_list($id, $name, $type = 0) {
    $list = get_contest_list();
    
    $filename = C("MARKDOWN_PATH") . "/list.php";
    $fp = fopen($filename, "w+");
    if(false === $fp) return false;
    
    fwrite($fp, "<?php\n");
    fwrite($fp, "/**\n");
    fwrite($fp, " * Generated by NBUT ACM PSPS\n");
    fwrite($fp, " * @date: " . date("jS M, Y", time()) . "\n");
    fwrite($fp, " */\n");
    fwrite($fp, "\$list = array(\n");
    
    
    if($type === 2) {
        $maxk = 0;
        foreach($list as $key => $val) {
            $maxk = max($maxk, $key);
        }
        
        $maxk++;
        $list[$maxk] = $name;
        
        ksort($list);
    }
    
    foreach($list as $key => $val) {
        if($key == $id) $val = $name;
        if($key == $id && $type === 1) continue;
        fwrite($fp, "    \"" . $key . "\" => \"" . format_php_str($val) . "\",\n");
    }
    
    fwrite($fp, ");\n");
    
    fclose($fp);
    
    return true;
}

function add_contest($name) {
    update_contest_list("", $name, 2);
    $list = get_contest_list();
    
    $maxk = 0;
    foreach($list as $key => $val) {
        $maxk = max($key, $maxk);
    }
    
    $id = $maxk;
    $path = C("MARKDOWN_PATH") . "/{$id}";
    
    mkdir($path);
    
    set_contest_info(array("title" => $name, "id" => $id, "time" => time()));
    
    return $id;
}

function delete_contest($id) {
    $contest = get_contest_info($id);
    if(false === $contest) return false;
    
    if(!update_contest_list($id, "", 1)) return false;
    
    delDirAndFile(C("MARKDOWN_PATH") . "/{$id}/", true);
    return true;
}

function is_problem_index_valid($index) {
    $len = strlen($index);
    for($i = 0; $i < $len; $i++) {
        $ch = $index[$i];
        if($ch >= 'a' && $ch <= 'z') continue;
        if($ch >= 'A' && $ch <= 'Z') continue;
        if($ch >= '0' && $ch <= '9') continue;
        if($ch === '-') continue;
        if($ch === '_') continue;
        return false;
    }
    
    return true;
}

function delete_problem($id, $index) {
    $path = C("MARKDOWN_PATH");
    $contest = get_contest_info($id);
    if($contest === false) return false;
    if(!isset($contest["prob"][$index])) return false;
    
    $path .= "/{$id}/{$index}";
    unset($contest["prob"][$index]);
    if(!set_contest_info($contest)) return false;
    
    delDirAndFile($path, true);
    
    return true;
}

/**
 * Write the problem summary
 * @param int $id
 * @param string $index
 * @param string $title
 * @param string $url
 * @param string $tags
 * @return boolean
 */
function write_problem_summary($id, $index, $title, $url, $tags) {
    $path = C("MARKDOWN_PATH") . "/{$id}/{$index}/";
    mkdir($path);
    
    $fp = fopen($path . "/summary.php", "w+");
    if($fp === false) return false;
    fwrite($fp, "<?php\n");
    fwrite($fp, "\$array = array(\n");
    fwrite($fp, "    \"title\"    => \"" . format_php_str($title) . "\",\n");
    fwrite($fp, "    \"index\"    => \"" . format_php_str($index) . "\",\n");
    fwrite($fp, "    \"tags\"     => \"" . format_php_str($tags) . "\",\n");
    fwrite($fp, "    \"url\"      => \"" . format_php_str($url) . "\"\n");
    fwrite($fp, ");\n");
    fclose($fp);
    
    return true;
}

function write_problem_markdown($id, $index, $md) {
    $path = C("MARKDOWN_PATH") . "/{$id}/{$index}/";
    mkdir($path);
    
    $fp = fopen($path . "/summary.md", "w+");
    if($fp === false) return false;
    
    fwrite($fp, $md);
    fclose($fp);
    
    compile_pandoc($path . "/summary.md", $path . "/summary.html");
    
    return true;
}

function write_problem_code($id, $index, $code, $lang) {
    $lang_arr = C("CODE_LANG");
    if(!isset($lang_arr[$lang])) return false;
    
    $filename = C("MARKDOWN_PATH") . "/{$id}/{$index}/code{$lang_arr[$lang]}";
    mkdir(C("MARKDOWN_PATH") . "/{$id}/{$index}/");
    
    $fp = fopen($filename, "w+");
    if($fp === false) return false;
    
    fwrite($fp, $code);
    fclose($fp);
    
    return true;
}

/**
 * Add a problem to a contest
 * @param int $id
 * @param string $index
 * @param string $title
 * @return boolean
 */
function add_problem($id, $index, $title) {
    $contest = get_contest_info($id);
    if(false === $contest) return false;
    
    /**
     * mkdir
     */
    $path = C("MARKDOWN_PATH");
    $path .= "/{$id}";
    //if(!mkdir($path . "/{$index}")) return false;
    mkdir($path . "/{$index}");
    
    /**
     * create the file.
     */
    if(!write_problem_summary($id, $index, $title, "", "")) return false;
    
    $fp = fopen($path . "/summary.md", "w+");
    fclose($fp);
    
    write_problem_markdown($id, $index, "");
    
    compile_pandoc($path . "/summary.md", $path . "/summary.html");
    
    /**
     * Update the contest file.
     */
    $probs = $contest["prob"];
    $probs[$index] = $title;
    ksort($probs);
    $contest["prob"] = $probs;
    
    return set_contest_info($contest);
}

function edit_problem_summary($id, $index, $title, $url, $tags) {
    $contest = get_contest_info($id);
    if(false === $contest) return false;
    
    $problem = get_problem($id, $index);
    if(false === $problem) return false;
    
    if(!write_problem_summary($id, $index, $title, $url, $tags)) {
        return false;
    }
    
    $contest["prob"][$index] = $title;
    return set_contest_info($contest);
}

function edit_problem_markdown($id, $index, $md) {
    $problem = get_problem($id, $index);
    if(false === $problem) return false;
    
    if(!write_problem_markdown($id, $index, $md)) return false;
    
    return true;
}

function edit_problem_code($id, $index, $code, $lang) {
    $problem = get_problem($id, $index);
    if(false === $problem) return false;
    
    return write_problem_code($id, $index, $code, $lang);
}

function get_problem($id, $index) {
    $contest = get_contest_info($id);
    if(false === $contest) return false;
    
    /**
     * No such problem.
     */
    $ex = false;
    foreach($contest["prob"] as $key => $val) {
        if($key === $index) {
            $ex = true;
            break;
        }
    }
    if(!$ex) return false;
    
    $path = C("MARKDOWN_PATH");
    $path .= "/{$id}/{$index}";
    
    if(!file_exists($path . "/summary.php")) {
        add_problem($id, $index, "No name");
    }
    
    require $path . "/summary.php";
    $problem = $array;
    
    $problem["md"] = file_get_contents($path . "/summary.md");
    if(false === $problem["md"]) $problem["md"] = "";
    $problem["code"] = array();
    
    foreach(C("CODE_LANG") as $lang => $ext) {
        $filename = $path . "/code{$ext}";
        if(!file_exists($filename)) continue;
        $problem["code"][$lang] = file_get_contents($filename);
        if(trim($problem["code"][$lang]) === "") unset($problem["code"][$lang]);
    }
    
    return $problem;
}

////////////////////////////////////////////////////////
// Featured
////////////////////////////////////////////////////////
/**
 * Get the max contest id in the list.
 * @return int|boolean
 */
function get_max_contest_id() {
    $list = get_contest_list();
    $mx = 0;
    foreach($list as $key => $val) {
        $mx = max($mx, $key);
    }
    
    if(0 === $mx) return false;
    return $mx;
}

function get_contest_summary_html($id) {
    if(!is_numeric($id)) return "";
    
    $html = file_get_contents(C("MARKDOWN_PATH") . "/{$id}/summary.html");
    if(null === $html) return "";
    
    return $html;
}

function get_problem_summary_html($id, $index) {
    if(!is_numeric($id)) return "";
    if(!is_problem_index_valid($index)) return "";
    
    $html = file_get_contents(C("MARKDOWN_PATH") . "/{$id}/{$index}/summary.html");
    if(null === $html) return "";
    
    return $html;
}

/**
 * Get code.
 * @param int $id
 * @param string $index
 * @param string $lang
 * @return boolean|string
 */
function get_problem_code($id, $index, $lang) {
    $langs = C("CODE_LANG");
    if(!is_numeric($id)) return false;
    if(!is_problem_index_valid($index)) return false;
    
    $final_ext = "";
    foreach($langs as $name => $ext) {
        if($lang === $name) $final_ext = $ext;
    }
    
    if($final_ext === "") return false;
    
    $filename = C("MARKDOWN_PATH") . "/{$id}/{$index}/code{$final_ext}";
    $code = file_get_contents($filename);
    
    if(null === $code || trim($code) === "") return false;
    return $code;
}

function get_problem_markdown($id, $index) {
    if(!is_numeric($id)) return false;
    if(!is_problem_index_valid($index)) return false;
    
    $problem = get_problem($id, $index);
    if($problem === false) return false;
    
    $filename = C("MARKDOWN_PATH") . "/{$id}/{$index}/summary.md";
    $md = file_get_contents($filename);
    
    if(null === $md) return "";
    return $md;
}