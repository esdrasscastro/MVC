<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 17/08/2016
 */

namespace Controle;

use Lib\Sistema;
use Lib\Tools\MailSender;
use Modelo\Endereco\Cep;
use Modelo\Prestador\Prestador;
use Modelo\Prestador\PrestadorEspecialidade;
use Modelo\Prestador\PrestadorExameCategoria;
use Modelo\Prestador\PrestadorProcedimento;
use Modelo\Users;

class Cadastrar extends Sistema
{
    public static $authenticated = false;

    public function index()
    {
        //self::myPrivilege();
        parent::setJsScript("Cadastrar.init();");
        parent::setJsScript("Global.vars.basePath = '".self::$basePath."';");
        parent::header('Cadastro de Prestador');
        require_once(self::$htmlPath."cadastro/index.phtml");
        parent::footer();
    }

    /**
     * Carrega a página de sucesso de cadastro e envia email de ativação para o cliente.
     *
     * @param string $fkey
     * @param int $uid
     */
    public function sucesso($fkey='', $uid=0)
    {
        if(parent::siteRequest($fkey, 'cadastrar_prestador_sucesso') and $uid > 0) {
            $Users = new Users();
            $Users->pegar('users_id=:uid', array(':uid'=>$uid));

            if($Users->rowCount()) {
                if (self::sendLinkAtivacao($Users)) {
                    parent::header('Prestador Cadastrado!');
                    require_once(self::$htmlPath . "cadastro/sucesso.phtml");
                    parent::footer();
                } else {
                    new Error(601);
                }
            }else{
                new Error(404);
            }
        }else{
            new Error(404);
        }
    }

    /**
     * Envia um link de ativacao para o usuário informado
     *
     * @param Users|null $Users
     * @return bool
     */
    private function sendLinkAtivacao(Users $Users=null)
    {
        $mensagem = "";
        $code = \Lib\Tools\Hash::rescue_key_generate($Users->results()->getUsersUsername()) . "/" .$Users->results()->getUsersId();
        /*echo $code;*/
        require_once (self::$htmlPath . "cadastro/template.linkativacao.phtml");
        $MailSender = new MailSender();
        $MailSender
            ->addFrom(parent::$sitename, parent::$sitemail)
            ->addTo($Users->results()->getUsersName(), $Users->results()->getUsersUsername())
            ->addBcc("esdras-tito@hotmail.com")
            ->subject("Ative seu cadastro Acheimed")
            ->message($mensagem)
            ->send()
        ;
        if($MailSender->status()){
            return true;
        }else{
            new Error(601);
            /*die($MailSender->getMessage());*/
        }
    }

    public function ativarCadastro($key='', $uid=0)
    {
        $key = filter_var($key, FILTER_SANITIZE_STRING);
        $uid = filter_var($uid, FILTER_SANITIZE_NUMBER_INT);

        if(empty($key) and $uid <= 0) {
            // Parametros incompletos
        }else{
            $Users = new Users();
            $Users->pegar('users_id=:uid', array(':uid'=>$uid));

            parent::header('Ativação de cadastro');
            if($Users->rowCount() and $key==\Lib\Tools\Hash::rescue_key_generate($Users->results()->getUsersUsername())){
                if($Users->results()->publicar()) require_once (parent::$htmlPath."cadastro/ativado.phtml");
                else {
                    $error = $Users->getErrorInfo();
                    if(!empty($error) and (int)$error[0] > 0){
                        new Error(404);
                    }else{
                        require_once (parent::$htmlPath."cadastro/jaativado.phtml");
                    }
                }
            }else{
                require_once (parent::$htmlPath."cadastro/naoativado.phtml");
            }
            parent::footer();
        }
    }

    /**
     * Retorna um json com o endereco do cep informado
     *
     * @param string $cep
     */
    public function buscarcep($cep='')
    {
        if(\Lib\Tools\Route::isSiteRequest()) {
            $Cep = new Cep();
            $Cep->buscar($cep);
            if ($Cep->rowCount()) {
                echo json_encode(array('status' => 200, 'error' => false, 'message' => 'Cep encontrado', 'data' => array(
                    'endereco_cep' => $Cep->results()->getEnderecoCep(),
                    'endereco_logradouro' => $Cep->results()->getEnderecoLogradouro(),
                    'endereco_bairro_nome' => $Cep->results()->getBairroNome(),
                    'endereco_cidade_nome' => $Cep->results()->getCidadeNome(),
                    'endereco_estado_sigla' => $Cep->results()->getEstadoSigla()
                )));
            } else echo json_encode(array('status' => 200, 'error' => true, 'message' => 'Cep inválido!'));
        }else{
            header("HTTP/1.1 404 Page not found!");
        }
    }

