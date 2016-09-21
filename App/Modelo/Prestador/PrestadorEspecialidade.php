<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 21/09/2016
 */

namespace Modelo\Prestador;


use Modelo\DbModelo;

class PrestadorEspecialidade extends DbModelo
{
    /** @var  integer */
    private $prestadorespecialidade_especialidade_id;
    /** @var  integer */
    private $prestadorespecialidade_prestador_id;
    /** @var  boolean */
    private $prestadorespecialidade_publico;

    function __construct(){
        $this->tableName = "prestador_especialidade";
        $this->tableView = "prestador_especialidade";
        $this->prefix = $this->semCaracteresEspeciais($this->tableName);
        $this->classname = get_class();
    }

    public function adicionar()
    {
        $dados = array();
        $dados['prestadorespecialidade_prestador_id'] = $this->getPrestadorespecialidadePrestadorId();
        $dados['prestadorespecialidade_especialidade_id'] = $this->getPrestadorespecialidadeEspecialidadeId();
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
            ':peid'=>$this->getPrestadorespecialidadeEspecialidadeId(),
            ':ppid'=>$this->getPrestadorespecialidadePrestadorId(),
            ':publico'=>$this->getPrestadorespecialidadePublico()
        );
        $comparar = new self;
        $comparar->pegar($this->prefix."_especialidade_id=:peid AND ".$this->prefix."_prestador_id=:ppid AND ".$this->prefix."_publico=:publico", $bind);
        if($comparar->rowCount() > 0) return true;
        else return false;
    }

    public function despublicar()
    {
        return parent::alterar(array($this->prefix . "_publico"=>false), $this->prefix . "_especialidade_id=:peid AND " . $this->prefix . "_prestador_id=:ppid", array(':peid'=>$this->getPrestadorespecialidadeEspecialidadeId(), ':ppid'=>$this->getPrestadorespecialidadePrestadorId()));
    }

    public function publicar()
    {
        return parent::alterar(array($this->prefix . "_publico"=>true), $this->prefix . "_especialidade_id=:peid AND " . $this->prefix . "_prestador_id=:ppid", array(':peid'=>$this->getPrestadorespecialidadeEspecialidadeId(), ':ppid'=>$this->getPrestadorespecialidadePrestadorId()));
    }

    /**
     * @return int
     */
    public function getPrestadorespecialidadeEspecialidadeId()
    {
        return $this->prestadorespecialidade_especialidade_id;
    }

    /**
     * @param int $prestadorespecialidade_especialidade_id
     * @return PrestadorEspecialidade
     */
    public function setPrestadorespecialidadeEspecialidadeId($prestadorespecialidade_especialidade_id)
    {
        $this->prestadorespecialidade_especialidade_id = $prestadorespecialidade_especialidade_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrestadorespecialidadePrestadorId()
    {
        return $this->prestadorespecialidade_prestador_id;
    }

    /**
     * @param int $prestadorespecialidade_prestador_id
     * @return PrestadorEspecialidade
     */
    public function setPrestadorespecialidadePrestadorId($prestadorespecialidade_prestador_id)
    {
        $this->prestadorespecialidade_prestador_id = $prestadorespecialidade_prestador_id;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getPrestadorespecialidadePublico()
    {
        return $this->prestadorespecialidade_publico;
    }

    /**
     * @param boolean $prestadorespecialidade_publico
     * @return PrestadorEspecialidade
     */
    public function setPrestadorespecialidadePublico($prestadorespecialidade_publico)
    {
        $this->prestadorespecialidade_publico = $prestadorespecialidade_publico;
        return $this;
    }


}