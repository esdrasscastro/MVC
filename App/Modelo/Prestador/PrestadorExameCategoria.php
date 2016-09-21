<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 21/09/2016
 */

namespace Modelo\Prestador;

use Modelo\DbModelo;

class PrestadorExameCategoria extends DbModelo
{
    /** @var  integer */
    private $prestadorexamecategoria_examecategoria_id;
    /** @var  integer */
    private $prestadorexamecategoria_prestador_id;
    /** @var  boolean */
    private $prestadorexamecategoria_publico;

    function __construct(){
        $this->tableName = "prestador_exame_categoria";
        $this->tableView = "prestador_exame_categoria";
        $this->prefix = $this->semCaracteresEspeciais($this->tableName);
        $this->classname = get_class();
    }

    public function adicionar()
    {
        $dados = array();
        $dados['prestadorexamecategoria_examecategoria_id'] = $this->getPrestadorexamecategoriaExamecategoriaId();
        $dados['prestadorexamecategoria_prestador_id'] = $this->getPrestadorexamecategoriaPrestadorId();
        $dados = array_filter($dados);

        return parent::inserir($dados);
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
            ':peid'=>$this->getPrestadorexamecategoriaPrestadorId(),
            ':ppid'=>$this->getPrestadorexamecategoriaPrestadorId(),
            ':publico'=>$this->getPrestadorexamecategoriaPublico()
        );
        $comparar = new self;
        $comparar->pegar($this->prefix."_examecategoria_id=:peid AND ".$this->prefix."_prestador_id=:ppid AND ".$this->prefix."_publico=:publico", $bind);
        if($comparar->rowCount() > 0) return true;
        else return false;
    }

    public function despublicar()
    {
        return parent::alterar(array($this->prefix . "_publico"=>false), $this->prefix . "_examecategoria_id=:peid AND " . $this->prefix . "_prestador_id=:ppid", array(':peid'=>$this->getPrestadorexamecategoriaExamecategoriaId(), ':ppid'=>$this->getPrestadorexamecategoriaPrestadorId()));
    }

    public function publicar()
    {
        return parent::alterar(array($this->prefix . "_publico"=>true), $this->prefix . "_examecategoria_id=:peid AND " . $this->prefix . "_prestador_id=:ppid", array(':peid'=>$this->getPrestadorexamecategoriaExamecategoriaId(), ':ppid'=>$this->getPrestadorexamecategoriaPrestadorId()));
    }

    /**
     * @return int
     */
    public function getPrestadorexamecategoriaExamecategoriaId()
    {
        return $this->prestadorexamecategoria_examecategoria_id;
    }

    /**
     * @param int $prestadorexamecategoria_examecategoria_id
     * @return PrestadorExameCategoria
     */
    public function setPrestadorexamecategoriaExamecategoriaId($prestadorexamecategoria_examecategoria_id)
    {
        $this->prestadorexamecategoria_examecategoria_id = $prestadorexamecategoria_examecategoria_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrestadorexamecategoriaPrestadorId()
    {
        return $this->prestadorexamecategoria_prestador_id;
    }

    /**
     * @param int $prestadorexamecategoria_prestador_id
     * @return PrestadorExameCategoria
     */
    public function setPrestadorexamecategoriaPrestadorId($prestadorexamecategoria_prestador_id)
    {
        $this->prestadorexamecategoria_prestador_id = $prestadorexamecategoria_prestador_id;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getPrestadorexamecategoriaPublico()
    {
        return $this->prestadorexamecategoria_publico;
    }

    /**
     * @param boolean $prestadorexamecategoria_publico
     * @return PrestadorExameCategoria
     */
    public function setPrestadorexamecategoriaPublico($prestadorexamecategoria_publico)
    {
        $this->prestadorexamecategoria_publico = $prestadorexamecategoria_publico;
        return $this;
    }


}