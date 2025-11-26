<?php

class PerchMembers_Affiliates extends PerchAPI_Factory
{
    protected $table  = 'affiliates';
    protected $pk     = 'id';
    	protected $singular_classname = 'PerchMembers_Affiliate';
	protected $default_sort_column = 'id';
       public $default_fields = '
        				<perch:affiliates type="email" id="memberEmail" label="Email" listing="true" order="98" />
                        ';

public function get_affiliates_listing($sort,$Paging=false)
    {  try{
     $sort_val = "id";
            $sort_dir = "asc";
            if(isset($sort)){
        if (strpos($sort, '^') === 0) {
            $sort_val = substr($sort, 1);
            $sort_dir = "desc";
        } else {
            $sort_val = $sort;
        }
            }
      $sql = 'SELECT * FROM '.PERCH_DB_PREFIX.'affiliates order by '.$sort_val.' '.$sort_dir;

      		$results=$this->db->get_rows($sql);
      	//	print_r($results);
      		        return $this->return_instances($results);

} catch (Exception $e) {
    echo "Database error: " . $e->getMessage();
}
    }
	public function get_by_affID($affID='nan', $Paging=false)
    {
        return $this->get_by('affid', $affID, $Paging);
    }
    	public function get_edit_columns()
    	{

    		$Template   = $this->api->get('Template');
    		$Template->set('members/affiliate.html', 'members', $this->default_fields);

    	    $tags = $Template->find_all_tags_and_repeaters('affiliates');

    	    $out = array();

    	    if (PerchUtil::count($tags)) {
    	    	foreach($tags as $Tag) {
    	    		if ($Tag->listing()) {
    	    			$out[] = array(
    	    			            'id'=>$Tag->id(),
    	    			            'title'=>$Tag->label(),
    	    			            'Tag'=>$Tag,
    	    			        );
    	    		}
    	    	}
    	    }
    	    return $out;

    	}
}
