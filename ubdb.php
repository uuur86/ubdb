<?php

/**
 * UBDB JSON DATABASE CLASS
 * Fast, small, easy and flexible databases management processes using json database.
 * 
 * @Author      Uğur Biçer, <info@ugurbicer.com.tr>
 * @Version     0.326
 * 
 */


    if(class_exists('ubdb', false)){return;}

class ubdb{
    
private $table,
        $cols,
        $params,
        $db,
        $rows,
        $itemTemp     = array(),
        $config_names = array('@','lang','dir'),
        $edit_id      = null,
        $indexData    = array();


    function __construct($params=array()) {
    $this->params['dir'] = 'db/';
    
        foreach ($params as $paramname=>$paramval){
        $this->params[$paramname] = $paramval;
        }
        
    $this->config_names = array_flip($this->config_names);
    }
    
    
    
    private function run(){
    $this->params['table_dir'] = $this->params['dir'].$this->db.'/lang-'.$this->params['lang'].'/';  

        if(!$this->check_configs()){
        // Already created
        }
    $this->checkIndex();
    }
    
    
    private function check_configs(){  
        
        if(!$this->create_table($this->table)){
        return false;
        }
        
    return true;
    }
    
    
    private function checkIndex(){
        
        if(count($this->indexData[$this->table])>0){
        return;
        }
        
    $maxcount = 0;
    
        foreach ($this->cols[$this->table] as $prkey){
        $this->rows[$this->table][$prkey] = $this->readdb($this->table,$prkey);
        $index_d = count($this->rows[$this->table][$prkey]); 
        
            if($index_d > $maxcount){
            $maxcount = $index_d;
            }
        }
        
        if($maxcount > 0){
        $this->indexData[$this->table]['rows'] = $maxcount;
        $this->indexData[$this->table]['cols'] = count($this->cols[$this->table]);
        }
    }

    
    
    
    
    private function create_table($tblname){
    $table_dir = $this->params['table_dir'].$tblname.'/';
    
        if(!file_exists($table_dir)){
        mkdir($table_dir,0777,TRUE);     
        }  
        
    }
    
    
    
    public function define($key,$val){
    
        if($key=='col' && isset($this->table)){
            
            if(!isset($this->cols[$this->table])){
            $this->cols[$this->table] = array('index');
            }

        $this->cols[$this->table][] = $val;
        }
        else if($key=='table'){
        $this->table  = $val;
        }
        else if(isset($this->config_names[$key])){
        $this->params[$key]   = $val;
        }
        else{
        $this->itemTemp[$key] = $val;    
        }
           
    } 
    

    
    private function dbfile($table,$colname){
    $dbfile = $this->params['table_dir'].$table.'/'.$colname.'.json';
    return $dbfile;
    }
    
    

    private function readdb($table,$colname){
    $dbfile = $this->dbfile($table,$colname);
    
        if(file_exists($dbfile)){
        $file_content = file_get_contents($dbfile);
        
            if($file_content){
            $file_content = json_decode($file_content, TRUE);
            return $file_content;
            }
        }
    return false;
    }
    
    
    
    public function codetoId($code, $vvArr = array()){
    $arrprcodes = $this->rows[$this->table]["productcode"];
    $codeId_    = array_keys($arrprcodes, $code);
        
        if(count($vvArr)>0){
            
            foreach($codeId_ as $codeId){
            
                if($this->rows[$this->table][$vvArr[0]][$codeId]==$vvArr[1]){
                return $codeId;
                }
            }
            
        $codeId = 0;
        }
        else{
        $codeId = $codeId[0];     
        }

    return $codeId;
    }
    

    
    private function writedb($table,$colname){
    $jsondatas = $this->rows[$table][$colname];
        
        if(count($jsondatas)<1){return false;}
    
        if($colname<>'index'){
            
            if(empty($jsondatas)){
            $jsondatas = null;
            }
        }
    
    $dbfile = $this->dbfile($table,$colname);    

    $jsondatas = json_encode($jsondatas);
    
        if(file_put_contents($dbfile,$jsondatas)){
        return true;
        }
        
    return false;
    }
    
    
    public function check($table,$colname,$val){
    $oldvals = $this->readdb($table,$colname);
    
        if($oldvals && is_array($oldvals)){
        $oldvals = array_flip($oldvals);
        }
        
        if(isset($oldvals[$val])){
        return true;
        }
        
    return false;
    }
    
    

