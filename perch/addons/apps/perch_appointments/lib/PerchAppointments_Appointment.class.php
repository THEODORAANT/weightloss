<?php

class PerchAppointments_Appointment extends PerchAPI_Base
{
    protected $table  = 'appointments';
    protected $pk     = 'appointmentID';

    private $tmp_url_vars = array();


    public function update($data)
    {

        $PerchAppointments_Appointments = new PerchAppointments_Appointments();


        // Update the event itself
        parent::update($data);


 		return true;
    }

    public function delete()
    {
        parent::delete();

    }

    public function date()
    {
        return date('Y-m-d', strtotime($this->appointmentDate()));
    }

  public function to_array($template_ids=false)
    {
        $out = parent::to_array();




        return $out;
    }

    private function substitute_url_vars($matches)
    {
        $url_vars = $this->tmp_url_vars;
        if (isset($url_vars[$matches[1]])){
            return $url_vars[$matches[1]];
        }
    }

}
