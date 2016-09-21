<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 15/09/2016
 */

namespace Modelo\Endereco;


class Cidade extends Estado
{
    /** @var  integer */
    protected $cidade_id;
    /** @var  integer */
    //protected $cidade_estado_id;
    /** @var  string */
    protected $cidade_nome;
    /** @var  boolean */
    protected $cidade_publico;

    function __construct()
    {
        $this->tableName = "cidade";
        $this->tableView = "view_cidade";
        $this->prefix = $this->semCaracteresEspeciais($this->tableName);
        $this->classname = get_class();
    }

    /**
     * Busca uma cidade utilizando como referência o nome da cidade e o id do estado
     * Retorna uma instancia de Cidade ou null caso nada seja encontrado.
     *
     * @param string $nome
     * @param int $idEstado
     * @return Cidade
     */
    public function buscarCidadePorNomeEEstadoId($nome='', $idEstado=0)
    {
        if(empty($nome)) $nome = $this->getCidadeNome();
        if(empty($idEstado) or $idEstado <= 0) $idEstado = $this->getEstadoId();

        $nome = filter_var($nome, FILTER_SANITIZE_STRING);
        $idEstado = filter_var($idEstado, FILTER_SANITIZE_NUMBER_INT);

        $Cidade = new self;
        $Cidade->pegar($Cidade->prefix.'_nome = :cnome AND '.$Cidade->prefix.'_estado_id = :eid', array(':cnome'=>$nome, ':eid'=>$idEstado));
        return $Cidade;
    }

    /**
     * Adiciona um bairro ao banco
     * Ele espera receber uma instancia como parametro, caso não seja informado ele assumirá o valor null
     * e usará os dados da instancia atual
     *
     * @param Cidade|null $Cidade
     * @return Cidade|null
     */
    public function adicionarCidade(Cidade $Cidade=null)
    {
        if(is_null($Cidade)) {
            $Cidade = new self;
            $Cidade->setEstadoId($this->getEstadoId());
            $Cidade->setCidadeNome($this->getCidadeNome());
        }

        if($Cidade->adicionar()) return $Cidade;
        else return null;
    }

    public function adicionar()
    {
        $dados = array();
        $dados[$this->prefix.'_nome'] = $this->getCidadeNome();
        $dados[$this->prefix.'_estado_id'] = $this->getEstadoId();
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
    public function getCidadeId()
    {
        return $this->cidade_id;
    }

    /**
     * @param int $cidade_id
     * @return Cidade
     */
    public function setCidadeId($cidade_id)
    {
        $this->cidade_id = $cidade_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getCidadeNome()
    {
        return $this->cidade_nome;
    }

    /**
     * @param string $cidade_nome
     * @return Cidade
     */
    public function setCidadeNome($cidade_nome)
    {
        $this->cidade_nome = $cidade_nome;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getCidadePublico()
    {
        return $this->cidade_publico;
    }

    /**
     * @param boolean $cidade_publico
     * @return Cidade
     */
    public function setCidadePublico($cidade_publico)
    {
        $this->cidade_publico = $cidade_publico;
        return $this;
    }


}