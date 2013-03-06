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
 * @property string $Syntax
 * @property-read object $Highlighter Syntax highlighter
 */
 class TextFile extends File implements IFile{
     
    /**
     *	The presenter called for this file
     * @var string
     */
    protected $presenter =  'TextFile';
     
     // overwriting parent's value
    protected $iconName = 'file-text';
     
     // overwriting parent's value
    protected static $priority = 0;
    
    private static $suffixHighlight = array(
	'css'=>'CSS',
	'js'=>'JS',
	'sh'=>'Bash',
	'php'=>'PHP',
	'less'=>'CSS',
	'ini'=>'ini',
	'c'=>'Cpp',
	'cpp'=>'Cpp',
	'h'=>'h',
	'neon'=>'Neon',
	'sql'=>'SQL',
	'py'=>'Python',
	'texy'=>'Texy!',
	'html'=>'HTML',
	'xml'=>'XML',
	'text'=>'Plain text',
    );
    
    private static $mimeHighlight = array(
	'text/x-php'=>'PHP',
	'text/x-shellscript'=>'Bash',
	'text/html'=>'HTML',
	'application/xml'=>'XML',
    );
    
    /**
     * language used for highlight
     * @var string
     */
    public $syntax;
    
    private $fshl;

    
    
    /**
     * Return array of known languages for highlighting.
     * Make an unique array, but take care about actually selected
     * syntax.
     * @return array
     */
    public function getAvailableSyntax(){
	//return array_unique(self::$suffixHighlight);
	$uniq = array();
	if(($actualKey = array_search($this->getSyntax(),self::$suffixHighlight))){
	    $uniq[$actualKey]=$this->getSyntax();
	}
	foreach(self::$suffixHighlight as $key=>$item){
	    if(!in_array($item, $uniq)){
		$uniq[$key]=$item;
	    }
	}
	
	return $uniq;
    }
    
    public function getSyntax(){
	if($this->syntax == NULL){
	    
	    if(isset(self::$mimeHighlight[$this->getMimeType()])){
		// at first try it by mime
		$this->syntax = self::$mimeHighlight[$this->getMimeType()];
	    }else  if(isset(self::$suffixHighlight[$this->getSuffix()])){
		// if mimetype does not return specific thing, try suffix
		$this->syntax = self::$suffixHighlight[$this->getSuffix()];
	    }else{
		// and if still nothing, than set plaintext
		$this->syntax = 'text';
	    }
	}
	return $this->syntax;
    }
    public function setSyntax($syntax){
	$this->syntax = $syntax;
    }
    
    protected function getFSHL(){
	if($this->fshl == NULL){
	    $this->fshl =  new \FSHL\Highlighter(new \FSHL\Output\Html(),\FSHL\Highlighter::OPTION_LINE_COUNTER);
	}
	return   $this->fshl;
    }

    public function getFSHLSyntax(){
	    // now create correct output filter
	    switch(strtolower($this->syntax)){
		
		case 'cpp':
		case 'h':
		    $output = new \FSHL\Lexer\Cpp();
		    break;
		
		case 'css':
		    $output = new \FSHL\Lexer\Css();
		    break;
		
		case 'html':
		case 'php':
		case 'xml':
		    $output = new \FSHL\Lexer\Html();
		    break;
		
		case 'ini':
		    $output = new \FSHL\Lexer\Ini();
		    break;
		
		case 'js':
		    $output = new \FSHL\Lexer\Javascript();
		    break;
		
		case 'neon':
		    $output = new \FSHL\Lexer\Neon();
		    break;
		
		case 'python':
		    $output = new \FSHL\Lexer\Python();
		    break;
		
		case 'sql':
		    $output = new \FSHL\Lexer\Sql();
		    break;
		
		case 'texy!':
		    $output = new \FSHL\Lexer\Texy();
		    break;
		
		default:
		    $output = new \FSHL\Lexer\Minimal();
	    }
	    return $output;
    }


    public function getTemplateName() {
	return "text";
    }
    
    public static function knownFileType($file) {
	return \LightFM\Filetypes::isText($file);
    }

    public function __construct($path) {
	parent::__construct($path);
	
	$this->iconName .= ' '. strtolower($this->getSyntax());
    }
    
    
    /**
     * Return highlighted content
     * @return string
     */
    public function getHighlightedContent($parser){
	if($parser == 'geshi'){
	    /** will we use GeSHi? */
	    $g = new \GeSHi(implode(file($this->fullPath)), 'HTML');
	    return $g->parse_code();
	    
	}else if($parser == 'fshl'){
	    /** Or FSHL? */
	    $this->getFSHL()->setLexer($this->getFSHLSyntax());
	    // the \n is there because the lexer needs \n at the end
	    $text = $this->getFSHL()->highlight(implode(file($this->fullPath))."\n");
	    $this->parse($text);
	    return explode("<ROWEND />",$this->parse($text));
	}else{
	    return "No highlighter selected.";
	}
    }
    
    public function getContent(){
	return implode(file($this->fullPath));
    }
 
    
    private function parse($text){
	//$array = explode("\n", $text);
	$array = preg_split("/(<[^>]+class=.line.[^>]*>[^<]+<\/span>)/", $text);
	$len = count($array);
	$string="";
	for($i=1; $i<$len-1;$i++){
	    // because first and last lines are empty
	    $string .= '<span class="row"><i data-line="'.($i).'"></i><code>'.$array[$i].'</code></span><ROWEND />';
	}
	return $string;
    }
    
}