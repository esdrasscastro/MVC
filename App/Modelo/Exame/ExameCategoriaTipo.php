<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 15/09/2016
 */

namespace Modelo\Exame;


use Modelo\DbModelo;

class ExameCategoriaTipo extends DbModelo
{
    /** @var  integer */
    private $examecategoriatipo_id;
    /** @var  string */
    private $examecategoriatipo_nome;
    /** @var  boolean */
    private $examecategoriatipo_publico;

    function __construct(){
        $this->tableName = "exame_categoria_tipo";
        $this->tableView = "exame_categoria_tipo";
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
            ':id'=>$this->getExamecategoriatipoId(),
            ':nome'=>$this->getExamecategoriatipoNome(),
            ':publico'=>$this->getExamecategoriatipoPublico()
        );

        $sql = $this->prefix."_id=:id AND "
            .$this->prefix."_nome=:nome AND "
            .$this->prefix."_publico=:publico";

        $comparar = new self;
        $comparar->pegar($sql, $bind);
        if($comparar->rowCount() > 0) return true;
        else return false;
    }

    public function despublicar()
    {
        return parent::alterar(array($this->prefix."_publico"=>false), $this->prefix."_id=:id", array(':id'=>$this->getExamecategoriatipoId()));
    }

    public function publicar()
    {
        return parent::alterar(array($this->prefix."_publico"=>true), $this->prefix."_id=:id", array(':id'=>$this->getExamecategoriatipoId()));
    }

    /**
     * @return int
     */
    public function getExamecategoriatipoId()
    {
        return $this->examecategoriatipo_id;
    }

    /**
     * @param int $examecategoriatipo_id
     * @return ExameCategoriaTipo
     */
    public function setExamecategoriatipoId($examecategoriatipo_id)
    {
        $this->examecategoriatipo_id = $examecategoriatipo_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getExamecategoriatipoNome()
    {
        return $this->examecategoriatipo_nome;
    }

    /**
     * @param string $examecategoriatipo_nome
     * @return ExameCategoriaTipo
     */
    public function setExamecategoriatipoNome($examecategoriatipo_nome)
    {
        $this->examecategoriatipo_nome = filter_var($examecategoriatipo_nome, FILTER_SANITIZE_STRING);
        return $this;
    }

    /**
     * @return boolean
     */
    public function getExamecategoriatipoPublico()
    {
        return $this->examecategoriatipo_publico;
    }

    /**
     * @param boolean $examecategoriatipo_publico
     * @return ExameCategoriaTipo
     */
    public function setExamecategoriatipoPublico($examecategoriatipo_publico)
    {
        $this->examecategoriatipo_publico = $examecategoriatipo_publico;
        return $this;
    }


}