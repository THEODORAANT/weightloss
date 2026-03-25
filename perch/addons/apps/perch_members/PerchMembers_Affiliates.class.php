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

public function get_affiliates_listing($sort, $search='', $Paging=false)
    {
        try {
            $sort_val = 'id';
            $sort_dir = 'asc';

            if (isset($sort)) {
                if (strpos($sort, '^') === 0) {
                    $sort_val = substr($sort, 1);
                    $sort_dir = 'desc';
                } else {
                    $sort_val = $sort;
                }
            }

            $allowed_sort_columns = ['id', 'affid', 'credit', 'member_id', 'program_type', 'affiliate_name', 'member_email'];
            if (!in_array($sort_val, $allowed_sort_columns, true)) {
                $sort_val = 'id';
            }

            if ($sort_val === 'affiliate_name') {
                $sort_expr = "TRIM(CONCAT(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(m.memberProperties, '$.first_name')), ''), ' ', COALESCE(JSON_UNQUOTE(JSON_EXTRACT(m.memberProperties, '$.last_name')), '')))";
            } elseif ($sort_val === 'member_email') {
                $sort_expr = 'm.memberEmail';
            } else {
                $sort_expr = 'a.'.$sort_val;
            }

            $sql = "SELECT a.*,
                           m.memberEmail AS member_email,
                           TRIM(CONCAT(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(m.memberProperties, '$.first_name')), ''), ' ', COALESCE(JSON_UNQUOTE(JSON_EXTRACT(m.memberProperties, '$.last_name')), ''))) AS affiliate_name
                    FROM ".PERCH_DB_PREFIX."affiliates a
                    LEFT JOIN ".PERCH_DB_PREFIX."members m ON m.memberID = a.member_id";

            if ($search !== '') {
                $search_like = '%'.$search.'%';
                $sql .= " WHERE a.affid LIKE ".$this->db->pdb($search_like)."
                           OR m.memberEmail LIKE ".$this->db->pdb($search_like)."
                           OR TRIM(CONCAT(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(m.memberProperties, '$.first_name')), ''), ' ', COALESCE(JSON_UNQUOTE(JSON_EXTRACT(m.memberProperties, '$.last_name')), ''))) LIKE ".$this->db->pdb($search_like);
            }

            $sql .= ' ORDER BY '.$sort_expr.' '.$sort_dir;

            $results = $this->db->get_rows($sql);
            return $this->return_instances($results);
        } catch (Exception $e) {
            echo 'Database error: '.$e->getMessage();
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
