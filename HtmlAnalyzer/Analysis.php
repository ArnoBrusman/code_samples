<?php

namespace HtmlAnalyzer;

class Analysis {
    
    var $isValid;
    
    protected $nodes;
    protected $errors;


    public function __construct($nodes, $errors = [])
    {
        $this->nodes = $nodes;
        $this->errors = $errors;
    }
    
    public function isValid()
    {
        return empty($this->errors);
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
    
    public function getNodes()
    {
        return $this->nodes;
    }
    
    public function countTypes()
    {
        $types = [];
        
        foreach ($this->nodes as $node) {
            if(isset($node['type'])) {
                $types[$node['type']]++;
            } else {
                $types[$node['type']] = 1;
            }
        }
        
        return $types;
    }
    
    /**
     * Get all nodes of a certain type.
     * @param string $type
     * @return array
     */
    public function getNodesOfType($type)
    {
        return array_filter($this->nodes, function($node) use ($type) {
            return $node['type'] == $type;
        });
    }
    
    /**
     * Get nodes in nested format.
     * @return type
     */
    public function getNodesNested()
    {
        $nested = [];
        
        foreach ($this->nodes as $node) {
            $child = $node;
            $nodeNested = [];
            $nodeNested[$node['id']] = $node;
            while(!is_null($parentId = $child['parent'])) {
                $parent = current(array_filter($this->nodes, function($node) use ($child) { 
                    return $node['id'] === $child['parent'];
                }));
                $loopNested = $nodeNested;
                $nodeNested = [];
                $nodeNested[$parentId]['children'] = $loopNested;
                
                $child = $parent;
            }
            
            // merge arrays recursively
            $nested = array_replace_recursive($nested,$nodeNested);
        }
        
        return $nested;
    }
    
    public function getImages()
    {
        $sources = [];
        $image_nodes = array_filter($this->nodes, function($node) {
            return $node['type'] == 'img';
        });
        foreach ($image_nodes as $node) {
            if(isset($node['attributes']['src'])) {
                $sources[] = $node['attributes']['src'];
            }
        }
        return empty($sources) ? null : $sources;
    }
    
}
