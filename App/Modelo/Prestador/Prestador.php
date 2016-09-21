<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 21/09/2016
 */

namespace Modelo\Prestador;


use Modelo\DbModelo;

class Prestador extends DbModelo
{
    /** @var  integer */
    private $prestador_id;
    /** @var  integer */
    private $prestador_prestadortipo_id;
    /** @var  integer */
    private $prestador_users_id;
    /** @var  string */
    private $prestador_nome;
    /** @var  string */
    private $prestador_cnpj;
    /** @var  string */
    private $prestador_cpf;
    /** @var  string */
    private $prestador_crm;
    /** @var  string */
    private $prestador_responsavel;
    /** @var  string */
    private $prestador_buscar;
    /** @var  string */
    private $prestador_endereco_cep;
    /** @var  string */
    private $prestador_numero;
    /** @var  string */
    private $prestador_complemento;
    /** @var  string */
    private $prestador_telefone1;
    /** @var  string */
    private $prestador_telefone2;
    /** @var  string */
    private $prestador_site;
    /** @var  string */
    private $prestador_descricao;
    /** @var  boolean */
    private $prestador_recebernews;
    /** @var  string */
    private $prestador_dtcadastro;
    /** @var  boolean */
    private $prestador_publico;

    function __construct(){
        $this->tableName = "prestador";
        $this->tableView = "prestador";
        $this->prefix = $this->semCaracteresEspeciais($this->tableName);
        $this->classname = get_class();
    }

    public function adicionar()
    {
        $dados = array();
        $dados['prestador_prestadortipo_id'] = $this->getPrestadorPrestadortipoId();
        $dados['prestador_users_id'] = $this->getPrestadorUsersId();
        $dados['prestador_nome'] = $this->getPrestadorNome();
        $dados['prestador_cnpj'] = $this->getPrestadorCnpj();
        $dados['prestador_cpf'] = $this->getPrestadorCpf();
        $dados['prestador_crm'] = $this->getPrestadorCrm();
        $dados['prestador_responsavel'] = $this->getPrestadorResponsavel();
        $dados['prestador_buscar'] = $this->getPrestadorBuscar();
        $dados['prestador_endereco_cep'] = $this->getPrestadorEnderecoCep();
        $dados['prestador_numero'] = $this->getPrestadorNumero();
        $dados['prestador_complemento'] = $this->getPrestadorComplemento();
        $dados['prestador_telefone1'] = $this->getPrestadorTelefone1();
        $dados['prestador_telefone2'] = $this->getPrestadorTelefone2();
        $dados['prestador_site'] = $this->getPrestadorSite();
        $dados['prestador_descricao'] = $this->getPrestadorDescricao();
        $dados['prestador_recebernews'] = $this->getPrestadorRecebernews();
        $dados['prestador_dtcadastro'] = $this->getPrestadorDtcadastro();
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
            ':pid'=>$this->getPrestadorId(),
            ':ptid'=>$this->getPrestadorPrestadortipoId(),
            ':uid'=>$this->getPrestadorUsersId(),
            ':nome'=>$this->getPrestadorNome(),
            ':cnpj'=>$this->getPrestadorCnpj(),
            ':cpf'=>$this->getPrestadorCpf(),
            ':crp'=>$this->getPrestadorCrm(),
            ':resp'=>$this->getPrestadorResponsavel(),
            ':busc'=>$this->getPrestadorBuscar(),
            ':ecep'=>$this->getPrestadorEnderecoCep(),
            ':num'=>$this->getPrestadorNumero(),
            ':comp'=>$this->getPrestadorComplemento(),
            ':tel1'=>$this->getPrestadorTelefone1(),
            ':tel2'=>$this->getPrestadorTelefone2(),
            ':site'=>$this->getPrestadorSite(),
            ':desc'=>$this->getPrestadorDescricao()
        );

