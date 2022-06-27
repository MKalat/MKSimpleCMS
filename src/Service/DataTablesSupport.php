<?php

namespace App\Service;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Types\Type;

class DataTablesSupport
{
    
    public function __construct()
    {
    }
    
    public function limitData($req)
    {
        $limit = '';
        if (isset($req['start']) && $req['length'] != -1) {
            $limit = "LIMIT ".intval($req['start']).",".intval($req['length']);
        }
        return $limit;
    }
    
    public function filterData($request, $columns, $addFilterPresent = false)
    {
        $globalSearch = array();
        $columnSearch = array();
        $dtColumns = $this->pluck($columns, 'dt');
        if (isset($request['search']) && $request['search']['value'] != '') {
            $str = $request['search']['value'];
                        $str = filter_var($str, FILTER_SANITIZE_STRING);
            for ($i=0, $ien=count($request['columns']); $i<$ien; $i++) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[ $columnIdx ];
                if ($requestColumn['searchable'] == 'true') {
                    $binding =  '%'.$str.'%';
                    $globalSearch[] = "".$column['db']." LIKE '".$binding."'";
                }
            }
        }
        // Individual column filtering
        if (isset($request['columns'])) {
            for ($i=0, $ien=count($request['columns']); $i<$ien; $i++) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[ $columnIdx ];
                $str = $requestColumn['search']['value'];
                                $str = filter_var($str, FILTER_SANITIZE_STRING);
                if ($requestColumn['searchable'] == 'true' &&
                 $str != '') {
                    $binding =  '%'.$str.'%';
                    $columnSearch[] = "".$column['db']." LIKE '".$binding."'";
                }
            }
        }
        // Combine the filters into a single string
        $where = '';
        if (count($globalSearch)) {
            $where = '('.implode(' OR ', $globalSearch).')';
        }
        if (count($columnSearch)) {
            $where = $where === '' ?
                implode(' AND ', $columnSearch) :
                $where .' AND '. implode(' AND ', $columnSearch);
        }
        if ($where !== '') {
            if (!$addFilterPresent) {
                $where = 'WHERE '.$where;
            } else {
                $where = 'AND '.$where;
            }
        }
        return $where;
    }
        
    public function orderData($request, $columns)
    {
        $order = '';
        if (isset($request['order']) && count($request['order'])) {
            $orderBy = array();
            $dtColumns = self::pluck($columns, 'dt');
            for ($i=0, $ien=count($request['order']); $i<$ien; $i++) {
                // Convert the column index into the column data property
                $columnIdx = intval($request['order'][$i]['column']);
                $requestColumn = $request['columns'][$columnIdx];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[ $columnIdx ];
                if ($requestColumn['orderable'] == 'true') {
                    $dir = $request['order'][$i]['dir'] === 'asc' ?
                        'ASC' :
                        'DESC';
                    $orderBy[] = ''.$column['db'].' '.$dir;
                }
            }
            if (count($orderBy)) {
                $order = 'ORDER BY '.implode(', ', $orderBy);
            }
        }
        return $order;
    }
        
    private function pluck($a, $prop)
    {
        $out = array();
        for ($i=0, $len=count($a); $i<$len; $i++) {
            $out[] = $a[$i][$prop];
        }
        return $out;
    }
        
    public function simple($request, Connection $conn, $table, $primaryKey, $columns)
    {
        $bindings = array();
        
        // Build the SQL query string from the request
        $limit = $this->limitData($request, $columns);
        $order = $this->orderData($request, $columns);
        $where = $this->filterDataBinded($request, $columns, $bindings);
        // Main query to actually get the data
        $data = $this->sql_exec(
            $conn,
            $bindings,
            "SELECT ".implode(", ", $this->pluck($columns, 'db'))."
			 FROM $table
			 $where
			 $order
			 $limit"
        );
        // Data set length after filtering
        $resFilterLength = $this->sql_exec(
            $conn,
            $bindings,
            "SELECT COUNT({$primaryKey})
			 FROM   $table
			 $where"
        );
        $recordsFiltered = $resFilterLength[0];
        // Total data set length
        $resTotalLength = $this->sql_exec(
            $conn,
            "SELECT COUNT({$primaryKey})
			 FROM   $table"
        );
        $recordsTotal = $resTotalLength[0];
        /*
     * Output
     */
        return array(
        "draw"            => isset($request['draw']) ?
            intval($request['draw']) :
            0,
        "recordsTotal"    => intval($recordsTotal),
        "recordsFiltered" => intval($recordsFiltered),
        "data"            => $this->data_output($columns, $data)
        );
    }
        
    private function sql_exec($db, $bindings, $sql = null)
    {
        // Argument shifting
        if ($sql === null) {
            $sql = $bindings;
        }
        $stmt = $db->prepare($sql);
        //echo $sql;
        // Bind parameters
        if (is_array($bindings)) {
            for ($i=0, $ien=count($bindings); $i<$ien; $i++) {
                $binding = $bindings[$i];
                $stmt->bindValue($binding['key'], $binding['val'], $binding['type']);
            }
        }
        $stmt->execute();
        
        // Return all
        return $stmt->fetchAll();
    }
        
    private function data_output($columns, $data)
    {
        $out = array();
        for ($i=0, $ien=count($data); $i<$ien; $i++) {
            $row = array();
            for ($j=0, $jen=count($columns); $j<$jen; $j++) {
                $column = $columns[$j];
                // Is there a formatter?
                if (isset($column['formatter'])) {
                    $row[ $column['dt'] ] = $column['formatter']($data[$i][ $column['db'] ], $data[$i]);
                } else {
                    $row[ $column['dt'] ] = $data[$i][ $columns[$j]['db'] ];
                }
            }
            $out[] = $row;
        }
        return $out;
    }
        
    private function filterDataBinded($request, $columns, &$bindings)
    {
        $globalSearch = array();
        $columnSearch = array();
        $dtColumns = $this->pluck($columns, 'dt');
        if (isset($request['search']) && $request['search']['value'] != '') {
            $str = $request['search']['value'];
            for ($i=0, $ien=count($request['columns']); $i<$ien; $i++) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[ $columnIdx ];
                if ($requestColumn['searchable'] == 'true') {
                    $binding = $this->bind($bindings, '%'.$str.'%', Type::getType('string'));
                    $globalSearch[] = "".$column['db']." LIKE ".$binding;
                }
            }
        }
        // Individual column filtering
        if (isset($request['columns'])) {
            for ($i=0, $ien=count($request['columns']); $i<$ien; $i++) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[ $columnIdx ];
                $str = $requestColumn['search']['value'];
                if ($requestColumn['searchable'] == 'true' &&
                 $str != '') {
                    $binding = $this->bind($bindings, '%'.$str.'%', Type::getType('string'));
                    $columnSearch[] = "".$column['db']." LIKE ".$binding;
                }
            }
        }
        // Combine the filters into a single string
        $where = '';
        if (count($globalSearch)) {
            $where = '('.implode(' OR ', $globalSearch).')';
        }
        if (count($columnSearch)) {
            $where = $where === '' ?
                implode(' AND ', $columnSearch) :
                $where .' AND '. implode(' AND ', $columnSearch);
        }
        if ($where !== '') {
            $where = 'WHERE '.$where;
        }
        return $where;
    }
        
    private function bind(&$a, $val, $type)
    {
        $key = ':binding_'.count($a);
        $a[] = array(
        'key' => $key,
        'val' => $val,
        'type' => $type
        );
        return $key;
    }
}
