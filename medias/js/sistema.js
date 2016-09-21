/**
 * Created by Esdras Castro on 14/09/2016.
 */
var Global = {
    vars : {
        basePath : ''
    }
};

var Login = {
    init : function (){
        $('button[name="login[logar]"]').on('click',function(){
            Login.auth($(this).closest('form'));
            return false;
        });

        $('button[name="login[recuperar]"]').on('click',function(){
            Login.recover($(this).closest('form'));
            return false;
        });
    },
    auth : function(form){
        $.ajax({
            url :form.attr('action'),
            type : 'POST',
            data : form.serialize(),
            dataType: 'JSON',
            success : function (response) {
                if(response.status > 0) {
                    Materialize.toast(response.message, 15000);
                    location.href = form.data('redirect');
                }else{
                    Materialize.toast(response.message, 15000);
                }
            },
            error : function(xhr, text){
                Materialize.toast(xhr.statusText, 4000);
            }
        });
    },
    recover : function(form){
        console.log('recuperar');
    }
};

var BuscaCep = function(form){
    var cep = $('#endereco_cep');
    var vcep = cep.val();
    var log = $('#endereco_logradouro');
    var bai = $('#endereco_bairro_nome');
    var cid = $('#endereco_cidade_nome');
    var est = $('#endereco_estado_sigla');
    $.ajax({
        url : Global.vars.basePath + 'cadastrar/buscarcep/' + vcep,
        type : 'GET',
        dataType : 'json',
        beforeSend : function(){
            cep.val('').attr('placeholder','Carregando...').prop('disabled',true);
        },
        success : function(response){
            cep.prop('disabled',false).attr('placeholder','').val(vcep);

            if(!response.error){
                log.val(response.data.endereco_logradouro).addClass('valid').closest('.input-field').find('label').addClass('active');
                bai.val(response.data.endereco_bairro_nome).addClass('valid').closest('.input-field').find('label').addClass('active');
                cid.val(response.data.endereco_cidade_nome).addClass('valid').closest('.input-field').find('label').addClass('active');
                est.val(response.data.endereco_estado_sigla).addClass('valid').closest('.input-field').find('label').addClass('active');
            }else{
                cep.removeClass('valid').addClass('invalid').closest('.input-field').find('label').addClass('active');
                log.val('').removeClass('valid');
                bai.val('').removeClass('valid');
                cid.val('').removeClass('valid');
                est.val('').removeClass('valid');
            }
        },
        error : function(xhr, statusText){
            cep.closest('.input-field').find('label').data('error',statusText);
        }
    });
};

var Cadastrar = {
    enviar : false,
    init : function(){
        $('#endereco_cep')
            .mask('99999-999', {
                onComplete : function(cep){
                    BuscaCep($(this).closest('form'));
                }
            });

        $('#prestador_telefone1, #prestador_telefone2').mask(function (val) {
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
        });

        $('#prestador_crm').mask('000009999/ZZ',{translation: {'Z': {pattern: /[a-zA-Z]/, optional: true}}});

        $('#prestador_cpf_cnpj').mask(function (val) {
            return (parseInt($('input[name="radio_cpf_cnpj"]:checked').val()) == 1)? '000.000.000-00' : '00.000.000/0000-00';
        });

        $('#cadastro_prestadores').on('submit', function(event){
            event.preventDefault();
            var countErro = 0;
            var msgErro = "Verifique estes erros antes de continuar.<br><br>";

            if($('input[name="exame_categoria_id[]"]:checked').length == 0){
                countErro++;
                msgErro += "- Por favor selecione ao menos um tipo de exame.<br>";
            }
            if($('select[name="procedimento_id[]"]').closest('div').find('ul li.active').length == 0){
                countErro++;
                msgErro += "- Por favor selecione ao menos um procedimento.<br>";
            }
            if($('select[name="especialidade_id[]"]').closest('div').find('ul li.active').length == 0){
                countErro++;
                msgErro += "- Por favor selecione ao menos uma especialidade.<br>";
            }


            if(countErro > 0){
                $('.submit_btn_div').addClass('m2');
                $('.msg_error').fadeIn().html(msgErro);
                Cadastrar.enviar = false;
            }else{
                $('.submit_btn_div').removeClass('m2');
                $('.msg_error').fadeOut();
                Cadastrar.enviar = true;
            }

            return false;
        }).validate({
            rules : {
                users_username : {
                    required : true,
                    email : true
                },
                users_password : {
                    required : true,
                    minlength : 6
                },
                conf_senha : {
                    required : true,
                    equalTo : "#users_password"
                },
                prestador_site : {
                    url : true
                },
                prestador_nome : {
                    required : true
                },
                endereco_cep : "required",
                procedimento_id : "required",
                especialidade_id : "required",
                prestador_cpf_cnpj : "required",
                prestador_crm_responsavel : "required",
                prestador_telefone1: "required",
                aceito: "required"
            },
            messages: {
                users_username:{
                    required: "Informe um email de acesso.",
                    email: "Por favor, informe um email válido."
                },
                users_password:{
                    required: "Digite uma senha de acesso.",
                    minlength: "A sua senha deve conter ao menos 6 dígitos."
                },
                conf_senha : {
                    required : "Digite uma senha de confirmação.",
                    equalTo: "As senhas não conferem."
                },
                endereco_cep: "Digite um CEP.",
                prestador_site: "Digite uma URL válida!",
                prestador_nome: "Informe o nome ou a razão social.",
                prestador_cpf_cnpj: "Informe um cpf ou cnpj válido.",
                prestador_crm_responsavel: "Informe o CRM ou o nome do responsável",
                prestador_telefone1: "Informe um telefone fixo ou celular",
                aceito: "Leia e aceite os termos para se cadastrar."
            },
            errorClass : 'invalid',
            validClass : 'valid',
            errorElement : 'span',
            errorPlacement: function(error, element) {
                $(element).closest('.input-field').find('label').attr('data-error', error.insertAfter(element).text());
                $(element).closest('.input-field').find('span.invalid').remove();
            },
            submitHandler : function(form){
                form = $(form);
                var btnsubmit = form.find('button[type=submit]');
                if(Cadastrar.enviar){
                    $.ajax({
                        url : form.attr('action'),
                        dataType : 'json',
                        type : form.attr('method'),
                        data : form.serialize(),
                        beforeSend : function(){
                            Loader.show();
                            btnsubmit.prop('disabled', true).text('Enviando aguarde...');
                        },
                        success : function(response) {
                            Loader.hide();
                            btnsubmit.prop('disabled', false).text('Enviar');

                            console.log(response);
                        },
                        error : function (xhr, statusText) {
                            Loader.hide();
                            btnsubmit.prop('disabled', false).text('Enviar');


                            console.log(statusText);
                        }
                    });
                }
            }
        });
    }
};

/**
 * Mostra ou oculta a tela de carregamento
 * @type {{element: (any), show: Loader.show, hide: Loader.hide}}
 */
var Loader = {
    element : $('#loader'),
    show : function () {
        Loader.element.fadeIn();
    },
    hide : function () {
        Loader.element.fadeOut();
    }
};