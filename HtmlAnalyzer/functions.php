<?php

function analyze_html($html) :HtmlAnalyzer\Analysis {
    
    $analyzer = new HtmlAnalyzer\Analyzer;
    
    return $analyzer->analyze($html);
}
