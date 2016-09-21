<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 15/09/2016
 */

namespace Modelo;


class Procedimento extends DbModelo
{
    /** @var  integer */
    private $procedimento_id;
    /** @var  string */
    private $procedimento_nome;
    /** @var  boolean */
    private $procedimento_publico;

    function __construct(){
        $this->tableName = "procedimento";
        $this->tableView = "procedimento";
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
            ':id'=>$this->getProcedimentoId(),
            ':nome'=>$this->getProcedimentoNome(),
            ':publico'=>$this->getProcedimentoPublico()
        );
        $comparar = new self;
        $comparar->pegar($this->prefix."_id=:id AND ".$this->prefix."_nome=:nome AND ".$this->prefix."_publico=:publico", $bind);
        if($comparar->rowCount() > 0) return true;
        else return false;
    }

    public function despublicar()
    {
        return parent::alterar(array($this->prefix . "_publico"=>false), $this->prefix . "_id=:id", array(':id'=>$this->getProcedimentoId()));
    }

    public function publicar()
    {
        return parent::alterar(array($this->prefix . "_publico"=>true), $this->prefix . "_id=:id", array(':id'=>$this->getProcedimentoId()));
    }

    /**
     * @return int
     */
    public function getProcedimentoId()
    {
        return $this->procedimento_id;
    }

    /**
     * @param int $procedimento_id
     * @return Procedimento
     */
    public function setProcedimentoId($procedimento_id)
    {
        $this->procedimento_id = $procedimento_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getProcedimentoNome()
    {
        return $this->procedimento_nome;
    }

    /**
     * @param string $procedimento_nome
     * @return Procedimento
     */
    public function setProcedimentoNome($procedimento_nome)
    {
        $this->procedimento_nome = filter_var($procedimento_nome, FILTER_SANITIZE_STRING);
        return $this;
    }

    /**
     * @return boolean
     */
    public function getProcedimentoPublico()
    {
        return $this->procedimento_publico;
    }

    /**
     * @param boolean $procedimento_publico
     * @return Procedimento
     */
    public function setProcedimentoPublico($procedimento_publico)
    {
        $this->procedimento_publico = $procedimento_publico;
        return $this;
    }


}