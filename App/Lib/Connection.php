<?
/**
 * Class Connection
 * @Version 1.0
 * @author Esdras Castro
 */

namespace Lib;

class Connection
{
	private static $conn = null;
    private static $readyTable = '';
    private static $erroInfo = array();

    /**
     * @param string $dsn
     * @param $username
     * @param $passwd
     * @param array $options
     * @return null|PDO
     */
    public static function connect($dsn='',$username, $passwd, array $options = array())
    {
        if(empty(self::$conn)){
            try{
                self::$conn = new \PDO($dsn, $username, $passwd, $options);
            }catch(\PDOException $err){
                die($err->getMessage());
            }
        }

        return self::$conn;
    }

    /**
     * @return integer
     */
    public static function errorCode()
    {
        return self::$conn->errorCode();
    }

    /**
     * @return array
     */
    public static function errorInfo()
    {
        return self::$erroInfo;
    }

    /**
     * retorna o id da ultima inserção
     *
     * @return integer
     */
    public static function lastInsertId()
    {
        return self::$conn->lastInsertId();
    }

    /**
     * @param string $table
     * @param string $instruction
     * @param array $bind
     * @param string $join
     * @param string $fields
     * @return bool|object
     */
    public static function select($table='', $instruction='', array $bind=array(), $join='', $fields='*', $className=null)
    {
        if(!empty($table))
        {
            $instruction = (empty($instruction))?'':'WHERE '.$instruction;
            $sql = "SELECT {$fields} FROM {$table} {$join} {$instruction};";
            $rs = self::run($sql, $bind, $className);

            return $rs;
        }

        return false;
    }

    /**
     * @param string $table
     * @param array $data
     * @param string $instruction
     * @param array $bind
     * @return int
     */
    public static function update($table='', array $data=array(), $instruction='', array $bind=array())
    {
        $instruction = (empty($instruction))?die("Informe a condição de atualização."):$instruction;
        if(empty($data) and empty($table))return false;

        self::$readyTable = $table;
        $binded = self::filter($data, true);
        $fields = self::fieldsToUpdate($binded);

        $binded = self::bind($binded);

        foreach ($bind as $key=>$val) $binded[$key] = $val;

        $sql = "UPDATE {$table} SET {$fields} WHERE {$instruction};";
        return self::execute($sql, $binded);
    }

    /**
     * @param string $table
     * @param array $data
     * @return int
     */
    public static function insert($table='', array $data=array())
    {
        if(empty($data) and empty($table))return false;

        self::$readyTable = $table;
        $bind = self::filter($data);
        $fields = self::fieldsToInsert($bind);
        $bind = self::bind($bind);

        $sql = "INSERT INTO {$table} {$fields};";
        return self::execute($sql, $bind);
    }

    /**
     * @param string $table
     * @param array $data
     * @return int
     */
    public static function replace($table='', array $data=array())
    {
        if(empty($data) and empty($table))return false;

        self::$readyTable = $table;
        $bind = self::filter($data);
        $fields = self::fieldsToInsert($bind);
        $bind = self::bind($bind);

        $sql = "REPLACE INTO {$table} {$fields};";
        return self::execute($sql, $bind);
    }

    /**
     * @param string $table
     * @param string $instruction
     * @param array $bind
     * @return bool|int
     */
    public static function delete($table='', $instruction='',array $bind=array())
    {
        $instruction = (empty($instruction))?die("Informe a condição de remoção."):$instruction;
        if(empty($bind) and empty($table))return false;

        if(self::select($table, $instruction, $bind)->rowCount) {
            $sql = "DELETE FROM {$table} WHERE {$instruction};";
            return self::execute($sql, $bind);
        }

        return 0;
    }

    /**
     * @param $data
     * @param bool $original
     * @return string
     */
    public static function dataToDB($data, $original=false)
    {
        if($original) return $data;
        $newdata1 = explode('-',$data);
        $newdata2 = explode('/',$data);
        if(count($newdata1) == 3 or count($newdata2) == 3)
        {
            if(count($newdata1) == 3){
                $len1 = strlen($newdata1[0]);
                $len2 = strlen($newdata1[1]);
                $len3 = strlen($newdata1[2]);
            }else{
                $len1 = strlen($newdata2[0]);
                $len2 = strlen($newdata2[1]);
                $len3 = strlen($newdata2[2]);
            }

            if($len1 == 4 and $len2 == 2 and $len3 == 2) return $newdata1[0].'-'.$newdata1[1].'-'.$newdata1[2];
            else if($len1 == 2 and $len2 == 2 and $len3 == 4) return $newdata1[2].'-'.$newdata1[1].'-'.$newdata1[0];
        }

        return '0000-00-00';
    }

