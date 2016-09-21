<?php
/**
 * Created by Grupo B+M
 * User: Esdras Castro
 * Date: 14/09/2016
 */

namespace Modelo;


interface InterfaceDbModelo
{
    /**
     * Seta o nome da tabela a ser usada
     *
     * @param string $string
     * @return \stdClass
     */
    public function setTableName($string='');

    /**
     * Seta o nome da tabela view a ser usada
     *
     * @param string $string
     * @return \stdClass
     */
    public function setTableView($string='');

    /**
     * Retorna o resultado de uma consulta SQL.
     * Quando o resultado é de apenas um único registro é
     * retornado uma objeto do resultado. Caso o $singleResult seja falso
     * o resultado é obtido da mesma forma que o de multiplos registros.
     *
     * @param boolean $singleResult
     * @return object
     */
    public function results($singleResult=true);

    /**
     * Retorna o número de registros da consulta
     *
     * @return integer
     */
    public function rowCount();

    /**
     * Faz uma consulta a uma tabela informada no setTableName
     * Este método retorna a própria classe
     *
     * @param string $whr
     * @param array $bind
     * @param string $join
     * @param string $field
     * @param int $limit
     * @param int $offset
     * @param string $orderby
     * @param string $order
     * @return \stdClass
     */
    public function pegar($whr='', array $bind = array(), $join='', $field='*', $limit=0, $offset=0, $orderby='', $order='');

    /**
     * Adicionar um registro a tabela corrente.
     *
     * retorna o id da inserção
     *
     * @return integer
     */
    public function adicionar();

    /**
     * Alterar um registro a tabela corrente.
     *
     * @return boolean
     */
    public function editar();

    /**
     * Remove um registro da tabela corrente.
     *
     * @return boolean
     */
    public function deletar();

    /**
     * Compara a instancia informada com os registros na tabela corrente.
     * Ele retorna true para o caso positivo
     *
     * @return boolean
     */
    public function comparar();

    /**
     * Altera o valor da coluna tablename_publico para true
     *
     * @return boolean
     */
    public function publicar();

    /**
     * Altera o valor da coluna tablename_publico para false
     *
     * @return boolean
     */
    public function despublicar();

    /**
     * Retorna as mensagens de erro da operação
     *
     * @return array
     */
    public function getErrorInfo();

    /**
     * Deixa apenas letras sem números ou caracteres especiais
     *
     * @param string $value
     * @return string
     */
    public function semCaracteresEspeciais($value='');
}