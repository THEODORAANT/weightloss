<?php

class PerchEmailOctopus_Imports extends PerchAPI_Factory
{
	protected $table               = 'emailoctopus_imports';
    protected $pk                  = 'importID';
    protected $singular_classname  = 'PerchEmailOctopus_Import';

    protected $namespace           = 'import';

    protected $event_prefix        = 'emailoctopus.import';
	protected $master_template	   = 'emailoctopus/imports/import.html';
	
	protected $default_sort_column = 'importTitle';
	protected $created_date_column = 'importCreated';

	public function run_next_import()
	{
		$Import = $this->get_next_import();
		if ($Import) {
			return $Import->run();	
		}
		return false;
	}

	public function get_next_import($type=null, $sourceID=null)
	{
		$sql = 'SELECT * FROM '.$this->table.' WHERE 1=1';

		if (!is_null($type)) {
			$sql .= ' AND importType='.$this->db->pdb($type);

			if (!is_null($sourceID)) {
				$sql .= ' AND importSourceID='.$this->db->pdb((int)$sourceID);
			}
		}

		$sql .= ' ORDER BY importUpdated ASC 
				LIMIT 1';
		return $this->return_instance($this->db->get_row($sql));
	}

}
