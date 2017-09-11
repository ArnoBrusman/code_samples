<?php

namespace HtmlAnalyzer;

/**
 * An Html analyzer.
 */
class Analyzer {
    
    protected $nodes;

    protected $analysis;
    protected $errors = [];
    
    // list taken from http://w3c.github.io/html/syntax.html#void-elements
    protected $void_elements = ['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr'];
            
    /**
     * Analyze a given html string. Return a html Analysis object
     * @param string $html
     * @return \HtmlAnalyzer\Analysis
     */
    function analyze($html) : Analysis {
        $this->errors = [];
        
        $this->nodes = $this->getNodes($html);
        
        $this->analysis = new Analysis($this->nodes, $this->errors);
        
        return $this->analysis;
    }
    
    protected function getNodes($html) {
        $matches = [];
        $nodes = [];
        $level = 0;
        $id = 0;
        $active_nodes = [];
        $_match = '';
        
        // get node data from html
        preg_match_all('~</?[^>]+>~', $html, $matches);
        
        foreach ($matches[0] as $key => $match) {
            //get node name
            preg_match('~^</?([^ \n>]+)~', $match, $_match);
            $node_type = $_match[1];
            
            // go down a level if current node is closed
            if (isset($active_nodes[$level-1]) && $match === '</'.$active_nodes[$level-1]['type'].'>') {
                unset($active_nodes[$level-1]);
                $level--;
            } elseif (preg_match('~^</~', $match)) {
                echo $match;
                $this->errors[] = 'wrong node closed';
            } else {
                $node = [
                    'id' => $id,
                    'type' => $node_type,
                    'level' => $level,
                    'attributes' => $this->getAttributes($match),
                    'parent' => isset($active_nodes[$level-1]) ? $active_nodes[$level-1]['id'] : null,
                ];
                $nodes[$id] = $node;
                $id++;
                
                if(!$this->isVoidElement($node_type)) {
                    $active_nodes[$level] = $node;
                    $level++;
                }
            }
        }
        return $nodes;
    }
    
    protected function isVoidElement($node)
    {
        return is_int(array_search($node, $this->void_elements));
    }
    
    protected function getAttributes($node)
    {
        $attributes = [];
        $matches = [];
        $nr_matches = preg_match_all('~[ \n](?![/>])([^ =]+)(\=[\'"](.*)[\'"]|\=([^ \>]+))?~', $node, $matches);
        
        if($nr_matches > 0) {
            foreach ($matches[0] as $key => $attrStr) {
                $attributes[$matches[1][$key]] = !empty($matches[3][$key]) ? $matches[3][$key] : $matches[4][$key];
            }
        }
        return $attributes;
    }
    
}
