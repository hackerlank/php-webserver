<?php
/**
 * 2014-03-06
 * @author wanglm
 *
 */
class sys_model
{
    public $_tableName = NULL;
    public $_partitionTable = NULL;
    public $_dbName = NULL;
    public $_partitionDb = NULL;
    public $_fields = array();

    public $_DbConf = NULL;
    public $_db = NULL;

    protected function getMapping(){
        //Must extends this function<getMapping>
        throw new ErrorException('Must extends this function<getMapping>');
    }
    
    public function __construct()
    {
        $this->init();
    }
    
    public function init()
    {
        /**
         * 要预留切库 以支持分库分表
         */
        $this->_db = sys_db::getInstance(sys_define::$main_db);
        
        $this->_dbName      = $this->getDbName();
        $this->_tableName   = $this->getTableName();
        $this->_fields      = $this->getMapping();
    }

    /**
     * 
     * @return mixed|NULL
     */
    protected function getDbName()
    {
        if ($this->_dbName)
        {
            return $this->_dbName;
        }
        if(sys_define::$main_db)
        {
            return sys_define::$main_db['dbn'];
        }
        return NULL;
    }

    protected function getTableName()
    {
        if ($this->_tableName)
        {
            return $this->_tableName;
        }
        $aClassName = explode("_", get_class($this));
        return array_pop($aClassName);
    }
    
    /**
     * 分表使用
     * @param type $oModel
     * @return type
     */
    protected function getRealTableName($oModel)
    {
        $reg = "/\[#{3}\]/";
        $tableName = $oModel->getTableName();
        if (!empty($oModel->_partitionTable) && preg_match($reg,$tableName))
        {
            return preg_replace($reg, $oModel->_partitionTable, $tableName);
        }
        return $oModel->_tableName;
    }
    /**
     * 分库使用
     * @param type $oModel
     * @return type
     */
    protected function getRealDbName($oModel)
    {
        $reg = "/\[#{3}\]/";
        $dbName = $oModel->getDbName();
        if (!empty($oModel->_partitionDb) && preg_match($reg,$dbName))
        {
            return preg_replace($reg, $oModel->_partitionDb, $dbName);
        }
        return $oModel->_dbName;
    }

    public function getPk()
    {
        if (empty($this->_fields['pk']))
        {
            return array();
        }
        return explode(',', $this->_fields['pk']);
    }

    public function isPk($key)
    {
        if (empty($this->_fields['pk']))
        {
            return FALSE;
        }
        return in_array($key, explode(',', $this->_fields['pk']))? TRUE:FALSE;
    }

    public function array2Model($arr)
    {
        if (!empty($arr))
        {
            foreach ($arr as $key=>$value)
            {
                $this->$key = $value;
            }
        }
        return $this;
    }
    
    public function __set($name, $value) 
    {
        if(empty($this->_fields))
        {
            $this->_fields = $this->getMapping();
        }
        if(!empty($this->_fields['columns'][$name]))
        {
            switch (strtolower(preg_replace("/(\(\w+\))?/i", '', $this->_fields['columns'][$name]))){
                case 'tinyint':
                case 'smallint':
                case 'int' :
                case 'integer':
                case 'mediumint':
                case 'bit':
                case 'real':
                case 'double':
                case 'float':
                case 'decimal':
                case 'numeric':
                    $this->$name = $value+0;
                    break;
                
                case 'time':
                    $this->$name = $value;
                    break;
                case 'date':
                case 'year':
                    $this->$name = $value;
                    break;
                default:
                    $this->$name = $value;
                    break;
            }
        }
    }
}
