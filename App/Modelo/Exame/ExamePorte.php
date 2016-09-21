<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 15/09/2016
 */

namespace Modelo\Exame;


use Modelo\DbModelo;

class ExamePorte extends DbModelo
{
    /** @var  integer */
    private $exameporte_id;
    /** @var  string */
    private $exameporte_valor;
    /** @var  boolean */
    private $exameporte_publico;

    function __construct(){
        $this->tableName = "exame_porte";
        $this->tableView = "exame_porte";
        $this->prefix = $this->semCaracteresEspeciais($this->tableName);
        $this->classname = get_class();

    }

    public function adicionar()
    {
        // TODO: Implement adicionar() method.
    }

    public function editar()
    {
        // TODO: Implement editar() method.
    }

    public function deletar()
    {
        // TODO: Implement deletar() method.
    }

    public function comparar()
    {
        $bind = array(
            ':id'=>$this->getExameporteId(),
            ':valor'=>$this->getExameporteValor(),
            ':publico'=>$this->getExameportePublico()
        );
        $comparar = new self;
        $comparar->pegar($this->prefix."_id=:id AND ".$this->prefix."_valor=:valor AND ".$this->prefix."_publico=:publico", $bind);
        if($comparar->rowCount() > 0) return true;
        else return false;
    }

    public function despublicar()
    {
        return parent::alterar(array($this->prefix."_publico"=>false), $this->prefix."_id=:id", array(':id'=>$this->getExameporteId()));
    }

    public function publicar()
    {
        return parent::alterar(array($this->prefix."_publico"=>true), $this->prefix."_id=:id", array(':id'=>$this->getExameporteId()));
    }

    /**
     * @return int
     */
    public function getExameporteId()
    {
        return $this->exameporte_id;
    }

    /**
     * @param int $exameporte_id
     * @return ExamePorte
     */
    public function setExameporteId($exameporte_id)
    {
        $this->exameporte_id = $exameporte_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getExameporteValor()
    {
        return $this->exameporte_valor;
    }

    /**
     * @param string $exameporte_valor
     * @return ExamePorte
     */
    public function setExameporteValor($exameporte_valor)
    {
        $this->exameporte_valor = $exameporte_valor;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getExameportePublico()
    {
        return $this->exameporte_publico;
    }

    /**
     * @param boolean $exameporte_publico
     * @return ExamePorte
     */
    public function setExameportePublico($exameporte_publico)
    {
        $this->exameporte_publico = $exameporte_publico;
        return $this;
    }


}