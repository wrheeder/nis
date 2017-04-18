<?php

class Page_treeJSON extends Page {

    function init() {
        
        header('Content-Type: application/json');
        $json_out = array();
        $sf = $_GET['filter'];
        if($_GET['id']=='#')
        {
           $m_regions = $this->add("Model_UserRegions")->addCondition('users_id', $this->api->auth->model->id)->setOrder('regions_id');
           $m_regions_join = $m_regions->join("Regions");
            
//            $m_regions = $this->add('Model_Regions');
            $json_out[]=array('id'=>'[Root]0000','text'=>'Regions',"parent"=>"#",'icon'=>'../vendor/jsTree/js/themes/default/transit-store.png');
            foreach ($m_regions->getRows() as $region){
                //echo var_dump($region);
                $q=$this->api->db->dsql();
                if($sf!='None' && $sf!=''){
                    $field = $q->table('site')->where('regions_id',$region['regions_id'])->where('site_code', 'like', '%' . $sf . '%')->field('count(*)');
                }else
                {
                    $field = $q->table('site')->where('regions_id',$region['regions_id'])->field('count(*)');
                }
                if($field!='0'){
                    $json_out[] = array("id"=>"[Region]".$region['regions_id'],"text"=>$region['regions'].'('.$field.')','parent'=>'[Root]0000',"children"=>true,'icon'=>'../vendor/jsTree/js/themes/default/warehouse-star.png');
                }
            }
        }
        if(strpos($_GET['id'],'[Region]')===0){
            $m_sites = $this->add('Model_Site');
            //$m_regions = $this->add('Model_Regions');
//            echo str_replace('[Region]', '', $_GET['id']) ;
            $m_sites->addCondition('regions_id',  str_replace('[Region]', '', $_GET['id']));
            if($sf!='None' && $sf!=''){
                $m_sites->addCondition('site_code', 'like', '%' . $sf . '%');   
            }
            $m_sites->setOrder('site_code','asc');
            foreach ($m_sites->getRows() as $site){
                $json_out[] = array("id"=>"[Site]".$site['id'],"text"=>$site['site_code'].' '.$site['candidate_letter']." - ".$site['site_name'],'parent'=>$_GET['id'],"children"=>true,'icon'=>'../vendor/jsTree/js/themes/default/gray-broadcast-tower-icon_sml.png');
            }
        }
        if(strpos($_GET['id'],'[Site]')===0){
                $json_out[] = array("id"=>"[RF]".str_replace('[Site]','',$_GET['id']),"text"=>'RF Info','parent'=>$_GET['id'],"children"=>false,'icon'=>'../vendor/jsTree/js/themes/default/root.png');
                $json_out[] = array("id"=>"[TX]".str_replace('[Site]','',$_GET['id']),"text"=>'Tx Info','parent'=>$_GET['id'],"children"=>false,'icon'=>'../vendor/jsTree/js/themes/default/root.png');
                $json_out[] = array("id"=>"[LEASE]".str_replace('[Site]','',$_GET['id']),"text"=>'Lease Info','parent'=>$_GET['id'],"children"=>true,'icon'=>'../vendor/jsTree/js/themes/default/folder.png');
        }
        if(strpos($_GET['id'],'[LEASE]')===0){
            $m_leases = $this->add('Model_LeaseSite');
            $m_leases->addCondition('site_id',  str_replace('[LEASE]', '', $_GET['id']));
            foreach ($m_leases->getRows() as $lease){
//                die(var_dump($lease));
                $json_out[] = array("id"=>"[LEASE_ID]".$lease['lease_id'],"text"=>$lease['lease'],'parent'=>$_GET['id'],"children"=>false,'icon'=>'../vendor/jsTree/js/themes/default/file.png');
            }
        }
        echo json_encode($json_out);
        exit;
    }
}
