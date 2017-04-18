<?php
class Model_ApplicationPageGenericModel extends Model_Table{
    public $id_field='site_id';
    public $set_id = '';
    public $set_table_name = '';
    public $table = "default";
    
    function init() {
        
        if($this->set_table_name!='' || isset($_GET['app_page_tbl']))
            $this->table = $this->set_table_name!=''?$this->set_table_name:$_GET['app_page_tbl'];
        
        parent::init();
        
        $site=$this->getField('site_id')->system(true);
                
        $m_sections = $this->add('Model_ApplicationPagesSections');
        $m_sections->addCondition('application_pages_id', $this->set_id!=''?$this->set_id:$_GET['app_page_id']);
        
        $sections = $m_sections->getRows();
        
        foreach ($sections as $section) {
            
            $m_fields = $this->add('Model_ApplicationPagesDataFields',array('kool'=>'kop'));
            $m_fields->addCondition('application_pages_sections_id', $section['id']);
            $fields = $m_fields->getRows();
            
            foreach ($fields as $field) {
                if(strpos($field['field_type'],'enum')===false){
                    $this->addField($field['field_name'])->type($field['field_type']);
                }
                else{
                    $outp='';
                    preg_match_all('/\'([^\']*?)\'/',$field['field_type'],$outp);
//                  $outp=explode(',',$outp[1]);
//                  die(var_dump($outp));
                    $this->addField($field['field_name'])->enum($outp[1]);
                }
            }
        
        }
        
//        echo var_dump($this->table."<br>");
//        Allow this onlya when changes to application fields needs to be synced to db
//        $this->add('dynamic_model/Controller_AutoCreator_MySQL');    
    }
    
    function setAppPage($app_page_name){
        $ap = $this->add("Model_ApplicationPages");
        $ap->tryLoadBy('page_name',$app_page_name);
        if($ap->loaded()){
            $this->set_table_name=$ap['table_name'];
            $this->set_id=$ap->id;
            $this->init();
        }else{
            die('app page not found '.$app_page_name);
        }
    }
}