<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 15/09/2016
 */

namespace Modelo\Endereco;

use Modelo\DbModelo;

abstract class Pais extends DbModelo
{
    /** @var  integer */
    protected $pais_id;
    /** @var  string */
    protected $pais_nome;
    /** @var  string */
    protected $pais_sigla;
    /** @var  boolean */
    protected $pais_publico;

    function __construct()
    {
        $this->tableName = "pais";
        $this->tableView = "pais";
        $this->prefix = $this->semCaracteresEspeciais($this->tableName);
        $this->classname = get_class();
    }

    /**
     * @return int
     */
    public function getPaisId()
    {
        return $this->pais_id;
    }

    /**
     * @param int $pais_id
     * @return Pais
     */
    public function setPaisId($pais_id)
    {
        $this->pais_id = $pais_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getPaisNome()
    {
        return $this->pais_nome;
    }

    /**
     * @param string $pais_nome
     * @return Pais
     */
    public function setPaisNome($pais_nome)
    {
        $this->pais_nome = $pais_nome;
        return $this;
    }

    /**
     * @return string
     */
    public function getPaisSigla()
    {
        return $this->pais_sigla;
    }

    /**
     * @param string $pais_sigla
     * @return Pais
     */
    public function setPaisSigla($pais_sigla)
    {
        $this->pais_sigla = $pais_sigla;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getPaisPublico()
    {
        return $this->pais_publico;
    }

    /**
     * @param boolean $pais_publico
     * @return Pais
     */
    public function setPaisPublico($pais_publico)
    {
        $this->pais_publico = $pais_publico;
        return $this;
    }
}