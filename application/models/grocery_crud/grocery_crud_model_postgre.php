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
    
     function build_relation_n_n_subquery($field, $selection_table, $relation_table, $primary_key_alias_to_selection_table, $primary_key_selection_table, $primary_key_alias_to_this_table, $field_name){
        return "(SELECT GROUP_CONCAT(DISTINCT ".$this->protect_identifiers($field).") FROM ".$this->protect_identifiers($selection_table)
                    ." LEFT JOIN ".$this->protect_identifiers($relation_table)." ON ".$this->protect_identifiers($relation_table.".".$primary_key_alias_to_selection_table)." = ".$this->protect_identifiers($selection_table.".".$primary_key_selection_table)
                    ." WHERE ".$this->protect_identifiers($relation_table.".".$primary_key_alias_to_this_table)." = ".$this->table_name.".".$this->get_primary_key($this->table_name)." GROUP BY ".$this->protect_identifiers($relation_table.".".$primary_key_alias_to_this_table).") AS ".$this->protect_identifiers($field_name);
    }       
    
    protected function relation_n_n_queries($select)
    {
    	$this_table_primary_key = $this->get_primary_key();
    	foreach($this->relation_n_n as $relation_n_n)
    	{
    		list($field_name, $relation_table, $selection_table, $primary_key_alias_to_this_table,
    					$primary_key_alias_to_selection_table, $title_field_selection_table, $priority_field_relation_table) = array_values((array)$relation_n_n);

    		$primary_key_selection_table = $this->get_primary_key($selection_table);

	    	$field = "";
	    	$use_template = strpos($title_field_selection_table,'{') !== false;
	    	$field_name_hash = $this->_unique_field_name($title_field_selection_table);
	    	if($use_template)
	    	{
	    		$title_field_selection_table = str_replace(" ", "&nbsp;", $title_field_selection_table);
	    		$field .= "CONCAT('".str_replace(array('{','}'),array("',COALESCE(",", ''),'"),str_replace("'","\\'",$title_field_selection_table))."')";
	    	}
	    	else
	    	{
	    		$field .= "$selection_table.$title_field_selection_table";
	    	}

    		//Sorry Codeigniter but you cannot help me with the subquery!
    		/*$select .= ", (SELECT GROUP_CONCAT(DISTINCT $field) FROM $selection_table "
    			."LEFT JOIN $relation_table ON $relation_table.$primary_key_alias_to_selection_table = $selection_table.$primary_key_selection_table "
    			."WHERE $relation_table.$primary_key_alias_to_this_table = {$this->table_name}.$this_table_primary_key GROUP BY $relation_table.$primary_key_alias_to_this_table) AS $field_name";*/
    	}

    	return $select;
    }
}
