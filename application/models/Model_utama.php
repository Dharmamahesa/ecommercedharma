<?php  
class Model_utama extends CI_Model {

    public function view($table) {
        return $this->db->get($table);
    }

    public function view_where($table, $data) {
        $this->db->where($data);
        return $this->db->get($table);
    }

    public function view_ordering_limit($table, $order, $ordering, $limit, $offset) {
        $this->db->select('*');
        $this->db->order_by($order, $ordering);
        $this->db->limit($offset, $limit);
        return $this->db->get($table);
    }

    public function view_where_ordering_limit($table, $data, $order, $ordering, $limit, $offset) {
        $this->db->where($data);
        $this->db->order_by($order, $ordering);
        $this->db->limit($offset, $limit);
        return $this->db->get($table);
    }

    public function view_single($table, $data, $order, $ordering) {
        $this->db->where($data);
        $this->db->order_by($order, $ordering);
        return $this->db->get($table);
    }

    public function view_join($table1, $table2, $field, $order, $ordering, $limit, $offset) {
        $this->db->select('*');
        $this->db->from($table1);
        $this->db->join($table2, "$table1.$field = $table2.$field");
        $this->db->order_by($order, $ordering);
        $this->db->limit($offset, $limit);
        return $this->db->get();
    }

    public function view_join_one($table1, $table2, $field, $where, $order, $ordering, $limit, $offset) {
        $this->db->select('*');
        $this->db->from($table1);
        $this->db->join($table2, "$table1.$field = $table2.$field");
        $this->db->where($where);
        $this->db->order_by($order, $ordering);
        $this->db->limit($offset, $limit);
        return $this->db->get();
    }

    public function view_joinn($table1, $table2, $table3, $field, $field1, $order, $ordering, $limit, $offset) {
        $this->db->select('*');
        $this->db->from($table1);
        $this->db->join($table2, "$table1.$field = $table2.$field");
        $this->db->join($table3, "$table1.$field1 = $table3.$field1");
        $this->db->order_by($order, $ordering);
        $this->db->limit($offset, $limit);
        return $this->db->get();
    }

    public function view_join_two($table1, $table2, $table3, $field, $field1, $where, $order, $ordering, $limit, $offset) {
        $this->db->select('*');
        $this->db->from($table1);
        $this->db->join($table2, "$table1.$field = $table2.$field");
        $this->db->join($table3, "$table1.$field1 = $table3.$field1");
        $this->db->where($where);
        $this->db->order_by($order, $ordering);
        $this->db->limit($offset, $limit);
        return $this->db->get();
    }
}
?>