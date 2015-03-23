<?php
class grocery_crud_model_Postgre extends grocery_CRUD_Generic_Model{
 function __construct(){
     parent::__construct();
 }
 
 function get_list()
    {
    	if($this->table_name === null)
    		return false;

        $select = "{$this->table_name}".".*";

        // this variable is used to save table.column info since postgresql doesn't support "AS 'table.column'" syntax
        $additional_fields = array();
    	//set_relation special queries
    	if(!empty($this->relation))
    	{
    		foreach($this->relation as $relation)
    		{
    			list($field_name , $related_table , $related_field_title) = $relation;
    			$unique_join_name = $this->_unique_join_name($field_name);
    			$unique_field_name = $this->_unique_field_name($field_name);

                        if(strstr($related_field_title,'{'))
                        {
                        $select .= ", '".str_replace(array('{','}'),array("' || COALESCE(".$unique_join_name.".".$this->ESCAPE_CHAR, $this->ESCAPE_CHAR.", '') || '"),str_replace("'","\\'",$related_field_title))."' as ".$unique_field_name;                                
                        }
    			else
    			{
    				$select .= ', ' .$unique_join_name. '.'. $related_field_title.' AS '.$unique_field_name;
    			}

    			if($this->field_exists($related_field_title)){
    			    $additional_fields[$this->table_name. '.'. $related_field_title] = $related_field_title;
    			    // this syntax doesn't work on postgresql
                    //$select .= ', '.$this->protect_identifiers($this->table_name. '.'. $related_field_title).' AS \''.$this->table_name. '.'. $related_field_title.'\'';
                }

    		}
    	}

    	//set_relation_n_n special queries. We prefer sub queries from a simple join for the relation_n_n as it is faster and more stable on big tables.
    	if(!empty($this->relation_n_n))
    	{
			$select = $this->relation_n_n_queries($select);
    	}
    	$this->db->select($select, false);        
    	$results = $this->db->get($this->table_name)->result();

        // add information from additional_fields
        for($i=0; $i<count($results); $i++){
            foreach($additional_fields as $alias=>$real_field){
                $results[$i]->{$alias} = $results[$i]->{$real_field};
            }
        }
    	return $results;      
    }
    
    function get_relation_array($field_name , $related_table , $related_field_title, $where_clause, $order_by, $limit = null, $search_like = null)
    {
    	$relation_array = array();
    	$field_name_hash = $this->_unique_field_name($field_name);

    	$related_primary_key = $this->get_primary_key($related_table);

    	$select = $related_table.'.'.$related_primary_key.', ';        
    	if(strstr($related_field_title,'{'))
    	{
    		$related_field_title = str_replace(" ", "&nbsp;", $related_field_title);
                
                /*$select .= $this->build_concat_from_template(
                    $related_field_title,
                    $this->ESCAPE_CHAR,
                    $this->ESCAPE_CHAR,
                    $this->protect_identifiers($field_name_hash)
                );*/
                
                
    		$select .= ", '".str_replace(array('{','}'),array("' || COALESCE(".$this->ESCAPE_CHAR , $this->ESCAPE_CHAR.", '') || '"),str_replace("'","\\'", $related_field_title))."' as ".$this->protect_identifiers($field_name_hash);
    	}
    	else
    	{
	    	$select .= $related_table.'.'.$related_field_title.' as '.$field_name_hash;
    	}

    	$this->db->select($select,false);
        
        $where = false;
        if($where_clause!==null){
        foreach($where_clause as $w=>$z)
        {
            if(count(explode(" ",$w))>1)$where = true;
        }
    	if(!$where)
    		$this->db->where($where_clause);
        elseif($where)
            $this->db->where($where_clause,'',FALSE);
        }
        
        

    	if($limit !== null)
    		$this->db->limit($limit);

    	if($search_like !== null)
    		$this->db->having($this->$field_name_hash." LIKE '%".$this->db->escape_like_str($search_like)."%'");

    	$order_by !== null
    		? $this->db->order_by($order_by)
    		: $this->db->order_by($field_name_hash);

    	$results = $this->db->get($related_table)->result();
        
    	foreach($results as $row)
    	{
    		$relation_array[$row->$related_primary_key] = $row->$field_name_hash;
    	}

    	return $relation_array;
    }
    
    function build_db_join_relation($related_table, $unique_name, $related_primary_key, $field_name){
        $this->db->join($related_table.' as '.$unique_name , $unique_name.'.'.$related_primary_key.' = '. $this->table_name.'.'.$field_name,'left');
    }
}
