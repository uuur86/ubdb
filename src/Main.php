<?php

/**
 * UBDB JSON DATABASE CLASS
 * Fast, small, easy and flexible databases management processes using json data type.
 *
 * @Package     ubdb
 * @Author      Uğur Biçer, <info@ugurbicer.com.tr>
 * @Version     0.329.1
 *
 */
namespace Ubdb;

    if( class_exists( "\\Ubdb\\Main", false ) ) { return; }

class Main{

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

    private function create_table($tblname){
    $table_dir = $this->params['table_dir'].$tblname.'/';

        if(!file_exists($table_dir)){
        mkdir($table_dir,0755,TRUE);
        }

    }

    private function run(){
    $this->params['table_dir'] = $this->params['dir'].$this->db.'/lang-'.$this->params['lang'].'/';

        if($this->create_table($this->table)){
        // Files creted
        }
    }


    private function rows($table){

        if(isset($this->indexData[$table])){
        $this->indexData[$table]['rows']++;
        return $this->indexData[$table]['rows'];
        }

    $indexF = $this->readdb($table,'index');

        if(!is_array($indexF)){
        $indexF['rows'] = 0;
        }
    $this->indexData[$table] = $indexF;
    $this->indexData[$table]['rows']++;
    return $indexF['rows'];
    }


    // We define a table namely $name
    // Table structure must be define in $tableinfo as a array
    public function db($name, $tableInfo) {
    $this->db = $name;

        foreach ($tableInfo as $tableName=>$tableArr){
        $this->define('table', $tableName);

            foreach($tableArr as $colnames){
            $this->define('col', $colnames);
            }
        $this->run();
        $this->checkIndex();
        }

    }




    private function define($key, $val){

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



    private function checkIndex(){

        foreach ($this->cols[$this->table] as $prkey){

            if($prkey=='index'){continue;}

        $this->rows[$this->table][$prkey] = $this->readdb($this->table,$prkey);
        }
    }



    private function dbfile($table,$colname){
    $dbfile = $this->params['table_dir'].$table.'/'.$colname.'.json.php';
    return $dbfile;
    }



    private function readdb($table, $colname){
    $dbfile = $this->dbfile($table, $colname);

        if(file_exists($dbfile)){
        $file_content = file_get_contents($dbfile);
        $file_content = substr($file_content,strpos($file_content,'?>')+2);

            if($file_content){
            $file_content = json_decode($file_content, TRUE);
            return $file_content;
            }
        }
    return false;
    }


    private function writedb($table, $colname){
    $jsondatas = $this->rows[$table][$colname];

        if(count($jsondatas)<1){return false;}

        if($colname<>'index'){

            if(empty($jsondatas)){
            $jsondatas = null;
            }
        }

    $dbfile = $this->dbfile($table, $colname);

    $jsondatas = json_encode($jsondatas);

        if(file_put_contents($dbfile, '<?php die?>'.$jsondatas, LOCK_EX)){
        return true;
        }

    return false;
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

    $termC  = preg_match("#^$term_txt$#siu", $text);

    return $termC;
    }

    // We define table, column and search term(s)
    // Will be return as array contains row key => how many time matched
    private function search_term($table, $colname, $term){
    $searchio   = $this->readdb( $table, $colname );

    $returnrows = array();

        foreach( $searchio as $drowname=>$drowval ){

            if( empty($term) || count($term)<1 ){continue;}

        $termcounter = $this->matches($drowval, $term);

            if( $termcounter>0 ){
            $returnrows[$drowname] = $termcounter;
            }
        }

    return $returnrows;
    }





    // Configure to language
    public function lang($lang) {
    $this->define("lang", $lang);
    }



    // Add new row
    public function add($table, $rowArr){
    $editId = $this->rows($table);

        if($this->edit_id != null){
        $editId = $this->edit_id;
        }

        foreach($this->cols[$table] as $colname){
        $this->rows[$table][$colname][$editId] = $rowArr[$colname];
        }

    return $editId;
    }


    // Edit to exists row
    public function edit($table, $rowArr, $id){
    $this->edit_id = $id;
    $this->add($table, $rowArr);
    }


    // If we defined all rows
    // must be save with this function.
    // ..
    // I didn't run in add function,
    // because will be wasting resources
    // running one more time.
    public function save(){

        foreach ($this->cols as $table => $prkey){

            foreach($prkey as $prkey_){

                if(strlen($prkey_)<3){continue;}

            $this->writedb($table, $prkey_);
            }
        //more table records
        $this->rows[$table]['index'] = $this->indexData[$table];

            try{
            $this->writedb($table, 'index');
            }
            catch(Exception $aa){
              return false;
            }

        }
    return true;
    }



    public function delete($table, $line){

        foreach($this->cols[$table] as $delcolname){
        $readeddata = $this->readdb($table, $delcolname);

            if(isset($readeddata[$line])){
            unset($readeddata[$line]);
            }
            else if(!array_splice($readeddata, $line, 1)){
            continue;
            }

        $readeddata_json = json_encode($readeddata);
        $dbfile          = $this->dbfile($table, $delcolname);
        file_put_contents($dbfile, $readeddata_json);
        }

    return true;
    }


    // Check for value is exists
    // Return as boolean
    public function check($table, $colname, $val){
    $oldvals = $this->readdb($table, $colname);

        if($oldvals && is_array($oldvals)){
        $oldvals = array_flip($oldvals);
        }

        if(isset($oldvals[$val])){
        return true;
        }

    return false;
    }

    // $proc variable is not ready
    // We can only searching for now
    // You can define as a array to $searchterm
    // Such as array('g','?','t') will be matching with get or got
    // or array('f','%','t') will be matching with float or foot
    public function get($table, $colname, $searchterm, $proc = "="){
    $rownumbs = $this->search_term($table, $colname, $searchterm);

        if($colname == "id"){
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

        foreach ($rownumbs as $rownumb => $rowcount){
        $roworder = abs(5 - $rowcount);

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
