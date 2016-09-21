<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 15/09/2016
 */

namespace Modelo\Endereco;


class Endereco extends Bairro
{
    /** @var  string */
    protected $endereco_cep;
    /** @var  string */
    protected $endereco_logradouro;
    /** @var  integer */
    //protected $endereco_bairro_id;
    /** @var  integer */
    //protected $endereco_cidade_id;
    /** @var  integer */
    //protected $endereco_estado_id;
    /** @var  integer */
    //protected $endereco_pais_id;
    /** @var  boolean */
    protected $endereco_publico;

    function __construct()
    {
        $this->tableName = "endereco";
        $this->tableView = "view_endereco";
        $this->prefix = $this->semCaracteresEspeciais($this->tableName);
        $this->classname = get_class();
    }

    public function adicionarBairro(Bairro $Bairro=null)
    {
        return parent::adicionarBairro($Bairro);
    }

    public function adicionarCidade(Cidade $Cidade=null)
    {
        return parent::adicionarCidade($Cidade);
    }

    public function adicionar()
    {
        $dados = array();
        $dados['endereco_cep'] = $this->getEnderecoCep();
        $dados['endereco_logradouro'] = $this->getEnderecoLogradouro();
        $dados['endereco_bairro_id'] = $this->getBairroId();
        $dados['endereco_cidade_id'] = $this->getCidadeId();
        $dados['endereco_estado_id'] = $this->getEstadoId();
        $dados['endereco_pais_id'] = $this->getPaisId();
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
            ':cep'=>$this->getEnderecoCep(),
            ':log'=>$this->getEnderecoLogradouro(),
            ':bai'=>$this->getBairroId(),
            ':cid'=>$this->getCidadeId(),
            ':est'=>$this->getEstadoId(),
            ':pai'=>$this->getPaisId()
        );
        $comparar = new self;
        $comparar->pegar(
            $comparar->prefix."_cep=:cep AND "
            .$comparar->prefix."_logradouro=:log AND "
            .$comparar->prefix."_bairro_id=:bai AND "
            .$comparar->prefix."_cidade_id=:cid AND "
            .$comparar->prefix."_estado_id=:est AND "
            .$comparar->prefix."_pais_id=:pai"
        , $bind);
        if($comparar->rowCount() > 0) return true;
        else return false;
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
     * @return string
     */
    public function getEnderecoCep()
    {
        return $this->endereco_cep;
    }

    /**
     * @param string $endereco_cep
     * @return Endereco
     */
    public function setEnderecoCep($endereco_cep)
    {
        $this->endereco_cep = $endereco_cep;
        return $this;
    }

    /**
     * @return string
     */
    public function getEnderecoLogradouro()
    {
        return $this->endereco_logradouro;
    }

    /**
     * @param string $endereco_logradouro
     * @return Endereco
     */
    public function setEnderecoLogradouro($endereco_logradouro)
    {
        $this->endereco_logradouro = $endereco_logradouro;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getEnderecoPublico()
    {
        return $this->endereco_publico;
    }

    /**
     * @param boolean $endereco_publico
     * @return Endereco
     */
    public function setEnderecoPublico($endereco_publico)
    {
        $this->endereco_publico = $endereco_publico;
        return $this;
    }
}