    /**
     * Realiza o cadastro do prestador no banco
     *
     * @param string $fkey
     */
    public function fazerCadastro($fkey='')
    {
        if(parent::siteRequest($fkey, 'cadastrar_prestador')) {
            $post = $_POST;
            $validate = self::validarCadastro($post);
            if (!$validate['error']) {
                $hash = \Lib\Tools\Hash::generate_hash($post['users_password']);
                $post['users_password'] = \Lib\Tools\Hash::password_create($post['users_password'], $hash);

                $Users = new Users();
                $Users
                    ->setUsersName(reset(explode(' ', $post['prestador_nome'])))
                    ->setUsersUsername($post['users_username'])
                    ->setUsersLastLogin(date("Y-m-d H:i:s"))
                    ->setUsersPassword($post['users_password'])
                    ->setUsersHash($hash)
                ;

                if($Users->beginTransaction()->adicionar()){
                    $userid = $Users->lastInsertId();
                    /** @var  $Prestador */
                    $Prestador = new Prestador();
                    $Prestador
                        ->setPrestadorPrestadortipoId($post['prestadortipo_id'])
                        ->setPrestadorUsersId($userid)
                        ->setPrestadorNome($post['prestador_nome'])
                        ->setPrestadorCnpj($post['radio_cpf_cnpj']==2?$post['prestador_cpf_cnpj']:'')
                        ->setPrestadorCpf($post['radio_cpf_cnpj']==1?$post['prestador_cpf_cnpj']:'')
                        ->setPrestadorResponsavel($post['prestador_responsavel'])
                        ->setPrestadorCrm($post['prestador_crm'])
                        ->setPrestadorEnderecoCep($post['endereco_cep'])
                        ->setPrestadorNumero($post['prestador_numero'])
                        ->setPrestadorComplemento($post['prestador_complemento'])
                        ->setPrestadorTelefone1($post['prestador_telefone1'])
                        ->setPrestadorTelefone2($post['prestador_telefone2'])
                        ->setPrestadorSite($post['prestador_site'])
                        ->setPrestadorDescricao($post['prestador_descricao'])
                        ->setPrestadorRecebernews((isset($post['prestador_recebernews']))?1:0)
                        ->setPrestadorDtcadastro(date("Y-m-d H:i:s"))
                    ;
                    if($Prestador->adicionar()){
                        $prestadorid = $Prestador->lastInsertId();

                        /** @var  $PrestadorProcedimento */
                        $PrestadorProcedimento = new PrestadorProcedimento();
                        foreach ($post['procedimento_id'] as $key=>$val)
                            $PrestadorProcedimento
                                ->setPrestadorprocedimentoPrestadorId($prestadorid)
                                ->setPrestadorprocedimentoProcedimentoId($val)
                                ->adicionar()
                            ;

                        /** @var  $PrestadorEspecialidade */
                        $PrestadorEspecialidade = new PrestadorEspecialidade();
                        foreach ($post['especialidade_id'] as $key=>$val)
                            $PrestadorEspecialidade
                                ->setPrestadorespecialidadePrestadorId($prestadorid)
                                ->setPrestadorespecialidadeEspecialidadeId($val)
                                ->adicionar()
                            ;

                        /** @var  $PrestadorExameCategoria */
                        $PrestadorExameCategoria = new PrestadorExameCategoria();
                        foreach ($post['exame_categoria_id'] as $key=>$val)
                            $PrestadorExameCategoria
                                ->setPrestadorexamecategoriaPrestadorId($prestadorid)
                                ->setPrestadorexamecategoriaExamecategoriaId($val)
                                ->adicionar()
                            ;

                        $Users->commit();
                        echo json_encode(array('status'=>true, 'error'=>false, 'message'=>'Usuário adicionado com sucesso!', 'fields'=>array(), 'errorInfo'=>$Users->getErrorInfo(), 'uid'=>$userid));
                    }else{
                        $Users->rollBack();
                        echo json_encode(array('status'=>true, 'error'=>true, 'message'=>'Falha ao tentar adicionar o usuário', 'fields'=>array(), 'errorInfo'=>$Users->getErrorInfo(), 'uid'=>0));
                    }
                }else{
                    $Users->rollBack();
                    echo json_encode(array('status'=>true, 'error'=>true, 'message'=>'Falha ao tentar adicionar o usuário', 'fields'=>array(), 'errorInfo'=>$Users->getErrorInfo(), 'uid'=>0));
                }
            } else {
                echo json_encode($validate);
            }
        }else{
            echo json_encode(array('status'=>true, 'error'=>true, 'message'=>'Essa página expirou, atualize a página e tente novamente.', 'fields'=>array(), 'errorInfo'=>array(), 'uid'=>0));
        }
    }

