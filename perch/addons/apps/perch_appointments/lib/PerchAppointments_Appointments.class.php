<?php


class PerchAppointments_Appointments  extends PerchAppointments_Factory
{
    protected $table     = 'appointments';
	protected $pk        = 'appointmentID';
	protected $singular_classname = 'PerchAppointments_Appointment';

	protected $default_sort_column = 'appointmentDate';
    protected $created_date_column = 'appointmentDate';

	public $static_fields   = array('appointmentDate');

    /**
     * Ensure newer columns exist on older installs.
     */
    public function ensure_schema()
    {
        $schema_updates = [
            'orderID' => "ALTER TABLE {$this->table} ADD COLUMN orderID INT UNSIGNED NULL DEFAULT NULL",
            'appointmentStatus' => "ALTER TABLE {$this->table} ADD COLUMN appointmentStatus ENUM('pending','confirmed','completed') NOT NULL DEFAULT 'pending'",
            'appointmentConfirmed' => "ALTER TABLE {$this->table} ADD COLUMN appointmentConfirmed TINYINT(1) NOT NULL DEFAULT 0",
            'confirmedAt' => "ALTER TABLE {$this->table} ADD COLUMN confirmedAt DATETIME NULL DEFAULT NULL",
        ];

        foreach ($schema_updates as $column => $sql) {
            $exists = $this->db->get_row("SHOW COLUMNS FROM {$this->table} LIKE '{$column}'");
            if (!PerchUtil::count($exists)) {
                $this->db->execute($sql);
            }
        }
    }

    /**
    * takes the event data and inserts it as a new row in the database.
    */
    public function create($data)
    {
        $appointmentID = $this->db->insert($this->table, $data);

        return $this->find($appointmentID);
    }

    private function _standard_pre_template_callback($opts)
    {
        return function($items) use ($opts) {
            if (isset($opts['include-meta'])) {
                $domain = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
                if (PerchUtil::count($items)) {
                    foreach($items as &$item) {
                        $item['domain'] = $domain;
                    }
                }
            }
            return $items;
        };
    }

    private function _standard_where_callback($opts)
    {
        $db = $this->db;

        return function(PerchQuery $Query) use ($opts, $db) {
            if (isset($opts['appointmentID'])) {
                $Query->where[] = ' appointmentID='.(int)$opts['appointmentID'].' ';
            }

            return $Query;
        };
    }

    public function get_custom($opts)
    {
        $announcements = array();
        $Announcement = false;
        $single_mode = false;
        $where = array();
        $order = array();
        $limit = '';

        if (isset($opts['_id'])) {
            $single_mode = true;
            $Announcement = $this->find($opts['_id']);
        }else{
            if (isset($opts['filter']) && is_array($opts['filter'])) {
                $keys = $opts['filter'];
                foreach($keys  as $key => $kvalue){
                    $raw_value = $kvalue;

                    $match = 'eq';
                    if (is_array($raw_value)) {
                        $match = 'between';
                    } else {
                        $value = $this->db->pdb($kvalue);
                    }

                    switch ($match) {
                        case 'eq':
                        case 'is':
                        case 'exact':
                            $where[] = $key.'='.$value;
                            break;
                        case 'neq':
                        case 'ne':
                        case 'not':
                            $where[] = $key.'!='.$value;
                            break;
                        case 'gt':
                            $where[] = $key.'>'.$value;
                            break;
                        case 'gte':
                            $where[] = $key.'>='.$value;
                            break;
                        case 'lt':
                            $where[] = $key.'<'.$value;
                            break;
                        case 'lte':
                            $where[] = $key.'<='.$value;
                            break;
                        case 'contains':
                            $v = str_replace('/', '\\/', $raw_value);
                            $where[] = $key." REGEXP '[[:<:]]'.$v.'[[:>:]]'";
                            break;
                        case 'regex':
                        case 'regexp':
                            $v = str_replace('/', '\\/', $raw_value);
                            $where[] = $key." REGEXP '".$v."'";
                            break;
                        case 'between':
                        case 'betwixt':
                            $vals  = $raw_value;
                            if (PerchUtil::count($vals)==2) {
                                $where[] = $key.'>'.trim($this->db->pdb($vals[0]));
                                $where[] = $key.'<'.trim($this->db->pdb($vals[1]));
                            }
                            break;
                        case 'eqbetween':
                        case 'eqbetwixt':
                            $vals  = explode(',', $raw_value);
                            if (PerchUtil::count($vals)==2) {
                                $where[] = $key.'>='.trim($this->db->pdb($vals[0]));
                                $where[] = $key.'<='.trim($this->db->pdb($vals[1]));
                            }
                            break;
                    }
                }
            }
        }

        if (isset($opts['sort'])) {
            $desc = false;
            if (isset($opts['sort-order']) && $opts['sort-order']=='DESC') {
                $desc = true;
            }
            $order[] = $opts['sort'].' '.($desc ? 'DESC' : 'ASC');
        }

        if (isset($opts['sort-order']) && $opts['sort-order']=='RAND') {
            $order[] = 'RAND()';
        }

        if (isset($opts['count'])) {
            $count = (int) $opts['count'];

            if (isset($opts['start'])) {
                $start = (((int) $opts['start'])-1). ',';
            }else{
                $start = '';
            }

            $limit = $start.$count;
        }

        if ($single_mode){
            $announcements = array($Announcement);
        }else{
            $sql = 'SELECT DISTINCT e.* FROM '.$this->table.' e ';

            if (count($where)) {
                $sql .= ' WHERE '.implode(' AND ', $where);
            }

            if (count($order)) {
                $sql .= ' ORDER BY '.implode(', ', $order);
            }

            if ($limit!='') {
                $sql .= ' LIMIT '.$limit;
            }

            $rows = $this->db->get_rows($sql);
            $announcements  = $this->return_instances($rows);
        }

        return $announcements;
    }
}
