<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 14/09/2016
 */

namespace Modelo\Prestador;


use Modelo\DbModelo;

class PrestadorTipo extends DbModelo
{
    /** @var  integer */
    private $prestadortipo_id;
    /** @var  string */
    private $prestadortipo_nome;
    /** @var  boolean */
    private $prestadortipo_publico;

    function __construct(){
        $this->tableName = "prestador_tipo";
        $this->tableView = "prestador_tipo";
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
            ':id'=>$this->getPrestadortipoId(),
            ':nome'=>$this->getPrestadortipoNome(),
            ':publico'=>$this->getPrestadortipoPublico()
        );
        $comparar = new self;
        $comparar->pegar($this->prefix."_id=:id AND ".$this->prefix."_nome=:nome AND ".$this->prefix."_publico=:publico", $bind);
        if($comparar->rowCount() > 0) return true;
        else return false;
    }

    public function despublicar()
    {
        return parent::alterar(array($this->prefix."_publico"=>false), $this->prefix."_id=:id", array(':id'=>$this->getPrestadortipoId()));
    }

    public function publicar()
    {
        return parent::alterar(array($this->prefix."_publico"=>true), $this->prefix."_id=:id", array(':id'=>$this->getPrestadortipoId()));
    }

    /**
     * @return int
     */
    public function getPrestadortipoId()
    {
        return $this->prestadortipo_id;
    }

    /**
     * @param int $prestadortipo_id
     * @return PrestadorTipo
     */
    public function setPrestadortipoId($prestadortipo_id)
    {
        $this->prestadortipo_id = $prestadortipo_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrestadortipoNome()
    {
        return $this->prestadortipo_nome;
    }

    /**
     * @param string $prestadortipo_nome
     * @return PrestadorTipo
     */
    public function setPrestadortipoNome($prestadortipo_nome)
    {
        $this->prestadortipo_nome = filter_var($prestadortipo_nome, FILTER_SANITIZE_STRING);
        return $this;
    }

    /**
     * @return boolean
     */
    public function getPrestadortipoPublico()
    {
        return $this->prestadortipo_publico;
    }

    /**
     * @param boolean $prestadortipo_publico
     * @return PrestadorTipo
     */
    public function setPrestadortipoPublico($prestadortipo_publico)
    {
        $this->prestadortipo_publico = $prestadortipo_publico;
        return $this;
    }


}