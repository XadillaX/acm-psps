<?php
/**
 * @filename    xpage.php
 * @version    0.0.1
 * @author     Kaidi Zhu
 * @contact     kaidi@clic2c.com
 * @update      22-08-2011
 * @comment     Page library. [Library of the aquaMoblie&CLIC2C CMS]
 */

if(!defined('THINK_PATH')) exit('No direct script access allowed');

class XPage
{
    public $first_page = "首页";
    public $last_page = "尾页";
    public $prev_page = "上一页";
    public $next_page = "下一页";
    public $class = "xpage";
    public $id = "";
    public $a_class = "page_a";
    public $cur_class = "page_cur";
    public $no_link_class = "page_no_link";

    public $rim_count = 3;
    public $center_count = 5;

    public $item_count = 0;
    public $per_page = 1;
    public $cur_page = 1;
    public $tot_page = 0;

    public $link_str = "";

    /**
     * Constructure.
     */
    public function CI_Xpage()
    {
        
    }

    /**
     * Set configuration.
     *
     * @param array $config
     */
    public function set_config($config)
    {
        foreach($config as $key => $value)
        {
            if(isset($this->$key))
            {
                $this->$key = $value;
            }
        }
    }

    /**
     * Get page url by page number
     *
     * @param int $page The number of page
     * @return string The url
     */
    public function get_page_url($page)
    {
        return sprintf($this->link_str, $page);
    }

    /**
     * Get page html
     *
     * @param int $page The number of page.
     * @param int $cur Current page.
     * @return string The whole html of each link.
     */
    public function get_page_html($page, $cur)
    {
        $str = "";
        if($page == $cur)
        {
            $str = "<span";
            if($this->cur_class != "") $str .= " class=\"{$this->cur_class}\"";
            $str .= ">{$page}</span>";
        }
        else
        {
            $str = "<a title=\"{$page}\"";
            if($this->a_class != "") $str .= " class=\"{$this->a_class}\"";
            $str .= " href=\"{$this->get_page_url($page)}\">$page</a>";
        }

        return $str;
    }

    /**
     * Create links
     *
     * @return string links.
     */
    public function create_links()
    {
        /** Total page */
        if($this->per_page <= 0) return false;
        $this->tot_page = (int)((int)$this->item_count / (int)$this->per_page);
        if($this->item_count % $this->per_page != 0) $this->tot_page++;
        if($this->tot_page == 0) $this->tot_page = 1;

        /** Abour current page */
        if($this->cur_page > $this->tot_page) return false;
        if($this->center_count % 2 == 0) $this->center_count--;
        if($this->center_count <= 0) $this->center_count = 1;

        /** Set links */
        $links = $this->__begining_of_wrapper();
        $links .= $this->__page_left();
        $links .= $this->__links();
        $links .= $this->__page_right();
        $links .= $this->__ending_of_wrapper();

        return $links;
    }

    /**
     * Get current page. (call after create_links)
     *
     * @return int Current page.
     */
    public function get_cur_page()
    {
        return $this->cur_page;
    }

    /**
     * Get total page. (call after create_links)
     *
     * @return int Total page.
     */
    public function get_tot_page()
    {
        return $this->tot_page;
    }

    /**
     * Get item count. (call after create_links)
     * 
     * @return int Item count;
     */
    public function get_count()
    {
        return $this->item_count;
    }

    private function __links()
    {
        $str = "";

        /** LEFT */
        $left = $this->rim_count;
        if($this->tot_page <= $left) $left = $this->tot_page;
        for($i = 1; $i <= $left; $i++)
        {
            $str .= $this->get_page_html($i, $this->cur_page);
            $str .= " ";
        }
        if($left == $this->tot_page) return $str;

        /** CENTER */
        $middle_left = $this->cur_page - (int)((int)$this->center_count / (int)2);
        $middle_right = $this->cur_page + (int)((int)$this->center_count / (int)2);
        if($middle_left <= $left) $middle_left = $left + 1;
        if($middle_left != $left + 1) $str .= " ... ";
        if($middle_right > $this->tot_page) $middle_right = $this->tot_page;
        for($i = $middle_left; $i <= $middle_right; $i++)
        {
            $str .= $this->get_page_html($i, $this->cur_page);
            $str .= " ";
        }
        if($middle_right == $this->tot_page) return $str;

        /** RIGHT */
        $right = $this->tot_page - $this->rim_count + 1;
        if($right <= $middle_right) $right = $middle_right + 1;
        if($middle_right + 1 != $right) $str .= " ... ";
        for($i = $right; $i <= $this->tot_page; $i++)
        {
            $str .= $this->get_page_html($i, $this->cur_page);
            $str .= " ";
        }

        return $str;
    }

    private function __page_left()
    {
        $str = "";
        if($this->cur_page == 1)
        {
            $str .= "<span";
            if($this->no_link_class != "") $str .= " class=\"$this->no_link_class\"";
            $str .= ">$this->first_page</span> ";

            $str .= "<span";
            if($this->no_link_class != "") $str .= " class=\"$this->no_link_class\"";
            $str .= ">$this->prev_page</span> ";
        }
        else
        {
            $str .= "<a title=\"$this->first_page\" href=\"{$this->get_page_url(1)}\"";
            if($this->a_class != "") $str .= " class=\"$this->a_class\"";
            $str .= ">$this->first_page</a> ";

            $str .= "<a title=\"$this->prev_page\" href=\"{$this->get_page_url($this->cur_page - 1)}\"";
            if($this->a_class != "") $str .= " class=\"$this->a_class\"";
            $str .= ">$this->prev_page</a> ";
        }

        return $str;
    }

    private function __page_right()
    {
        $str = "";
        if($this->cur_page == $this->tot_page)
        {
            $str .= "<span";
            if($this->no_link_class != "") $str .= " class=\"$this->no_link_class\"";
            $str .= ">$this->next_page</span> ";

            $str .= "<span";
            if($this->no_link_class != "") $str .= " class=\"$this->no_link_class\"";
            $str .= ">$this->last_page</span> ";
        }
        else
        {
            $str .= "<a title=\"$this->next_page\" href=\"{$this->get_page_url($this->cur_page + 1)}\"";
            if($this->a_class != "") $str .= " class=\"$this->a_class\"";
            $str .= ">$this->next_page</a> ";

            $str .= "<a title=\"$this->last_page\" href=\"{$this->get_page_url($this->tot_page)}\"";
            if($this->a_class != "") $str .= " class=\"$this->a_class\"";
            $str .= ">$this->last_page</a> ";
        }

        return $str;
    }

    private function __begining_of_wrapper()
    {
        $str = "<div";
        if($this->id != "") $str .= " id=\"{$this->id}\"";
        if($this->class != "") $str .= " class=\"{$this->class}\"";
        $str .= ">";

        return $str;
    }

    private function __ending_of_wrapper()
    {
        return "</div>";
    }
}

/* End of file xpage.php */
/* Location: ./application/libraries/xpage.php */