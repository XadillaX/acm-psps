<?php
return array(
    /** URI大小写不敏感 */
    "URL_CASE_INSENSITIVE"          => true,
    
    /** 默认主题 */
    "DEFAULT_THEME"                 => "default",
    
    /** 网站根目录：相对路径 */
    "WEB_ROOT"                      => "/",
    
    /** 网站根目录：绝对路径 */
    "BASE_HTTP"                     => "http://" . $_SERVER["HTTP_HOST"] . "/",
    
    /** 扩展配置文件 */
    "LOAD_EXT_CONFIG"               => "admin",
    
    /** 扩展函数文件 */
    "LOAD_EXT_FILE"                 => "utils",
    
    /** URL模式：重写 */
    "URL_MODEL"                     => 2,
    
    /** MARKDOWN路径 */
    "MARKDOWN_PATH"                 => "THE ABOSOLUTE PATH OF [./markdown]",
    
    "URL_HTML_SUFFIX"               => "html|json",
    
    "SHOW_PAGE_TRACE"               => isset($_GET["debug"]) ? true : false,
    
    "CODE_LANG"                     => array(
        "gcc"                       => ".c",
        "gpp"                       => ".cpp",
        "fpc"                       => ".pas"
    ),
    
    "DOUBAN_CLIENT_ID"              => "YOUR DOUBAN CLIENT ID",
    "DOUBAN_CLIENT_SECRET"          => "YOUR DOUBAN CLIENT SECRET"
);
