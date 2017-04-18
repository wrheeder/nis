<?php
/**
 * Created by IntelliJ IDEA.
 * User: WRheeder
 * Date: 22/02/2017
 * Time: 2:07 PM
 */
class Page_AssetSync extends Page
{

    public $title = 'AssetSync';

    function init()
    {
        parent::init();

        $q=$this->api->db2->dsql()->table('network_planning.logcell');
        $logcells = $q->field('*')->get();

        $assetMapping = $this->add('Model_AssetMapping');
        $assetMappingHistory = $this->add('Model_assetMappingHistory');

        $assetMapping_array = $assetMapping->getRows();
        $assetMapping_array_new = array();
        foreach ($assetMapping_array as $key=>$val){
            $assetMapping_array_new[$val['logcellpk']]=$val;
        }
        $assetMapping_array=$assetMapping_array_new;
        unset($assetMapping_array_new);
        $asset_ids = array();


        foreach ($logcells as $cell) {
            $asset_ids[$cell['LOGCELLPK']]=$cell;
            if(isset($assetMapping_array[$cell['LOGCELLPK']])){
                ////  Cell is in mapping table, check changes on indv fields
                // @ todo  Not tracking Asset changes yet


            }else{
                ////  Cell not in mapping table, add and write hist
                //die(print_r($cell));
                $assetMapping->set('logcellpk',$cell['LOGCELLPK']);
                $assetMapping->save();
                $assetMappingHistory->set('asset_mapping_id', $assetMapping->get('id'));
                $assetMappingHistory->set('fld', 'cell - added');
                $assetMappingHistory->set('previous', null);
                $assetMappingHistory->set('current',$cell['IDNAME']);
                $assetMappingHistory->set('changed_on', date('Y/m/d h:i:s'));
                $assetMappingHistory->saveAndUnload();
                $assetMapping->unload();
                $assetMapping_array[$cell['LOGCELLPK']]=$cell;
            }

            //do diff between mapping table and cell array to see which cells were removed.


        }

        $to_delete = array_diff_key($assetMapping_array,$asset_ids);
        foreach ($to_delete as $del_item) {

        }
    }
}
