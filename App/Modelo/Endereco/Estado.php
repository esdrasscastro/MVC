<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 15/09/2016
 */

namespace Modelo\Endereco;


class Estado extends Pais
{
    /** @var  integer */
    protected $estado_id;
    /** @var  integer */
    //protected $estado_pais_id;
    /** @var  string */
    protected $estado_nome;
    /** @var  string */
    protected $estado_sigla;
    /** @var  boolean */
    protected $estado_publico;

    function __construct()
    {
        $this->tableName = "estado";
        $this->tableView = "view_estado";
        $this->prefix = $this->semCaracteresEspeciais($this->tableName);
        $this->classname = get_class();
    }

    /**
     * Busca um determinado estado utilizando a sigla do estado (ex.: RJ)
     * Se encontrado retorna uma instancia de Estado caso contrário retorna null
     *
     * @param string $sigla
     * @return Estado|null
     */
    public function buscarEstadoPorSigla($sigla='')
    {
        if(empty($sigla)) $sigla = $this->getEstadoSigla();

        $sigla = filter_var($sigla, FILTER_SANITIZE_STRING);
        $Estado = new Estado();
        $Estado->pegar($Estado->prefix.'_sigla = :euf', array(':euf'=>$sigla) );
        if($Estado->rowCount()) return $Estado;
        return null;
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
        // TODO: Implement comparar() method.
    }

    public function despublicar()
    {
        // TODO: Implement despublicar() method.
    }

    public function publicar()
    {
        // TODO: Implement publicar() method.
    }



    /**
     * @return int
     */
    public function getEstadoId()
    {
        return $this->estado_id;
    }

    /**
     * @param int $estado_id
     * @return Estado
     */
    public function setEstadoId($estado_id)
    {
        $this->estado_id = $estado_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getEstadoNome()
    {
        return $this->estado_nome;
    }

    /**
     * @param string $estado_nome
     * @return Estado
     */
    public function setEstadoNome($estado_nome)
    {
        $this->estado_nome = $estado_nome;
        return $this;
    }

    /**
     * @return string
     */
    public function getEstadoSigla()
    {
        return $this->estado_sigla;
    }

    /**
     * @param string $estado_sigla
     * @return Estado
     */
    public function setEstadoSigla($estado_sigla)
    {
        $this->estado_sigla = $estado_sigla;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getEstadoPublico()
    {
        return $this->estado_publico;
    }

    /**
     * @param boolean $estado_publico
     * @return Estado
     */
    public function setEstadoPublico($estado_publico)
    {
        $this->estado_publico = $estado_publico;
        return $this;
    }
}