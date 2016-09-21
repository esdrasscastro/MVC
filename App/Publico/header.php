<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 17/08/2016
 */
?>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>

        <!-- CSS -->
        <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <?=self::getStyleScript('css');?>
        <!-- JavaScript -->
        <title><?=self::$title;?></title>

        <style>
            body {display: flex;min-height: 100vh;flex-direction: column;}main{flex: 1 0 auto;}<?=self::getStyleScript('styleScript');?>
            input:not([type]) + label:after, input[type=text] + label:after, input[type=password] + label:after, input[type=email] + label:after, input[type=url] + label:after, input[type=time] + label:after, input[type=date] + label:after, input[type=datetime] + label:after, input[type=datetime-local] + label:after, input[type=tel] + label:after, input[type=number] + label:after, input[type=search] + label:after, textarea.materialize-textarea + label:after{top: 42px;}
            span.error{color: red;font-size: 14px;}
        </style>
    </head>
    <body>
        <header>
            <?
            if(parent::$controller == 'Home') {
                ?>
                <div class="barra_top">
                    <div class="container">
                        <div class="col m12 left-align">
                            <p>O AcheiMed é uma Plataforma Online de gerenciamento de custo <span>para pacientes sem plano de saúde.</span>
                            </p>
                        </div>
                    </div>
                </div>
                <?
            }
            ?>
            <div class="<?=parent::$controller == 'Home'?'bg-header-site':'bg-header'?>">
                <div class="container">
                    <nav class="nav-wrapper">
                        <a href="<?=parent::$basePath?>" class="brand-logo logo"><img src="<?=parent::$imagePath.(parent::$controller=='Home'?'logo_site.png':'logo.png');?>"></a>
                        <?=self::getMenuDesktop();?>
                        <?=self::getMenuMobile();?>
                        <a href="#" data-activates="slide-out" class="button-collapse"><i class="mdi mdi-menu btn-collapse-color"></i></a>
                    </nav>
                </div>
            </div>
            <? /*require_once parent::$publicoPath."breadcrumb.phtml";*/ ?>
        </header>
