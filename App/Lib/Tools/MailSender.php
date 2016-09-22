<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 02/09/2016
 */

namespace Lib\Tools;


class MailSender
{
    private $to;
    private $from;
    private $replyTo;
    private $message;
    private $subject;
    private $headers;
    private $bcc;
    private $cc;
    private $priority;
    private $priorityAllowed = array();
    private $dispositionNotificationTo;
    private $domainsAllowed = array();
    private $linebreaks;
    private $errormsg;
    private $status;

    function __construct(){
        array_push($this->domainsAllowed, 'tempsite.ws', 'locaweb.com.br','hospedagemdesites.ws','websiteseguro.com', 'acheimed.com.br');
        array_push($this->priorityAllowed, 1,3,5);
        $this->errormsg = '';
        $this->priority = 3;
        $this->dispositionNotificationTo = '';
        $this->status = false;
    }

    /**
     * Retorna o status do envio do email
     *
     * @return bool
     */
    public function status()
    {
        return $this->status;
    }

    /**
     * Texto de assunto do email
     *
     * @param string $subject
     * @return $this
     */
    public function subject($subject='')
    {
        $this->subject = utf8_decode($subject);

        return $this;
    }

    /**
     * É a mensagem que será enviada no corpo do email
     * Ela pode ser em html ou texto simples
     *
     * @param string $htmlmsg
     * @return $this
     */
    public function message($htmlmsg='')
    {
        $this->message = utf8_decode($htmlmsg);

        return $this;
    }

    /**
     * E-mail que receberá confirmação de leitura (somente se recebido por algum cliente de e-mail, como o Outlook).
     *
     * @return $this
     */
    public function confirmationEmail($email='')
    {
        if(!empty($email) and !is_string($email)){
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            if(!empty($email)){
                $this->dispositionNotificationTo = $email;
            }
        }

        return $this;
    }

    /**
     * Insere um novo Destinarário
     *
     * @param string $nome
     * @param string $email
     * @return $this
     */
    public function addTo($nome='',$email='')
    {
        $nome = filter_var($nome, FILTER_SANITIZE_STRING);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if(!empty($email)){
            if(empty($nome)) array_push($this->to, $email);
            else $this->to[$nome] = $email;
        }else{
            if(!empty($nome)){
                $nome = filter_var($nome, FILTER_SANITIZE_EMAIL);
                if(!empty($nome)) $this->to[] = $nome;
            }
        }

        return $this;
    }

    /**
     * Insere um novo Com Cópia
     *
     * @param $nome
     * @param $email
     *
     * @return $this
     */
    public function addCc($nome='',$email='')
    {
        $nome = filter_var($nome, FILTER_SANITIZE_STRING);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if(!empty($email)){
            if(empty($nome)) array_push($this->cc, $email);
            else $this->cc[$nome] = $email;
        }else{
            if(!empty($nome)){
                $nome = filter_var($nome, FILTER_SANITIZE_EMAIL);
                if(!empty($nome)) $this->cc[] = $nome;
            }
        }

        return $this;
    }

    /**
     * Insere um novo Com Cópia Oculta
     *
     * @param $nome
     * @param $email
     *
     * @return $this
     */
    public function addBcc($nome='',$email='')
    {
        $nome = filter_var($nome, FILTER_SANITIZE_STRING);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if(!empty($email)){
            if(empty($nome)) array_push($this->bcc, $email);
            else $this->bcc[$nome] = $email;
        }else{
            if(!empty($nome)){
                $nome = filter_var($nome, FILTER_SANITIZE_EMAIL);
                if(!empty($nome)) $this->bcc[] = $nome;
            }
        }

        return $this;
    }

    /**
     * Insere um contato de from
     *
     * @param $nome
     * @param $email
     * @return $this
     */
    public function addFrom($nome='',$email='')
    {
        $nome = filter_var($nome, FILTER_SANITIZE_STRING);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if(!empty($email))
            $this->from[$nome] = $email;
        else{
            if(!empty($nome)){
                $nome = filter_var($nome, FILTER_SANITIZE_EMAIL);
                if(!empty($nome)) $this->from = $nome;
            }
        }

        return $this;
    }

    /**
     * Insere um responder para
     *
     * @param $nome
     * @param $email
     * @return $this
     */
    public function addReplyTo($nome='',$email='')
    {
        $nome = filter_var($nome, FILTER_SANITIZE_STRING);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if(!empty($email))
            $this->replyTo[$nome] = $email;
        else{
            if(!empty($nome)){
                $nome = filter_var($nome, FILTER_SANITIZE_EMAIL);
                if(!empty($nome)) $this->replyTo = $nome;
            }
        }

        return $this;
    }

    /**
     * Seta a prioridade do email [1: Urgente | 3: Normal | 5: Baixa]
     *
     * @param int $priority
     * @return $this
     */
    public function setPriority($priority=3)
    {
        if(in_array($priority, $this->priorityAllowed)) $this->priority = $priority;
        else $this->errormsg = "Por favor informe uma das prioridades [1: Urgente | 3: Normal | 5: Baixa]";

        return $this;
    }

