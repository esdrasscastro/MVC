<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 21/09/2016
 */

namespace Modelo\Prestador;


use Modelo\DbModelo;

class PrestadorProcedimento extends DbModelo
{
    /** @var  integer */
    private $prestadorprocedimento_procedimento_id;
    /** @var  integer */
    private $prestadorprocedimento_prestador_id;
    /** @var  boolean */
    private $prestadorprocedimento_publico;

    function __construct(){
        $this->tableName = "prestador_procedimento";
        $this->tableView = "prestador_procedimento";
        $this->prefix = $this->semCaracteresEspeciais($this->tableName);
        $this->classname = get_class();
    }

    public function adicionar()
    {
        $dados = array();
        $dados['prestadorprocedimento_prestador_id'] = $this->getPrestadorprocedimentoPrestadorId();
        $dados['prestadorprocedimento_procedimento_id'] = $this->getPrestadorprocedimentoProcedimentoId();
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
            ':pppid'=>$this->getPrestadorprocedimentoProcedimentoId(),
            ':ppid'=>$this->getPrestadorprocedimentoPrestadorId(),
            ':publico'=>$this->getPrestadorprocedimentoPublico()
        );
        $comparar = new self;
        $comparar->pegar($this->prefix."_procedimento_id=:pppid AND ".$this->prefix."_prestador_id=:ppid AND ".$this->prefix."_publico=:publico", $bind);
        if($comparar->rowCount() > 0) return true;
        else return false;
    }

    public function despublicar()
    {
        return parent::alterar(array($this->prefix . "_publico"=>false), $this->prefix . "_procedimento_id=:pppid AND " . $this->prefix . "_prestador_id=:ppid", array(':pppid'=>$this->getPrestadorprocedimentoProcedimentoId(), ':ppid'=>$this->getPrestadorprocedimentoPrestadorId()));
    }

    public function publicar()
    {
        return parent::alterar(array($this->prefix . "_publico"=>true), $this->prefix . "_procedimento_id=:pppid AND " . $this->prefix . "_prestador_id=:ppid", array(':pppid'=>$this->getPrestadorprocedimentoProcedimentoId(), ':ppid'=>$this->getPrestadorprocedimentoPrestadorId()));
    }

    /**
     * @return int
     */
    public function getPrestadorprocedimentoProcedimentoId()
    {
        return $this->prestadorprocedimento_procedimento_id;
    }

    /**
     * @param int $prestadorprocedimento_procedimento_id
     * @return PrestadorProcedimento
     */
    public function setPrestadorprocedimentoProcedimentoId($prestadorprocedimento_procedimento_id)
    {
        $this->prestadorprocedimento_procedimento_id = $prestadorprocedimento_procedimento_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrestadorprocedimentoPrestadorId()
    {
        return $this->prestadorprocedimento_prestador_id;
    }

    /**
     * @param int $prestadorprocedimento_prestador_id
     * @return PrestadorProcedimento
     */
    public function setPrestadorprocedimentoPrestadorId($prestadorprocedimento_prestador_id)
    {
        $this->prestadorprocedimento_prestador_id = $prestadorprocedimento_prestador_id;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getPrestadorprocedimentoPublico()
    {
        return $this->prestadorprocedimento_publico;
    }

    /**
     * @param boolean $prestadorprocedimento_publico
     * @return PrestadorProcedimento
     */
    public function setPrestadorprocedimentoPublico($prestadorprocedimento_publico)
    {
        $this->prestadorprocedimento_publico = $prestadorprocedimento_publico;
        return $this;
    }


}