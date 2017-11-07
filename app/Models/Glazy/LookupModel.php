<?php namespace App\Models\Glazy;

use Illuminate\Database\Eloquent\Model;

class LookupModel extends Model {

    protected $static_values = array();

    public function getValue($id)
    {
//        if (isset($this->static_values[$id]))
        if (isset($id) && !empty($id) && array_key_exists($id, $this->static_values))
        {
            return $this->static_values[$id];
        }
        return null;
    }

    public function getId($name)
    {
        if ($name)
        {
            return array_search($name, $this->static_values);
        }
        return null;
    }

    public function getAll()
    {
        return $this->static_values;
    }


}