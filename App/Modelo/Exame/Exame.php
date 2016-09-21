<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 15/09/2016
 */

namespace Modelo\Exame;


class Exame extends \Modelo\DbModelo
{
    /** @var  integer */
    private $exame_id;
    /** @var  string */
    private $exame_nome;
    /** @var  int */
    private $exame_codigo;
    /** @var  double */
    private $exame_fracao_porte;
    /** @var  double */
    private $exame_uco;
    /** @var  double */
    private $exame_filme;
    /** @var  double */
    private $exame_extra;
    /** @var  boolean */
    private $exame_publico;
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
    /** @var  integer */
    private $exameporte_id;
    /** @var  string */
    private $exameporte_nome;
    /** @var  boolean */
    private $exameporte_publico;

    function __construct(){
        $this->tableName = "exame";
        $this->tableView = "view_exame";
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
            ':eid'=>$this->getExameId(),
            ':ecid'=>$this->getExamecategoriatipoId(),
            ':epid'=>$this->getExameporteId(),
            ':enome'=>$this->getExameNome(),
            ':ecodigo'=>$this->getExameCodigo(),
            ':efracaoporte'=>$this->getExameFracaoPorte(),
            ':euco'=>$this->getExameUco(),
            ':efilme'=>$this->getExameFilme(),
            ':eextra'=>$this->getExameExtra(),
            ':epublico'=>$this->getExamePublico()
        );

        $sql = $this->prefix."_id=:eid AND "
            .$this->prefix."_examecategoria_id=:ecid AND "
            .$this->prefix."_exameporte_id=:epid AND "
            .$this->prefix."_nome=:enome AND "
            .$this->prefix."_codigo=:ecodigo AND "
            .$this->prefix."_fracao_porte=:efracaoporte AND "
            .$this->prefix."_uco=:euco AND "
            .$this->prefix."_filme=:efilme AND "
            .$this->prefix."_extra=:eextra AND "
            .$this->prefix."_publico=:epublico";

        $comparar = new self;
        $comparar->pegar($sql, $bind);
        if($comparar->rowCount() > 0) return true;
        else return false;
    }

    public function despublicar()
    {
        return parent::alterar(array($this->prefix."_publico"=>false), $this->prefix."_id=:id", array(':id'=>$this->getExameId()));
    }

    public function publicar()
    {
        return parent::alterar(array($this->prefix."_publico"=>true), $this->prefix."_id=:id", array(':id'=>$this->getExameId()));
    }

    /**
     * @return int
     */
    public function getExameId()
    {
        return $this->exame_id;
    }

    /**
     * @param int $exame_id
     * @return Exame
     */
    public function setExameId($exame_id)
    {
        $this->exame_id = $exame_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getExameNome()
    {
        return $this->exame_nome;
    }

    /**
     * @param string $exame_nome
     * @return Exame
     */
    public function setExameNome($exame_nome)
    {
        $this->exame_nome = filter_var($exame_nome, FILTER_SANITIZE_STRING);
        return $this;
    }

    /**
     * @return int
     */
    public function getExameCodigo()
    {
        return $this->exame_codigo;
    }

    /**
     * @param int $exame_codigo
     * @return Exame
     */
    public function setExameCodigo($exame_codigo)
    {
        $this->exame_codigo = $exame_codigo;
        return $this;
    }

    /**
     * @return float
     */
    public function getExameFracaoPorte()
    {
        return $this->exame_fracao_porte;
    }

    /**
     * @param float $exame_fracao_porte
     * @return Exame
     */
    public function setExameFracaoPorte($exame_fracao_porte)
    {
        $this->exame_fracao_porte = $exame_fracao_porte;
        return $this;
    }

    /**
     * @return float
     */
    public function getExameUco()
    {
        return $this->exame_uco;
    }

    /**
     * @param float $exame_uco
     * @return Exame
     */
    public function setExameUco($exame_uco)
    {
        $this->exame_uco = $exame_uco;
        return $this;
    }

    /**
     * @return float
     */
    public function getExameFilme()
    {
        return $this->exame_filme;
    }

    /**
     * @param float $exame_filme
     * @return Exame
     */
    public function setExameFilme($exame_filme)
    {
        $this->exame_filme = $exame_filme;
        return $this;
    }

    /**
     * @return float
     */
    public function getExameExtra()
    {
        return $this->exame_extra;
    }

    /**
     * @param float $exame_extra
     * @return Exame
     */
    public function setExameExtra($exame_extra)
    {
        $this->exame_extra = $exame_extra;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getExamePublico()
    {
        return $this->exame_publico;
    }

    /**
     * @param boolean $exame_publico
     * @return Exame
     */
    public function setExamePublico($exame_publico)
    {
        $this->exame_publico = $exame_publico;
        return $this;
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
     * @return Exame
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
     * @return Exame
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
     * @return Exame
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
     * @return Exame
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
     * @return Exame
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
     * @return Exame
     */
    public function setExamecategoriatipoPublico($examecategoriatipo_publico)
    {
        $this->examecategoriatipo_publico = $examecategoriatipo_publico;
        return $this;
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
     * @return Exame
     */
    public function setExameporteId($exameporte_id)
    {
        $this->exameporte_id = $exameporte_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getExameporteNome()
    {
        return $this->exameporte_nome;
    }

    /**
     * @param string $exameporte_nome
     * @return Exame
     */
    public function setExameporteNome($exameporte_nome)
    {
        $this->exameporte_nome = filter_var($exameporte_nome, FILTER_SANITIZE_STRING);
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
     * @return Exame
     */
    public function setExameportePublico($exameporte_publico)
    {
        $this->exameporte_publico = $exameporte_publico;
        return $this;
    }


}