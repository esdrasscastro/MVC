<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 15/09/2016
 */

namespace Modelo;

abstract class DbModelo implements InterfaceDbModelo
{
    public $tableName;
    public $tableView;
    public $prefix;
    protected $results;
    protected $rowCount;
    protected $classname;

    private function __construct(){}

    public function pegar($whr='', array $bind = array(), $join='', $field='*', $limit=0, $offset=0, $orderby='', $order='')
    {
        if(empty($whr) and !empty($bind)) return $this;

        if(!empty($order)){
            $whr .= " ORDER BY :orderby :order ";
            $bind[':orderby'] = $orderby;
            $bind[':order'] = $order;
        }

        if($limit > 0 and is_numeric($limit)){
            $whr .= "LIMIT {$limit} ";
        }
        if($offset > 0 and is_numeric($offset)){
            $whr .= "OFFSET {$offset}";
        }

        $rs = \Lib\Connection::select($this->tableView, $whr, $bind, $join, $field, $this->classname);

        if($rs){
            $this->results = $rs->results;
            $this->rowCount = $rs->rowCount;
        }

        return $this;
    }

    public abstract function adicionar();

    public abstract function editar();

    public abstract function deletar();

    public abstract function comparar();

    public function getErrorInfo()
    {
        return \Lib\Connection::errorInfo();
    }

    public function semCaracteresEspeciais($value='')
    {
        $value = filter_var($value, FILTER_SANITIZE_STRING);
        $value = preg_replace("/[^a-zA-Z]/", "", $value);
        return $value;
    }

    public function alterar(array $data = array(), $instruction='', array $bind=array())
    {
        return \Lib\Connection::update($this->tableName, $data, $instruction, $bind);
    }

    public function apagar($instruction='', array $bind=array())
    {
        return \Lib\Connection::delete($this->tableName, $instruction, $bind);
    }

    public function beginTransaction(){
        \Lib\Connection::beginTransaction();
        return $this;
    }

    public function commit()
    {
        \Lib\Connection::commit();
        return $this;
    }

    public function rollBack()
    {
        \Lib\Connection::rollBack();
        return $this;
    }

    public function inserir($dados)
    {
        return \Lib\Connection::insert($this->tableName, $dados);
    }

    public function lastInsertId()
    {
        return \Lib\Connection::lastInsertId();
    }

    public abstract function despublicar();

    public abstract function publicar();

    public function setTableName($string = '')
    {
        $string = filter_var($string, FILTER_SANITIZE_STRING);
        if(empty($string)) throw new \InvalidArgumentException("O nome da tabela não pode ser definido como vazio!");
        else $this->tableName = $string;
    }

    public function setTableView($string = '')
    {
        $string = filter_var($string, FILTER_SANITIZE_STRING);
        if(empty($string)) throw new \InvalidArgumentException("O nome da tabela view não pode ser definido como vazio!");
        else $this->tableView = $string;
    }

    public function results($singleResult=true)
    {
        if($singleResult) return $this->results;
        else {
            if($this->rowCount() <= 1) return [$this->results];
            else return $this->results;
        }
    }

    public function rowCount()
    {
        if($this->rowCount <= 0) return 0;
        else return $this->rowCount;
    }
}