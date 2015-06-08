<?php
class grocery_CRUD_Generic_Model  extends grocery_CRUD_Model  {

    public $ESCAPE_CHAR = '"';
    public $CAPABLE_CONCAT = TRUE;

    public function __construct(){
        parent::__construct();
        $test = $this->protect_identifiers('t');
        $first_char = substr($test,0,1);
        if($first_char !== 't'){
            $this->ESCAPE_CHAR = $first_char;
        }
    }

    public function protect_identifiers($value)
    {
        return $value;
    }


    // rather than mess around with this everytime, it is better to build a function for this.
    public function build_concat_from_template($template, $prefix_replacement='', $suffix_replacement='', $as=NULL){
        if($this->CAPABLE_CONCAT){
            // if CONCAT is possible in the current driver
            $concat_str =
                "CONCAT('".
                str_replace(
                    array(
                        "{",
                        "}"
                    ),
                    array(
                        "',COALESCE(".$prefix_replacement,
                        $suffix_replacement.", ''),'"
                    ),
                    str_replace("'","\\'",$template)
                ).
                "')";

        }else{
            // if CONCAT is impossible in the current driver, use || instead
            $concat_str =
                "('".
                str_replace(
                    array(
                        "{",
                        "}"
                    ),
                    array(
                        "' || COALESCE(".$replacement,
                        ", '') || '"
                    ),
                    str_replace("'","\\'",$template)
                ).
                "')";
        }
        if(isset($as)){
            $concat_str .= " as ".$as;
        }

    }

    function get_list()
    {
        if($this->table_name === null)
                return false;

        $select = $this->protect_identifiers("{$this->table_name}").".*";
        $additional_fields = array();  
        if(!empty($this->relation))
        {
                foreach($this->relation as $relation)
                {
                        list($field_name , $related_table , $related_field_title) = $relation;
                        $unique_join_name = $this->_unique_join_name($field_name);
                        $unique_field_name = $this->_unique_field_name($field_name);

                        if(strstr($related_field_title,'{'))
                        {
                            $select .= ", CONCAT('".str_replace(array('{','}'),array("',COALESCE(".$this->protect_identifiers($unique_join_name).".".$this->ESCAPE_CHAR, $this->ESCAPE_CHAR.", ''),'"),str_replace("'","\\'",$related_field_title))."') as ".$this->protect_identifiers($unique_field_name);
                        }
                        else
                        {
                                if(!strstr($related_field_title,'.'))
                                $select .= ', ' . $this->protect_identifiers($unique_join_name. '.'. $related_field_title).' AS '. $this->protect_identifiers($unique_field_name);
                                else{
                                    $select .= ', ' . $this->protect_identifiers($related_field_title);
                                    $rel = explode('.',$related_field_title);
                                    $this->db->join($rel[0],"$rel[0].id = $this->table_name.$rel[0]_id");
                                }
                        }

                        if($this->field_exists($related_field_title)){
                            $additional_fields[$this->table_name. '.'. $related_field_title] = $related_field_title;    			   
                        }
                }
        }
        if(!empty($this->relation_n_n))
        {
            $select = $this->relation_n_n_queries($select);
        }                    
        $this->db->select($select, false);

        $results = $this->db->get($this->table_name)->result();
        for($i=0; $i<count($results); $i++){
            foreach($additional_fields as $alias=>$real_field){
                $results[$i]->{$alias} = $results[$i]->{$real_field};
            }
        }
        return $results;
    }            

