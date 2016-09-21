<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 15/09/2016
 */

namespace Modelo;


class Especialidade extends DbModelo
{
    /** @var  integer */
    private $especialidade_id;
    /** @var  string */
    private $especialidade_nome;
    /** @var  boolean */
    private $especialidade_publico;

    function __construct(){
        $this->tableName = "especialidade";
        $this->tableView = "view_especialidade";
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
            ':id'=>$this->getEspecialidadeId(),
            ':nome'=>$this->getEspecialidadeNome(),
            ':publico'=>$this->getEspecialidadePublico()
        );
        $comparar = new self;
        $comparar->pegar($this->prefix."_id=:id AND ".$this->prefix."_nome=:nome AND ".$this->prefix."_publico=:publico", $bind);
        if($comparar->rowCount() > 0) return true;
        else return false;
    }

    public function despublicar()
    {
        return parent::alterar(array($this->prefix."_publico"=>false), $this->prefix."_id=:id", array(':id'=>$this->getEspecialidadeId()));
    }

    public function publicar()
    {
        return parent::alterar(array($this->prefix."_publico"=>true), $this->prefix."_id=:id", array(':id'=>$this->getEspecialidadeId()));
    }

    /**
     * @return int
     */
    public function getEspecialidadeId()
    {
        return $this->especialidade_id;
    }

    /**
     * @param int $especialidade_id
     * @return Especialidade
     */
    public function setEspecialidadeId($especialidade_id)
    {
        $this->especialidade_id = $especialidade_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getEspecialidadeNome()
    {
        return $this->especialidade_nome;
    }

    /**
     * @param string $especialidade_nome
     * @return Especialidade
     */
    public function setEspecialidadeNome($especialidade_nome)
    {
        $this->especialidade_nome = filter_var($especialidade_nome, FILTER_SANITIZE_STRING);
        return $this;
    }

    /**
     * @return boolean
     */
    public function getEspecialidadePublico()
    {
        return $this->especialidade_publico;
    }

    /**
     * @param boolean $especialidade_publico
     * @return Especialidade
     */
    public function setEspecialidadePublico($especialidade_publico)
    {
        $this->especialidade_publico = $especialidade_publico;
        return $this;
    }


}