        $sql = $this->prefix."_id=:pid AND "
            .$this->prefix."_prestadortipo_id=:ptid AND "
            .$this->prefix."_users_id=:uid AND "
            .$this->prefix."_nome=:nome AND "
            .$this->prefix."_cnpj=:cnpj AND "
            .$this->prefix."_cpf=:cpf AND "
            .$this->prefix."_crm=:crm AND "
            .$this->prefix."_responsavel=:resp AND "
            .$this->prefix."_buscar=:busc AND "
            .$this->prefix."_endereco_cep=:ecep AND "
            .$this->prefix."_numero=:num AND "
            .$this->prefix."_complemento=:comp AND "
            .$this->prefix."_telefone1=:tel1 AND "
            .$this->prefix."_telefone2=:tel2 AND "
            .$this->prefix."_site=:site AND "
            .$this->prefix."_descricao=:desc";

        $comparar = new self;
        $comparar->pegar($sql, $bind);
        if($comparar->rowCount() > 0) return true;
        else return false;
    }

    public function despublicar()
    {
        return parent::alterar(array($this->prefix."_publico"=>false), $this->prefix."_id=:id", array(':id'=>$this->getPrestadorId()));
    }

    public function publicar()
    {
        return parent::alterar(array($this->prefix."_publico"=>true), $this->prefix."_id=:id", array(':id'=>$this->getPrestadorId()));
    }

    /**
     * @return int
     */
    public function getPrestadorId()
    {
        return $this->prestador_id;
    }

    /**
     * @param int $prestador_id
     * @return Prestador
     */
    public function setPrestadorId($prestador_id)
    {
        $this->prestador_id = $prestador_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrestadorPrestadortipoId()
    {
        return $this->prestador_prestadortipo_id;
    }

    /**
     * @param int $prestador_prestadortipo_id
     * @return Prestador
     */
    public function setPrestadorPrestadortipoId($prestador_prestadortipo_id)
    {
        $this->prestador_prestadortipo_id = $prestador_prestadortipo_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrestadorUsersId()
    {
        return $this->prestador_users_id;
    }

    /**
     * @param int $prestador_users_id
     * @return Prestador
     */
    public function setPrestadorUsersId($prestador_users_id)
    {
        $this->prestador_users_id = $prestador_users_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrestadorNome()
    {
        return $this->prestador_nome;
    }

    /**
     * @param string $prestador_nome
     * @return Prestador
     */
    public function setPrestadorNome($prestador_nome)
    {
        $this->prestador_nome = filter_var($prestador_nome, FILTER_SANITIZE_STRING);
        return $this;
    }

    /**
     * @return string
     */
    public function getPrestadorCnpj()
    {
        return $this->prestador_cnpj;
    }

    /**
     * @param string $prestador_cnpj
     * @return Prestador
     */
    public function setPrestadorCnpj($prestador_cnpj)
    {
        $this->prestador_cnpj = filter_var($prestador_cnpj, FILTER_SANITIZE_STRING);
        return $this;
    }

    /**
     * @return string
     */
    public function getPrestadorCpf()
    {
        return $this->prestador_cpf;
    }

    /**
     * @param string $prestador_cpf
     * @return Prestador
     */
    public function setPrestadorCpf($prestador_cpf)
    {
        $this->prestador_cpf = filter_var($prestador_cpf, FILTER_SANITIZE_STRING);
        return $this;
    }

    /**
     * @return string
     */
    public function getPrestadorCrm()
    {
        return $this->prestador_crm;
    }

    /**
     * @param string $prestador_crm
     * @return Prestador
     */
    public function setPrestadorCrm($prestador_crm)
    {
        $this->prestador_crm = filter_var($prestador_crm, FILTER_SANITIZE_STRING);
        return $this;
    }

    /**
     * @return string
     */
    public function getPrestadorResponsavel()
    {
        return $this->prestador_responsavel;
    }

    /**
     * @param string $prestador_responsavel
     * @return Prestador
     */
    public function setPrestadorResponsavel($prestador_responsavel)
    {
        $this->prestador_responsavel = filter_var($prestador_responsavel, FILTER_SANITIZE_STRING);
        return $this;
    }

    /**
     * @return string
     */
    public function getPrestadorBuscar()
    {
        return $this->prestador_buscar;
    }

    /**
     * @param string $prestador_buscar
     * @return Prestador
     */
    public function setPrestadorBuscar($prestador_buscar)
    {
        $this->prestador_buscar = filter_var($prestador_buscar, FILTER_SANITIZE_STRING);
        return $this;
    }

    /**
     * @return string
     */
    public function getPrestadorEnderecoCep()
    {
        return $this->prestador_endereco_cep;
    }

    /**
     * @param string $prestador_endereco_cep
     * @return Prestador
     */
    public function setPrestadorEnderecoCep($prestador_endereco_cep)
    {
        $this->prestador_endereco_cep = filter_var($prestador_endereco_cep, FILTER_SANITIZE_STRING);
        return $this;
    }

    /**
     * @return string
     */
    public function getPrestadorNumero()
    {
        return $this->prestador_numero;
    }

    /**
     * @param string $prestador_numero
     * @return Prestador
     */
    public function setPrestadorNumero($prestador_numero)
    {
        $this->prestador_numero = $prestador_numero;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrestadorComplemento()
    {
        return $this->prestador_complemento;
    }

    /**
     * @param string $prestador_complemento
     * @return Prestador
     */
    public function setPrestadorComplemento($prestador_complemento)
    {
        $this->prestador_complemento = filter_var($prestador_complemento, FILTER_SANITIZE_STRING);
        return $this;
    }

    /**
     * @return string
     */
    public function getPrestadorTelefone1()
    {
        return $this->prestador_telefone1;
    }

    /**
     * @param string $prestador_telefone1
     * @return Prestador
     */
    public function setPrestadorTelefone1($prestador_telefone1)
    {
        $this->prestador_telefone1 = filter_var($prestador_telefone1, FILTER_SANITIZE_STRING);
        return $this;
    }

    /**
     * @return string
     */
    public function getPrestadorTelefone2()
    {
        return $this->prestador_telefone2;
    }

    /**
     * @param string $prestador_telefone2
     * @return Prestador
     */
    public function setPrestadorTelefone2($prestador_telefone2)
    {
        $this->prestador_telefone2 = filter_var($prestador_telefone2, FILTER_SANITIZE_STRING);
        return $this;
    }

    /**
     * @return string
     */
    public function getPrestadorSite()
    {
        return $this->prestador_site;
    }

    /**
     * @param string $prestador_site
     * @return Prestador
     */
    public function setPrestadorSite($prestador_site)
    {
        $this->prestador_site = filter_var($prestador_site, FILTER_SANITIZE_URL);
        return $this;
    }

    /**
     * @return string
     */
    public function getPrestadorDescricao()
    {
        return $this->prestador_descricao;
    }

    /**
     * @param string $prestador_descricao
     * @return Prestador
     */
    public function setPrestadorDescricao($prestador_descricao)
    {
        $this->prestador_descricao = filter_var($prestador_descricao, FILTER_SANITIZE_STRING);
        return $this;
    }

    /**
     * @return boolean
     */
    public function getPrestadorRecebernews()
    {
        return $this->prestador_recebernews;
    }

    /**
     * @param boolean $prestador_recebernews
     * @return Prestador
     */
    public function setPrestadorRecebernews($prestador_recebernews)
    {
        $this->prestador_recebernews = $prestador_recebernews;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrestadorDtcadastro()
    {
        return $this->prestador_dtcadastro;
    }

    /**
     * @param string $prestador_dtcadastro
     * @return Prestador
     */
    public function setPrestadorDtcadastro($prestador_dtcadastro)
    {
        $this->prestador_dtcadastro = $prestador_dtcadastro;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getPrestadorPublico()
    {
        return $this->prestador_publico;
    }

    /**
     * @param boolean $prestador_publico
     * @return Prestador
     */
    public function setPrestadorPublico($prestador_publico)
    {
        $this->prestador_publico = $prestador_publico;
        return $this;
    }


}