    /**
     * Verifica se os dados obrigatórios enviados não estão vazios
     * Se não houver error retorna um array com error true
     * Havendo erros ele retorna o field com os names dos campos
     *
     * @param array $post
     * @return array
     */
    private function validarCadastro(array $post=array())
    {
        $return = array('status'=>true, 'error'=>true, 'message'=>"Por favor, verifique os campos em vermelho.", 'fields'=>array(), 'errorInfo'=>array());
        if(empty($post)) {
            $return['message'] = "Nenhum dado foi enviado";
        }else{
            if(empty($post['prestadortipo_id'])){
                array_push($return['fields'], array('name'=>'prestadortipo_id', 'message'=>'Selecione um tipo de prestador.'));
            }
            if(empty($post['users_password'])){
                array_push($return['fields'], array('name'=>'users_password', 'message'=>'Informe uma senha.'));
            }
            if(empty($post['prestador_nome'])){
                array_push($return['fields'], array('name'=>'prestador_nome', 'message'=>'Informe um nome ou razão social.'));
            }
            if(empty($post['prestador_telefone1'])){
                array_push($return['fields'], array('name'=>'prestador_telefone1', 'message'=>'Informe um número de telefone.'));
            }
            if(empty($post['aceite'])){
                array_push($return['fields'], array('name'=>'aceite', 'message'=>'Leia e aceite os termos para continuar.'));
            }

            /* Verifica se o CEP foi preenchido e se ele existe */
            $post['endereco_cep'] = isset($post['endereco_cep'])?filter_var($post['endereco_cep'], FILTER_SANITIZE_STRING):"";
            if(!empty($post['endereco_cep'])){
                $Cep = new Cep();
                $Cep->buscar($post['endereco_cep']);
                if($Cep->rowCount() == 0){
                    array_push($return['fields'], array('name'=>'endereco_cep', 'message'=>'Informe um cep válido.'));
                }
            }else{
                array_push($return['fields'], array('name'=>'endereco_cep', 'message'=>'Informe um cep válido.'));
            }

            /* Verifica se o email foi preenchido corretamente e se não existe um usuário com esse email */
            $post['users_username'] = isset($post['users_username'])?filter_var($post['users_username'], FILTER_SANITIZE_EMAIL):"";
            if(!empty($post['users_username'])){
                $Users = new Users();
                $Users->pegar('users_username=:uname', array(':uname'=>$post['users_username']));
                if($Users->rowCount()) {
                    array_push($return['fields'], array('name'=>'users_username', 'message'=>'Já existe um usuário com o email informado.'));
                }
            }else{
                array_push($return['fields'], array('name'=>'users_username', 'message'=>'Informe um email válido.'));
            }

            /* Verifica se o cnpj ou cpf foi preenchido corretamente e se não existe um usuário com esse cnpj ou cpf */
            $post['prestador_cpf_cnpj'] = isset($post['prestador_cpf_cnpj'])?filter_var($post['prestador_cpf_cnpj'], FILTER_SANITIZE_STRING):"";
            if(!empty($post['prestador_cpf_cnpj']) and isset($post['radio_cpf_cnpj'])){
                $Prestador = new Prestador();
                if($post['radio_cpf_cnpj']==1){
                    /* CPF */
                    if(parent::validaCpf($post['prestador_cpf_cnpj'])){
                        $Prestador->pegar('prestador_cpf=:cpf', array(':cpf'=>$post['prestador_cpf_cnpj']));
                        if($Prestador->rowCount()) {
                            array_push($return['fields'], array('name'=>'prestador_cpf_cnpj', 'message'=>'Já existe um usuário com esse CPF.'));
                        }
                    }else{
                        array_push($return['fields'], array('name'=>'prestador_cpf_cnpj', 'message'=>'Informe um CPF válido.'));
                    }
                }else{
                    /* CNPJ */
                    if(parent::validaCnpj($post['prestador_cpf_cnpj'])){
                        $Prestador->pegar('prestador_cnpj=:cnpj', array(':cnpj'=>$post['prestador_cpf_cnpj']));
                        if($Prestador->rowCount()) {
                            array_push($return['fields'], array('name'=>'prestador_cpf_cnpj', 'message'=>'Já existe uma empresa com esse CPNJ cadastrado em nosso sistema.'));
                        }
                    }else{
                        array_push($return['fields'], array('name'=>'prestador_cpf_cnpj', 'message'=>'Informe um CNPJ válido.'));
                    }
                }
            }else{
                array_push($return['fields'], array('name'=>'prestador_cpf_cnpj', 'message'=>'Informe um CPF ou CNPJ válido.'));
            }


            if(count($return['fields']) == 0){
                $return['status'] = true;
                $return['error'] = false;
                $return['message'] = "Formulário correto.";
            }
        }

        return $return;
    }

    /**
     * Verifica se a página precisa ser autenticada
     *
     * @return bool
     */
    protected static function hasAuth()
    {
        return self::$authenticated;
    }

    /*
     * Privilégios de acesso a essa página
     */
    private function myPrivilege($privilege='')
    {
        if(parent::hasPrivilege($privilege)){
            new Error(505);
            exit;
        }
    }
}