    /**
     * Verifica se o domínio que está disparando o email faz parte dos domínios permitidos.
     * isso impede que mensagens sejam enviadas através de outros sites. Caso seja de outro
     * domínio, nós forçamos que seja através do nosso dominio.
     *
     * @return $this
     */
    protected function domainsAllowed()
    {
        if(!in_array($_SERVER['HTTP_HOST'], $this->domainsAllowed)){
            $this->from = "contato@" . $_SERVER['HTTP_HOST'];
        }

        return $this;
    }

    /**
     * Verifica se existe um email para enviar.
     *
     * @param string $variableName
     * @return boolean
     */
    protected function hasEmail($emails)
    {
        if(!empty($emails)){
            if(is_array($emails)){
                $emails = array_filter($emails);
                return !empty($emails);
            }else{
                $email = filter_var($emails, FILTER_SANITIZE_EMAIL);
                return !empty($email)?true:false;
            }
        }else{
            $this->errormsg = "Email não foi informado.";
        }

        return false;
    }

    /**
     * Monta a string com os emails formatados
     *
     * @param string $variableName
     * @return string
     */
    protected function preparingEmail($variableName='')
    {
        if(!empty($variableName) and property_exists($this, $variableName)){
            if(self::hasEmail($this->$variableName)){
                if(self::sistemaOperacional()==1){
                     if(is_array($this->$variableName)){
                         $string = '';
                         foreach ($this->$variableName as $nome=>$email) {
                             if (is_numeric($nome)) $string .= $email . ",";
                             else $string .= $nome . " <" . $email . ">,";
                         }

                         return trim($string, ',');
                     }else{
                         return $this->$variableName;
                     }
                }elseif(self::sistemaOperacional()==2){
                    if(is_array($this->$variableName)){
                        $string = '';
                        foreach ($this->$variableName as $nome=>$email)
                            $string .= $email.",";

                        return trim($string, ',');
                    }else{
                        return $this->$variableName;
                    }
                }
            }
        }

        return '';
    }

    /**
     * Verifica qual é o sistema operacional do servidor para ajustar o cabeçalho de forma correta. Não alterar
     * [1 : linux | 2: Windows | 0: Outro não suportado
     *
     * @return integer
     */
    protected function sistemaOperacional()
    {
        if(PHP_OS == "Linux") { $this->linebreaks = "\n"; return 1;}//Se for Linux
        elseif(PHP_OS == "WINNT") { $this->linebreaks = "\r\n"; return 2;}// Se for Windows
        else { $this->errormsg = "Este script nao esta preparado para funcionar com o sistema operacional de seu servidor"; return 0;}
    }

    /**
     * Monta o cabeçalho do email
     */
    protected function createHeader()
    {
        $bcc = self::preparingEmail('bcc');
        $cc = self::preparingEmail('cc');
        $from = self::preparingEmail('from');
        $reply = self::preparingEmail('replyTo');

        /* Montando o cabeçalho da mensagem */
        $headers = "MIME-Version: 1.1" . $this->linebreaks;
        $headers .= "Content-type: text/html; charset=iso-8859-1" . $this->linebreaks;
        $headers .= "From: " . $from . $this->linebreaks;
        $headers .= !empty($bcc) ? "Bcc: " . $bcc . $this->linebreaks : '';
        $headers .= !empty($cc) ? "Cc: " . $cc . $this->linebreaks : '';
        $headers .= !empty($this->priority) ? "X-Priority: " . $this->priority . $this->linebreaks : '';
        $headers .= "Reply-To: " . (!empty($reply) ? $reply . $this->linebreaks : $from . $this->linebreaks);

        $this->headers = $headers;

    }

    public function getMessage()
    {
        return $this->errormsg;
    }

    /**
     * Envia o email para o destino
     *
     * @return $this
     */
    public function send()
    {
        $from = self::preparingEmail('from');
        $to = self::preparingEmail('to');

        if(empty($this->errormsg)){
            if(empty($to) || empty($from)){
                $this->errormsg = "O email de remetente e/ou destinatário não foram informados.";
            } else {
                if(self::sistemaOperacional()==1){
                    if(@mail($to, $this->subject, $this->message, $this->headers, "-r" . $from)){
                        $this->status = true;
                    }else{
                        $this->errormsg = "Falha ao tentar enviar o seu email. Verifique a sua conexão com a internet e tente novamente.";
                    }
                } elseif (self::sistemaOperacional()==2) {
                    $this->headers .= "Return-Path: " . $from . $this->linebreaks;
                    if(@mail($to, $this->subject, $this->message, $this->headers)){
                        $this->status = true;
                    }else{
                        $this->errormsg = "Falha ao tentar enviar o seu email. Verifique a sua conexão com a internet e tente novamente.";
                    }
                }
            }
        }

        return $this;
    }
}


