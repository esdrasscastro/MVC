<?php
/**
 * Created by PhpStorm.
 * @author Esdras Castro
 * @date 07/12/2015 16:47
 */
namespace Modelo\Endereco;

class Cep extends Endereco
{
    private $urlRepublica;
    private $urlViacep;
    private $tentativas = 3;

    function __construct()
    {
        parent::__construct();
        $this->urlRepublica = 'http://cep.republicavirtual.com.br/web_cep.php?formato=json&cep=';
        $this->urlViacep = "http://viacep.com.br/ws/%s/json/unicode/";
    }

    /**
     * <b>BuscaCep</b>.
     * Identifica e busca o cep. Primeiramente ele varre o banco de dados em busca de endereços existentes
     * caso não encontre ele busca no site dos correios e insere essa nova informação do DB
     *
     * @param string $cep Cep de localização do cliente
     */
    public function buscar($cep='00000-000', $returnBD=true)
    {
        $this->setEnderecoCep($cep);
        $cep = self::cepMask($cep);
        if($returnBD){
            /* Caso queira retornar os dados do banco de dados */
            $endereco = parent::pegar($this->prefix.'_cep=:cep', array(':cep'=>$cep));
            if(!$endereco->rowCount()){

                /* O cep não existe no banco, então tentaremos inserir o novo endereço */
                if(self::buscarNaWeb()){
                    /* O cep existindo, adicionamos ele no banco de dados */

                    /* Primeiro procuramos o Estado, se encontrar retorna uma instancia de Estado senão retorna null */
                    if($Estado = parent::buscarEstadoPorSigla() and $Estado->rowCount()){
                        $Estado = $Estado->results();
                        $this->setEstadoId($Estado->getEstadoId());
                        $this->setPaisId($Estado->getPaisId());

                        /* Agora buscamos a cidade utilizando como referencia o nome e o estado a qual pertence */
                        if($Cidade = parent::buscarCidadePorNomeEEstadoId() and $Cidade->rowCount()){
                            $Cidade = $Cidade->results();
                            $this->setCidadeId($Cidade->getCidadeId());

                            /* Com a cidade e o estado encontrado, buscaremos o bairro utilizando o nome do bairro a cidade */
                            if($Bairro = parent::buscarBairroPorNomeECidadeId() and $Bairro->rowCount()){
                                $Bairro = $Bairro->results();
                                $this->setBairroId($Bairro->getBairroId());

                                /* Por fim, com todos os dados necessário inserimos o novo endereço */
                                if(!$this->comparar()){
                                    parent::beginTransaction();
                                    if($this->adicionar() and $this->tentativas > 0) {
                                        parent::commit();
                                        $this->tentativas--;
                                        return self::buscar($cep, $returnBD);
                                    }else parent::rollBack();
                                }else{
                                    if($this->tentativas > 0) {
                                        $this->tentativas--;
                                        return self::buscar($cep, $returnBD);
                                    }
                                }
                            }else{
                                /* Se não encontrarmos o bairro, então teremos que adiciona-lo no DB */
                                parent::beginTransaction();
                                if(parent::adicionarBairro() and $this->tentativas > 0) {
                                    parent::commit();
                                    $this->tentativas--;
                                    return self::buscar($cep, $returnBD);
                                }else parent::rollBack();
                            }
                        }
                    }
                }
            }
        }else{
            /**
             * Caso queira retornar os dados do servidor web
             * então pegamos ele na web e retornamos a classe CEP
             * com apenas alguns dados preenchidos.
             */
            self::buscarNaWeb($cep);
        }

        return $this;
    }


    /**
     * Mascara o cep para o formato 00000-000
     *
     * @param string $cep
     * @return string
     */
    public function cepMask($cep='00000-000')
    {
        $cep = filter_var( (int) preg_replace("/[^0-9]/",'', $cep), FILTER_SANITIZE_NUMBER_INT);

        $ceplengh = strlen($cep);
        if($ceplengh != 8) throw new \InvalidArgumentException("O cep informado é inválido.");

        $parte1 = substr($cep, 0, 5);
        $parte2 = substr($cep, 5, 8);

        return $parte1.'-'.$parte2;
    }

    /**
     * Faz uma busca nos sites RepublicaVirtual ou ViaCep procurando o cep informado
     * Retorna verdadeiro caso encontrado e seta os valores na classe atual
     *
     * @param string $cep
     * @return bool
     */
    private function buscarNaWeb($cep='')
    {
        if(empty($cep)) $cep = parent::getEnderecoCep();

        $republica = $this->webClient($this->urlRepublica.$cep);

        if($republica){
            $republica = json_decode($republica);

            if((boolean)$republica->resultado) {
                parent::setEnderecoLogradouro($republica->tipo_logradouro . ' ' . $republica->logradouro);
                parent::setBairroNome($republica->bairro);
                parent::setCidadeNome($republica->cidade);
                parent::setEstadoSigla($republica->uf);

                return true;
            }
        }else{
            $viacep = $this->webClient(sprintf($this->urlViacep, $cep));

            if($viacep){
                $viacep = json_decode($viacep);

                if(!property_exists($viacep, 'erro')){
                    parent::setEnderecoCep($cep);
                    parent::setEnderecoLogradouro($viacep->logradouro);
                    parent::setBairroNome($viacep->bairro);
                    parent::setCidadeNome($viacep->localidade);
                    parent::setEstadoSigla($viacep->uf);

                    return true;
                }
            }
        }

        return false;
    }

    public static function addEndereco($cep='', $logradouro='', $bairro='')
    {
        $cep = filter_var(trim($cep), FILTER_SANITIZE_STRING);
        $logradouro = ucwords(strtolower(filter_var(trim($logradouro), FILTER_SANITIZE_STRING)));
        $bairro     = ucwords(strtolower(filter_var(trim($bairro), FILTER_SANITIZE_STRING)));

        $rsendereco = self::buscar($cep, true);
        if(!empty($logradouro) and !empty($bairro)) {
            parent::beginTransaction();
            if($rsendereco->results->logradouro=='...') parent::alterar(array('logradouro'=>$logradouro), 'cep=:cep', array(':cep'=>$rsendereco->results->cep));
            if ($rsendereco->results->bairro == '...') parent::alterar(array('bairro' => $bairro), 'id=:id', array(':id' => $rsendereco->results->idbairro));
            parent::commit();
            return 1;
        }

        return 0;
    }

    public function webClient ($url='')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,30); /* tempo em segundos */
        curl_setopt($ch, CURLOPT_TIMEOUT, 120); /* tempo em segundos (2 minutos) padrão */
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}