<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 15/09/2016
 */

namespace Modelo\Exame;


class ExameCategoria extends \Modelo\DbModelo
{
    /** @var  integer */
    private $examecategoria_id;
    /** @var  string */
    private $examecategoria_nome;
    /** @var  boolean */
    private $examecategoria_publico;
    /** @var  integer */
    private $examecategoriatipo_id;
    /** @var  string */
    private $examecategoriatipo_nome;
    /** @var  boolean */
    private $examecategoriatipo_publico;

    function __construct(){
        $this->tableName = "exame_categoria";
        $this->tableView = "view_exame_categoria";
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
            ':id'=>$this->getExamecategoriaId(),
            ':nome'=>$this->getExamecategoriaNome(),
            ':categoriatipoid'=>$this->getExamecategoriatipoId(),
            ':publico'=>$this->getExamecategoriaPublico()
        );

        $sql = $this->prefix."_id=:id AND "
            .$this->prefix."_nome=:nome AND "
            .$this->prefix."_examecategoriatipo_id=:categoriatipoid AND "
            .$this->prefix."_publico=:publico";

        $comparar = new self;
        $comparar->pegar($sql, $bind);
        if($comparar->rowCount() > 0) return true;
        else return false;
    }

    public function despublicar()
    {
        return parent::alterar(array($this->prefix."_publico"=>false), $this->prefix."_id=:id", array(':id'=>$this->getExamecategoriaId()));
    }

    public function publicar()
    {
        return parent::alterar(array($this->prefix."_publico"=>true), $this->prefix."_id=:id", array(':id'=>$this->getExamecategoriaId()));
    }

    /**
     * @return int
     */
    public function getExamecategoriaId()
    {
        return $this->examecategoria_id;
    }

    /**
     * @param int $examecategoria_id
     * @return ExameCategoria
     */
    public function setExamecategoriaId($examecategoria_id)
    {
        $this->examecategoria_id = $examecategoria_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getExamecategoriaNome()
    {
        return $this->examecategoria_nome;
    }

    /**
     * @param string $examecategoria_nome
     * @return ExameCategoria
     */
    public function setExamecategoriaNome($examecategoria_nome)
    {
        $this->examecategoria_nome = filter_var($examecategoria_nome, FILTER_SANITIZE_STRING);
        return $this;
    }

    /**
     * @return boolean
     */
    public function getExamecategoriaPublico()
    {
        return $this->examecategoria_publico;
    }

    /**
     * @param boolean $examecategoria_publico
     * @return ExameCategoria
     */
    public function setExamecategoriaPublico($examecategoria_publico)
    {
        $this->examecategoria_publico = $examecategoria_publico;
        return $this;
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
     * @return ExameCategoria
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
     * @return ExameCategoria
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
     * @return ExameCategoria
     */
    public function setExamecategoriatipoPublico($examecategoriatipo_publico)
    {
        $this->examecategoriatipo_publico = $examecategoriatipo_publico;
        return $this;
    }


}