    /**
     * @param $data
     * @param string $separator
     * @param bool $original
     * @return string
     */
    public static function dataToScreen($data, $separator='/', $original=false)
    {
        if($original) return $data;
        $newdata1 = explode('-',$data);
        $newdata2 = explode('/',$data);
        if(count($newdata1) == 3 or count($newdata2) == 3)
        {
            if(count($newdata1) == 3){
                $len1 = strlen($newdata1[0]);
                $len2 = strlen($newdata1[1]);
                $len3 = strlen($newdata1[2]);
            }else{
                $len1 = strlen($newdata2[0]);
                $len2 = strlen($newdata2[1]);
                $len3 = strlen($newdata2[2]);
            }

            if($len1 == 4 and $len2 == 2 and $len3 == 2) return $newdata1[2].$separator.$newdata1[1].$separator.$newdata1[0];
            else if($len1 == 2 and $len2 == 2 and $len3 == 4) return $newdata1[0].$separator.$newdata1[1].$separator.$newdata1[2];
        }

        return '00/00/0000';
    }

    /**
     * @param array $bind
     * @return string
     */
    public static function bind(array $bind = array())
    {
        if(!empty($bind))
        {
            $auxiliar = $bind;
            $bind = array();
            foreach($auxiliar as $key=>$val)
            {
                $bind[':bind_'.str_replace(' ','_',$key)] = (!empty($val))?$val:null;
            }
        }

        return $bind;
    }

    /**
     * @param $data
     * @param $tableInfo
     * @return array
     */
    private static function filter($data, $noAutoIncrement=false)
    {
        $tableInfo = self::describe();
        if(count($data) > 0 and count($tableInfo) > 0)
        {
            $newArray = array();
            for($i=0;$i<count($tableInfo);$i++) {
                if(in_array($tableInfo[$i]['Field'], array_keys($data))){
                    if($noAutoIncrement){
                        if($tableInfo[$i]['Extra']!='auto_increment')
                            $newArray[$tableInfo[$i]['Field']] = $data[$tableInfo[$i]['Field']];
                    }else{
                        $newArray[$tableInfo[$i]['Field']] = $data[$tableInfo[$i]['Field']];
                    }

                }
            }
            return $newArray;
        }

        return $tableInfo;
    }

    /**
     * @param array $fields
     * @return string
     */
    private static function fieldsToInsert(array $fields = array())
    {
        $auxiliar = '';
        if(!empty($fields))
        {
            $auxiliar .= '('.implode(',', array_keys($fields)).') VALUES (';
            foreach($fields as $key=>$val)
            {
                $auxiliar .= ':bind_'.str_replace(' ','_',$key).',';
            }
            $auxiliar = trim($auxiliar, ',');
            $auxiliar .= ')';
        }

        return $auxiliar;
    }

    /**
     * @param array $fields
     * @return string
     */
    private static function fieldsToUpdate(array $fields = array())
    {
        $auxiliar = '';
        if(!empty($fields))
        {
            foreach($fields as $key=>$val)
            {
                $auxiliar .= str_replace(' ','_',$key).'=:bind_'.str_replace(' ','_',$key).', ';
            }
            $auxiliar = trim($auxiliar, ', ');
        }

        return $auxiliar;
    }

    /**
     * @param $sql
     * @param array $bind
     * @return int
     */
    private static function execute($sql, array $bind = array())
    {
        if(empty($sql) || self::$conn === null || !(self::$conn instanceof \PDO)) return false;

        $stmt = self::$conn->prepare($sql);
        if($stmt->execute((!empty($bind))?$bind:'')){
            return $stmt->rowCount();
        }

        self::$erroInfo = $stmt->errorInfo();

        return 0;
    }

    /**
     * @param $sql
     * @param array $bind
     * @return bool|object
     */
    private static function run($sql, array $bind = array(), $className=null)
    {
        if(empty($sql) || self::$conn === null || !(self::$conn instanceof \PDO)) return false;

        $stmt = self::$conn->prepare($sql);
        $bind = !empty($bind)?$bind:null;
        $rs = (object)null;

        if($stmt->execute($bind)){
            if($stmt->rowCount() == 1)
            {
                if(is_null($className)){
                    $rs->results = $stmt->fetchObject();
                }else{
                    $rs->results = $stmt->fetchObject($className);
                }

                $rs->rowCount = 1;
                self::$erroInfo = $stmt->errorInfo();
                return $rs;
            } else if($stmt->rowCount() > 1) {
                if(is_null($className)){
                    $rs->results = $stmt->fetchAll(\PDO::FETCH_CLASS);
                }else{
                    $rs->results = $stmt->fetchAll(\PDO::FETCH_CLASS, $className);
                }

                $rs->rowCount = $stmt->rowCount();
                self::$erroInfo = $stmt->errorInfo();
                return $rs;
            }
        }
        return false;
    }

    public static function beginTransaction(){
        self::$conn->beginTransaction();
    }

    public static function commit(){
        self::$conn->commit();
    }

    public static function rollBack(){
        self::$conn->rollBack();
    }

    /**
     * @return array
     */
    private static function describe()
    {
        $stmt = self::$conn->prepare("DESCRIBE ".self::$readyTable.";");
        if($stmt->execute()) {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }else{
            return array();
        }

    }
}