    public function add($table){
    $this->indexData[$table]['rows']++;
    
    $editId = $this->indexData[$table]['rows'];
            
        if($this->edit_id != null){
        $editId = $this->edit_id;
        }
        
        foreach($this->cols[$table] as $colname){
        $this->rows[$table][$colname][$editId] = $this->itemTemp[$colname];
        }
        
    return $this->indexData[$table]['rows'];
    }
    
    
    
    
    public function save(){
        
        foreach ($this->cols as $table=>$prkey){

            foreach($prkey as $prkey_){
           
                if(strlen($prkey_)<3){continue;}
                
            $this->writedb($table,$prkey_);
            }
        
        $this->rows[$table]['index'] = $this->indexData[$table];
        $this->writedb($table,'index');
        }
    }
    
    
    
    public function edit($table,$id){
    $this->edit_id = $id;
        
    $this->add($table);
    }

    
    private function matches($text, $terms) {
    $term_txt = '';    
        
        if(!is_array($terms)){
        $term_txt = $terms;
        }
        

        foreach($terms as $term){
            
            if(empty($term)){continue;}
            elseif($term=='%'){
            $term_txt .= '.+';
            continue;
            }
            elseif($term=='?'){
            $term_txt .= '.';
            continue;
            }
            
        $term_txt .= strtr($term, array(
                                  '.'=>'\\.', 
                                  '-'=>'\\-',
                                  '['=>'\\[',
                                  ']'=>'\\]',
                                  '('=>'\\(',
                                  ')'=>'\\)',
                                  '#'=>'',
                                  '+'=>'\\+',
                                  '*'=>'\\*'
                                    )
                            );
        }
    
    $termC  = preg_match("#$term_txt#siu", $text);
        //echo $term_txt.'<br/>';
    return $termC;
    }
    
    // We define table, column and search term(s)
    // Will be return as array contains row key => how many time matched
    private function search_term($table, $colname, $term){
    $searchio   = $this->readdb( $table, $colname );

    $returnrows = array();

        if( !is_array($term) ){
        $term   = ' '.$term.' ';
        }

        
        foreach( $searchio as $drowname=>$drowval ){

            if( empty($term) && count($term)<1 ){continue;}

        $termcounter = $this->matches($drowval, $term);
            
            if( $termcounter>0 ){
            $returnrows[$drowname] = $termcounter;
            }
        }
        
    return $returnrows;
    }

    
    
    public function delete($table, $line){
        
        foreach($this->cols[$table] as $delcolname){
        $readeddata = $this->readdb($table, $delcolname); 
 
            if(isset($readeddata[$line])){
            unset($readeddata[$line]);
            }
            else if(!array_splice($readeddata, $line,1)){
            continue;
            }
        
        $readeddata_json = json_encode($readeddata); 
        $dbfile          = $this->dbfile($table, $delcolname);
        file_put_contents($dbfile,$readeddata_json);
        }
        
    return true;
    }
    
    
    
    public function get($table, $colname, $searchterm, $proc = "="){
    $rownumbs = $this->search_term($table, $colname, $searchterm); 

    
        if($colname=="id"){
        $returnarr = array();
        
            foreach ($this->cols[$table] as $tbcols){
            $searchio_e         = $this->readdb($table, $tbcols);
            $returnarr[$tbcols] = $searchio_e[$searchterm];
            }
            
        return $returnarr;
        }
    
        if(count($rownumbs)<1){
        return array();
        }
    
    $returnarr = array();
    
        foreach ($rownumbs as $rownumb=>$rowcount){
        $roworder = abs(5-$rowcount);
        
            while(isset($returnarr[$roworder])){
            $roworder++;
            }
        
        $returnarr[$roworder]=array();
        
            foreach ($this->cols[$table] as $tbcols){
            $searchio_e = $this->readdb($table, $tbcols);
            $returnarr[$roworder][$tbcols] = $searchio_e[$rownumb];
            }
            
        $returnarr[$roworder]['id'] = $rownumb;
        }
  
    return $returnarr;
    }
    
    public function lang($lang) {
    $this->define("lang",$lang);
    }
    
    public function db($name, $tableInfo) {
    $this->db = $name;
    
        foreach ($tableInfo as $tableName=>$tableArr){
        $this->define('table', $tableName);
            
            foreach($tableArr as $colnames){
            $this->define('col', $colnames);
            }
        $this->run();
        }
    }
    
    
    public function join($aArr1, $aArr2, $interSect1, $interSect2) {
    $rArr = array();
    
        foreach($aArr1 as $aValArr){
            
            foreach($aArr2 as $bValArr){
        
                if($aValArr[$interSect1] == $bValArr[$interSect2]){
                $rArr[] = array_merge($aValArr, $bValArr);
                }
            }
        }
    
    return $rArr;   
    }
}



