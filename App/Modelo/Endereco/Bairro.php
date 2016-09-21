<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 15/09/2016
 */

namespace Modelo\Endereco;


class Bairro extends Cidade
{
    /** @var  integer */
    protected $bairro_id;
    /** @var  integer */
    //protected $bairro_cidade_id;
    /** @var  string */
    protected $bairro_nome;
    /** @var  boolean */
    protected $bairro_publico;

    function __construct()
    {
        $this->tableName = "bairro";
        $this->tableView = "view_bairro";
        $this->prefix = $this->semCaracteresEspeciais($this->tableName);
        $this->classname = get_class();
    }

    /**
     * Busca bairro utilizando nome do bairro e id da cidade como parametro
     * Retorna null se nada for encontrado ou uma instancia de Bairro caso contrário
     *
     * @param string $nome
     * @param int $idCidade
     * @return Bairro
     */
    public function buscarBairroPorNomeECidadeId($nome='', $idCidade=0)
    {
        if(empty($nome)) $nome = $this->getBairroNome();
        if(empty($idCidade) or $idCidade <= 0) $idCidade = $this->getCidadeId();

        $nome = filter_var($nome, FILTER_SANITIZE_STRING);
        $idCidade = filter_var($idCidade, FILTER_SANITIZE_NUMBER_INT);
        $Bairro = new self;
        $Bairro->pegar($Bairro->prefix.'_nome = :bnome AND '.$Bairro->prefix.'_cidade_id = :cid', array(':bnome'=>$nome, ':cid'=>$idCidade));

        return $Bairro;
    }

    /**
     * Adiciona um bairro ao banco
     * Ele espera receber uma instancia como parametro, caso não seja informado ele assumirá o valor null
     * e usará os dados da instancia atual
     *
     * @param Bairro|null $Bairro
     * @return Bairro|null
     */
    public function adicionarBairro(Bairro $Bairro=null)
    {
        if(is_null($Bairro)) {
            $Bairro = new self;
            $Bairro->setCidadeId($this->getCidadeId());
            $Bairro->setBairroNome($this->getBairroNome());
        }

        if($Bairro->adicionar()) return $Bairro;
        else return null;
    }

    public function adicionar()
    {
        $dados = array();
        $dados[$this->prefix.'_nome'] = $this->getBairroNome();
        $dados[$this->prefix.'_cidade_id'] = $this->getCidadeId();
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
            ':bnome'=>$this->getBairroNome(),
            ':cid'=>$this->getCidadeId()
        );
        $comparar = new self;
        $comparar->pegar(
            $comparar->prefix."_nome=:bnome AND "
            .$comparar->prefix."_cidade_id=:cid"
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
     * @return int
     */
    public function getBairroId()
    {
        return $this->bairro_id;
    }

    /**
     * @param int $bairro_id
     * @return Bairro
     */
    public function setBairroId($bairro_id)
    {
        $this->bairro_id = $bairro_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getBairroNome()
    {
        return $this->bairro_nome;
    }

    /**
     * @param string $bairro_nome
     * @return Bairro
     */
    public function setBairroNome($bairro_nome)
    {
        $this->bairro_nome = $bairro_nome;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isBairroPublico()
    {
        return $this->bairro_publico;
    }

    /**
     * @param boolean $bairro_publico
     * @return Bairro
     */
    public function setBairroPublico($bairro_publico)
    {
        $this->bairro_publico = $bairro_publico;
        return $this;
    }


}