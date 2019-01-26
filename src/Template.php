<?php

namespace Erebox\SimpleTemplate;

class Template
{
    private $imports = [];
    private $tags = [];
    private $content;
    private $filename;

    /* ******************************************************
     *  P U B L I C  methods
    ** *****************************************************/
    
    public function __construct($tplFile) {
        $this->filename = $tplFile;
        $this->content = $this->load($this->filename);
        if(!$this->content) {
            return 'Error loading '.$this->filename;
        }
    }

    public function tag($tag) {
        $this->tags = $tag;
    }

    public function render() {
        //manage import
        $this->parseImport();
        $this->parseDirective();
        $this->parseTag();
        echo $this->content;
    }

    /* ******************************************************
     *  P R I V A T E  methods
    ** *****************************************************/

    private function load($file) {
        if(file_exists($file)) {
            return file_get_contents($file);
        }
        return false;
    }


    private function getTags() {
        preg_match_all('~\{\{([^{]*)\}\}~i', $this->content, $match);
        if ($match[1] && count($match[1])>0) {
            return $match[1];
        }
        return false;
    }

    private function parseImport() {
        $all = $this->getTags();
        foreach ($all as $curr_tag) {
            if (strpos($curr_tag, "@import")!== false ) {
                $curr_tag_piece = explode(" ",$curr_tag,2);
                if ($curr_tag_piece[1]) {
                    $impTpl = $this->load($curr_tag_piece[1]);
                    if ($impTpl) {
                        $this->content = str_replace('{{'.$curr_tag.'}}', $impTpl, $this->content);
                    }        
                }
            }
        }
    }

    private function getLoop($inipar) {
        $end_par = '{{'.strtr($inipar,'#','/').'}}';
        $ini_par = '{{'.$inipar.'}}';
        $pos1 = strpos($this->content, $ini_par);
        $pos1a = $pos1 + strlen($ini_par);
        $pos2 = strpos($this->content, $end_par);
        $pos2a = $pos2 + strlen($end_par);
        $tpl = substr($this->content, $pos1a, $pos2 - $pos1a);
        $tpl_all = substr($this->content, $pos1, $pos2a - $pos1);


        $arr_tag = $this->tags[ltrim($inipar, '#')];

        $render = "";
        foreach($arr_tag as $curr_arr_tag) {
            $curr_render = $tpl;
            foreach($curr_arr_tag as $k => $v) {
                $curr_render = str_replace('{{'.$k.'}}', $v, $curr_render);
            }
            $render .= $curr_render;
        }
        #echo "<pre>".print_r($ini_par.$tpl.$end_par, 1)."</pre><hr/>\n";
        #echo "<pre>".print_r($tpl_all, 1)."</pre><hr/>\n";
        #echo "<pre>".print_r($render, 1)."</pre><hr/>\n";
        return [$tpl_all, $render];
    }

    private function parseDirective() {
        $all = $this->getTags();
        $list_loop = [];
        // get all directive
        foreach ($all as $curr_tag) {
            if (strpos($curr_tag[0], '#')!== false ) {
                $list_loop[] = $curr_tag;
            }
        }
        // manage loop
        foreach ($list_loop as $curr_loop) {
            list($loop_tpl, $loop_val) = $this->getLoop($curr_loop);
            $this->content = str_replace($loop_tpl, $loop_val, $this->content);
        }
    }

    private function parseTag() {
        foreach ($this->tags as $key => $val) {
            if (is_string($val)) {
                $this->content = str_replace('{{'.$key.'}}', $val, $this->content);
            }
        }
    }

}