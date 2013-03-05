<?php
/**
 * This file is part of LightFM web file manager.
 * 
 * Copyright (c) 2013 Jan Tulak (http://tulak.me)
 * 
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */


namespace LightFM;

/**
 * 
 * @property-read object $Highlighter Syntax highlighter
 */
 class TextFile extends File implements IFile{
     
     
     // overwriting parent's value
    protected $iconName = 'file-text';
     
     // overwriting parent's value
    private static $priority = 0;
    
    private $highlighter;

    public function getHighlighter(){
	if($this->highlighter == NULL){
	    $this->highlighter =  new \FSHL\Highlighter(new \FSHL\Output\Html(),\FSHL\Highlighter::OPTION_LINE_COUNTER);
	}
	return   $this->highlighter;
    }




    public function getTemplateName() {
	return "text";
    }
    
    public static function knownFileType($file) {
	return \LightFM\Filetypes::isText($file);
    }

    public function __construct($path) {
	parent::__construct($path);
	
	
    }
    
    
    public function getContent(){
	$this->Highlighter->setLexer(new \FSHL\Lexer\Html());
	$text = $this->Highlighter->highlight(implode(file($this->fullPath)));
	$this->parse($text);
	return $this->parse($text);
    }
 
    
    private function parse($text){
	//$array = explode("\n", $text);
	$array = preg_split("/(<[^>]+class=.line.[^>]*>[^<]+<\/span>)/", $text);
	$len = count($array);
	$string="";
	for($i=1; $i<$len-1;$i++){
	    // because first and last lines are empty
	    $string .= '<span class="row"><i data-line="'.($i).'"></i><code>'.$array[$i].'</code></span>';
	}
	return $string;
    }
}