    function get_total_results()
    {
        //set_relation_n_n special queries. We prefer sub queries from a simple join for the relation_n_n as it is faster and more stable on big tables.
        $select = $this->protect_identifiers("{$this->table_name}").".*";
        $additional_fields = array();  
        if(!empty($this->relation))
        {
                foreach($this->relation as $relation)
                {
                        list($field_name , $related_table , $related_field_title) = $relation;
                        $unique_join_name = $this->_unique_join_name($field_name);
                        $unique_field_name = $this->_unique_field_name($field_name);

                        if(strstr($related_field_title,'{'))
                        {
                            $select .= ", CONCAT('".str_replace(array('{','}'),array("',COALESCE(".$this->protect_identifiers($unique_join_name).".".$this->ESCAPE_CHAR, $this->ESCAPE_CHAR.", ''),'"),str_replace("'","\\'",$related_field_title))."') as ".$this->protect_identifiers($unique_field_name);
                        }
                        else
                        {
                                if(!strstr($related_field_title,'.'))
                                $select .= ', ' . $this->protect_identifiers($unique_join_name. '.'. $related_field_title).' AS '. $this->protect_identifiers($unique_field_name);
                                else{
                                    $select .= ', ' . $this->protect_identifiers($related_field_title);
                                    $rel = explode('.',$related_field_title);
                                    $this->db->join($rel[0],"$rel[0].id = $this->table_name.$rel[0]_id");
                                }
                        }

                        if($this->field_exists($related_field_title)){
                            $additional_fields[$this->table_name. '.'. $related_field_title] = $related_field_title;    			   
                        }
                }
        }
        if(!empty($this->relation_n_n))
        {
            $select = $this->relation_n_n_queries($select);
        }                    
        $this->db->select($select, false);
        return $this->db->get($this->table_name)->num_rows;
    }

    function join_relation($field_name , $related_table , $related_field_title)
    {
                $related_primary_key = $this->get_primary_key($related_table);

                if($related_primary_key !== false)
                {
                        $unique_name = $this->_unique_join_name($field_name);                        
                        
                        $this->build_db_join_relation($related_table, $unique_name, $related_primary_key, $field_name);

                        $this->relation[$field_name] = array($field_name , $related_table , $related_field_title);

                        return true;
                }

        return false;
    }        

    function get_field_types_basic_table()
    {
        $db_field_types = array();
        foreach($this->get_field_types($this->table_name) as $db_field_type)
        {
            $db_type = $db_field_type->type;  
            $length = $db_field_type->max_length;
            $db_field_types[$db_field_type->name]['db_max_length'] = $length;
            $db_field_types[$db_field_type->name]['db_type'] = $db_type;
            $db_field_types[$db_field_type->name]['db_null'] = true;
            $db_field_types[$db_field_type->name]['db_extra'] = $db_field_type->primary_key==1 || $this->primary_key==$db_field_type->name?'auto_increment':'';
        }

        $results = $this->get_field_types($this->table_name);
        foreach($results as $num => $row)
        {
                $row = (array)$row;
                $results[$num] = (object)( array_merge($row, $db_field_types[$row['name']])  );
        }
        return $results;
    }

    function get_field_types($table_name)
    {
        $results = $this->db->field_data($table_name);
        foreach($results as $num => $row)
        {
            $row = (array)$row;
            if(!array_key_exists('primary_key', $row)){
                $results[$num]->primary_key = 0;
            }            
        }
        return $results;
    }

    function db_insert($post_array)
    {
        $insert = $this->db->insert($this->table_name,$post_array);                    
        return $insert?$this->db->insert_id():false;
    }

    function db_update($post_array, $primary_key_value)
    {
        $primary_key_field = $this->get_primary_key();            
        return $this->db->update($this->table_name,$post_array, array( $primary_key_field => $primary_key_value));
    }

    function build_db_join_relation($related_table, $unique_name, $related_primary_key, $field_name){
        $field_name = !strstr($field_name,'.')?$this->table_name.'.'.$field_name:$field_name;
        $this->db->join($this->protect_identifiers($related_table).' as '.$this->protect_identifiers($unique_name) , $unique_name.'.'.$related_primary_key.' = '. $field_name,'left');
    }

