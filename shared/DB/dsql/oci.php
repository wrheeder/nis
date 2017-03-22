<?php
/**
 * Created by IntelliJ IDEA.
 * User: WRheeder
 * Date: 23/02/2017
 * Time: 8:27 AM
 */
class DB_dsql_oci extends DB_dsql
{
    public $bt = '';
    public function limit($cnt, $shift = 0)
    {
        $cnt += $shift;
        $this->where('NUM_ROWS>=', $shift);
        $this->where('NUM_ROWS<', $cnt);

        return $this;
    }
    public function render_limit()
    {
        return '';
    }
}
