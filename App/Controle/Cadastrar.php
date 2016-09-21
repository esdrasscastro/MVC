<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 17/08/2016
 */

namespace Controle;

use Lib\Sistema;
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
        parent::header('Home');
        require_once(self::$htmlPath."cadastro/index.phtml");
        parent::footer();
    }

    /**
     * Retorna um json com o endereco do cep informado
     *
     * @param string $cep
     */
    public function buscarcep($cep='')
    {
        $Cep = new Cep();
        $Cep->buscar($cep);
        if($Cep->rowCount()){
            echo json_encode(array('status'=>200, 'error'=>false, 'message'=>'Cep encontrado', 'data'=>array(
                'endereco_cep'=>$Cep->results()->getEnderecoCep(),
                'endereco_logradouro'=>$Cep->results()->getEnderecoLogradouro(),
                'endereco_bairro_nome'=>$Cep->results()->getBairroNome(),
                'endereco_cidade_nome'=>$Cep->results()->getCidadeNome(),
                'endereco_estado_sigla'=>$Cep->results()->getEstadoSigla()
            )));
        }
        else echo json_encode(array('status'=>200, 'error'=>true, 'message'=>'Cep inválido!'));
    }

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
                        echo json_encode(array('status'=>true, 'error'=>false, 'message'=>'Usuário adicionado com sucesso!', 'fields'=>array(), 'errorInfo'=>$Users->getErrorInfo()));
                    }else{
                        $Users->rollBack();
                        echo json_encode(array('status'=>true, 'error'=>true, 'message'=>'Falha ao tentar adicionar o usuário', 'fields'=>array(), 'errorInfo'=>$Users->getErrorInfo()));
                    }
                }else{
                    $Users->rollBack();
                    echo json_encode(array('status'=>true, 'error'=>true, 'message'=>'Falha ao tentar adicionar o usuário', 'fields'=>array(), 'errorInfo'=>$Users->getErrorInfo()));
                }
            } else {
                echo json_encode($validate);
            }
        }else{
            echo json_encode(array('status'=>true, 'error'=>true, 'message'=>'Essa página expirou, atualize a página e tente novamente.', 'fields'=>array(), 'errorInfo'=>array()));
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
        $return = array('status'=>true, 'error'=>true, 'message'=>'Por favor, verifique os campos em vermelho.', 'fields'=>array(), 'errorInfo'=>array());
        if(empty($post)) {
            $return['message'] = "Nenhum dado foi enviado";
        }else{
            if(empty($post['prestadortipo_id'])){ array_push($return['fields'], 'prestadortipo_id');}
            if(empty($post['users_username'])){ array_push($return['fields'], 'users_username');}
            if(empty($post['users_password'])){ array_push($return['fields'], 'users_password');}
            if(empty($post['prestador_nome'])){ array_push($return['fields'], 'prestador_nome');}
            if(empty($post['prestador_cpf_cnpj'])){ array_push($return['fields'], 'prestador_cpf_cnpj');}
            if(empty($post['especialidade_id'])){ array_push($return['fields'], 'especialidade_id[]');}
            if(empty($post['exame_categoria_id'])){ array_push($return['fields'], 'exame_categoria_id[]');}
            if(empty($post['procedimento_id'])){ array_push($return['fields'], 'procedimento_id[]');}
            if(empty($post['endereco_cep'])){ array_push($return['fields'], 'endereco_cep');}
            if(empty($post['prestador_telefone1'])){ array_push($return['fields'], 'prestador_telefone1');}
            if(empty($post['aceite'])){ array_push($return['fields'], 'aceite');}

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
    public static function hasAuth()
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