    function build_relation_n_n_subquery($field, $selection_table, $relation_table, $primary_key_alias_to_selection_table, $primary_key_selection_table, $primary_key_alias_to_this_table, $field_name){
        return "(SELECT GROUP_CONCAT(DISTINCT ".$this->protect_identifiers($field).") FROM ".$this->protect_identifiers($selection_table)
                    ." LEFT JOIN ".$this->protect_identifiers($relation_table)." ON ".$this->protect_identifiers($relation_table.".".$primary_key_alias_to_selection_table)." = ".$this->protect_identifiers($selection_table.".".$primary_key_selection_table)
                    ." WHERE ".$this->protect_identifiers($relation_table.".".$primary_key_alias_to_this_table)." = ".$this->protect_identifiers($this->table_name.".".$this->get_primary_key($this->table_name))." GROUP BY ".$this->protect_identifiers($relation_table.".".$primary_key_alias_to_this_table).") AS ".$this->protect_identifiers($field_name);
    }

    function db_delete($primary_key_value)
    {
        $primary_key_field = $this->get_primary_key();

        if($primary_key_field === false)
                return false;

        return $this->db->delete($this->table_name,array( $primary_key_field => $primary_key_value));            
    }

    function field_exists($field,$table_name = null)
    {
        if(empty($table_name))
        {
                $table_name = $this->table_name;
        }

        // sqlite doesn't support this $this->db->field_exists($field,$table_name)
        $field_data_list = $this->db->field_data($table_name);
        foreach($field_data_list as $field_data){
            if($field_data->name == $field) return TRUE;
        }
        return FALSE;
    }

    function get_edit_values($primary_key_value)
    {
        $result = parent::get_edit_values($primary_key_value);
        // some driver like postgresql doesn't return string
        foreach($result as $key => $value) {
            $result->$key = (string)$value;
        }
        return $result;
    }

    function get_relation_array($field_name , $related_table , $related_field_title, $where_clause, $order_by, $limit = null, $search_like = null)
    {
        $relation_array = array();
        $field_name_hash = $this->_unique_field_name($field_name);

        $related_primary_key = $this->get_primary_key($related_table);

        $select = "$related_table.$related_primary_key, ";                        

        if(strstr($related_field_title,'{'))
        {
                $related_field_title = str_replace(" ", "&nbsp;", $related_field_title);
                $select .= "CONCAT('".str_replace(array('{','}'),array("',COALESCE(",", ''),'"),str_replace("'","\\'",$related_field_title))."') as $field_name_hash";
        }
        else
        {
                if(!strstr($related_field_title,'.')){
                    $select .= "$related_table.$related_field_title as $field_name_hash";
                }
                else{
                    $rel = explode('.',$related_field_title);
                    $this->db->join($rel[0],"$rel[0].id = $related_table.$rel[0]_id");
                    $select .= "$related_field_title as $field_name_hash";
                }
        }

        $this->db->select($select,false);
        if($where_clause !== null)
                $this->db->where($where_clause);

        if($where_clause !== null)
                $this->db->where($where_clause);

        if($limit !== null)
                $this->db->limit($limit);

        if($search_like !== null)
                $this->db->having("$field_name_hash LIKE '%".$this->db->escape_like_str($search_like)."%'");

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
    
    function where($key, $value = NULL, $escape = TRUE)
    {  
        if(!empty($value)){
            $key = $key=='id'?$this->table_name.'.'.$key:$key;
            $this->db->where($key, $value, $escape);
        }
    }

    function or_where($key, $value = NULL, $escape = TRUE)
    {
         if(!empty($value)){
           $key = $key=='id'?$this->table_name.'.'.$key:$key;
           $this->db->or_where( $key, $value, $escape);
          }
    }

    function like($field, $match = '', $side = 'both')
    {      
        if(!empty($match)){
            $field = $field=='id'?$this->table_name.'.'.$field:$field;
            $this->db->like($field, $match, $side);  
        }        
    }

    function or_like($field, $match = '', $side = 'both')
    {        
        if(!empty($match)){
            $field = $field=='id'?$this->table_name.'.'.$field:$field;
            $this->db->or_like($field, $match, $side);  
        }      
    }

}
