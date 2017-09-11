<?php

use PHPUnit\Framework\TestCase;
use HtmlAnalyzer\Analyzer;

class HtmlAnalyzerTest extends TestCase {
    
    function testAnalyzer() {
        
        $htmlFile = 'to_analyze.html';
        
        $analyzer = new Analyzer;
        
        $analysis = $analyzer->analyze('<div class="foo"><img src="./puppy.png"><p><span>text</span></p></div>');
        
        $nested = $analysis->getNodesNested();
        
        $this->assertTrue($analysis->isValid());
        
        //test nested
        $this->assertEquals('div', $nested[0]['type']);
        $this->assertEquals(2, count($nested[0]['children']));
        $this->assertEquals('img', current($nested[0]['children'])['type']);
        next($nested[0]['children']);
        $this->assertEquals('p', current($nested[0]['children'])['type']);
        
        //test images
        $this->assertEquals('./puppy.png', current($analysis->getImages()));
        
    }
    
}
