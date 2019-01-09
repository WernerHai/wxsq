<?php
/**
 * Created by JetBrains PhpStorm.
 * 通用数据库操作类
 */
/* 使用
 require 'Query/M.class.php';

$hj = new M("user");
//增
echo $hj->add(array("NULL","1@qq.com","xxx"));
echo $hj->add(array("email"=>"12@qq.com","password"=>"cccc"));
echo $hj->add("NULL,'123@qq.com','cde'");

//查
//查看所有数据
$arr = $hj->select();
print_r($arr);
//查看id大于3且id小于6 的id 和email 字段 的所有数据
$arr = $hj->select("id>3 and id<6","id,email");
print_r($arr);
//获取数据库中一共有多少条记录
$rt = $hj->select("1","*","count");
echo $rt;

//查一个
$arr = $hj->find();

// 改
$b = $hj->update("id=1","email = '1234@qq.com'");
echo $b;
$b = $hj->update("id>3 and id<6",array("email"=>"1234@qq.com"));
echo $b;

// 删
$b = $hj->delete("id>3");
echo $b;

//关闭
$hj->close();
 */
class M
{

    private $link; //数据库连接
    private $table; //表名
    private $prefix; //表前缀
    private $db_configarray; //数据库配置
    public static $wlink;//用于外部连接

    /**
     * 参数:表名 数据库配置数组 或 数据库配置文件路径
     * @param $table
     * @param string $db_config_arr_path
     */
    function __construct($table)
    {

        $this->conn();
        $this->table = $this->prefix . $table;
    }

    /**
     * 连接数据库
     */
    private function conn()
    {
        require(dirname(__FILE__) . '/../data/config.php');
        $db_name = $dbname;
        $db_encode = 'utf8';
        $this->prefix = 'weixin_';
        $this->link = mysql_connect("{$host}:{$port}", $user, $pwd) or die('数据库服务器连接错误:' . mysql_error());
        M::$wlink = $this->link;
        mysql_select_db($db_name) or die('数据库连接错误:' . mysql_error());
        mysql_query('set names "'.$db_encode.'"');
    }

    /**
     * 数据查询
     * 参数:sql条件 查询字段 使用的sql函数名
     * @param string $where
     * @param string $field
     * @param string $fun
     * @return array
     * 返回值:结果集 或 结果(出错返回空字符串)
     */
    public function select($where = '1', $field = "*", $fun = '', $type = 'assoc')
    {
        $rarr = array();
        if (empty($fun)) {
            $sqlStr = "select $field from $this->table where $where";
            $rt = mysql_query($sqlStr, $this->link);
            if ($type == "row") {
                while ($rt && $arr = mysql_fetch_row($rt)) {
                    array_push($rarr, $arr);
                }
            } else {
                while ($rt && $arr = mysql_fetch_assoc($rt)) {
                    array_push($rarr, $arr);
                }

            }
        } else {
            $sqlStr = "select $fun($field) as rt from $this->table where $where";
            $rt = mysql_query($sqlStr, $this->link);
            if ($rt) {
                if ($type == "row") {
                    $arr = mysql_fetch_row($rt);
                } else {
                    $arr = mysql_fetch_assoc($rt);
                }
                $rarr = $arr['rt'];
            } else {
                $rarr = '';
            }
        }
        return $rarr;
    }

    /**
     * 数据查询
     * 参数:sql条件 查询字段 使用的sql函数名
     * @param string $where
     * @param string $field
     * @param string $fun
     * @return array
     * 返回值:结果集 或 结果(出错返回空字符串)
     */
    public function find($where = '1', $field = "*", $fun = '', $type = 'assoc')
    {
        $rarr = array();
        if (empty($fun)) {
            $sqlStr = "select $field from $this->table where $where";
            $rt = mysql_query($sqlStr, $this->link);
            if ($type == "row") {
                $rarr = mysql_fetch_row($rt);
            } else {
                $rarr = mysql_fetch_assoc($rt);
            }
        } else {
            $sqlStr = "select $fun($field) as rt from $this->table where $where";
            $rt = mysql_query($sqlStr, $this->link);
            if ($rt) {
                if ($type == "row") {
                    $arr = mysql_fetch_row($rt);
                } else {
                    $arr = mysql_fetch_assoc($rt);
                }
                $rarr = $arr['rt'];
            } else {
                $rarr = '';
            }
        }
        return $rarr;
    }
    public function findarray($where = '1', $field = "*"){
        $sqlStr = "select $field from $this->table where $where";
        $arr = mysql_fetch_array($sqlStr);
        return $arr;
    }
    /**
     * 数据更新
     * 参数:sql条件 要更新的数据(字符串 或 关联数组)
     * @param $where
     * @param $data
     * @return bool
     * 返回值:语句执行成功或失败,执行成功并不意味着对数据库做出了影响
     */
    public function update($where, $data)
    {
        $ddata = '';
        if (is_array($data)) {
            while (list($k, $v) = each($data)) {
                if (empty($ddata)) {
                    $ddata = "$k='$v'";

                } else {
                    $ddata .= ",$k='$v'";
                }
            }
        } else {
            $ddata = $data;
        }
        $sqlStr = "update $this->table set $ddata where $where";
        return mysql_query($sqlStr);
    }

    /**
     * 数据添加
     * 参数:数据(数组 或 关联数组 或 字符串)
     * @param $data
     * @return int
     * 返回值:插入的数据的ID 或者 0
     */
    public function add($data)
    {
        $field = '';
        $idata = '';
//        echo var_export(array_keys($data));
//        echo var_export(range(0, count($data) - 1));
        if (is_array($data) && array_keys($data) != range(0, count($data) - 1)) {
            //关联数组
            while (list($k, $v) = each($data)) {
                if (empty($field)) {
                    $field = "$k";
                    $idata = "'$v'";
                } else {
                    $field .= ",$k";
                    $idata .= ",'$v'";
                }
            }
            $sqlStr = "insert into $this->table($field) values ($idata)";
        } else {
            //非关联数组 或字符串
            if (is_array($data)) {
                while (list($k, $v) = each($data)) {
                    if (empty($idata)) {
                        $idata = "NULL";
                    } else {
                        $idata .= ",'$v'";
                    }
                }

            } else {
                //为字符串
                $idata = $data;
            }
            $sqlStr = "insert into $this->table values ($idata)";
        }

        if (mysql_query($sqlStr, $this->link)) {
            return mysql_insert_id($this->link);
        }
        return 0;
    }

    public function insert_id(){
        return mysql_insert_id($this->link);
    }
    /**
     * 执行sql语句
     *
    */
    public function query($sql){
        return mysql_query($sql,$this->link);
    }
    /**
     * 数据删除
     * 参数:sql条件
     * @param $where
     * @return bool
     */
    public function delete($where)
    {
        $sqlStr = "delete from $this->table where $where";
        return mysql_query($sqlStr);
    }
    /**
     * 显示数据库
     * 参数:sql条件
     * @param $sql sql语句
     * @return array
     * 返回值:结果集 或 结果(出错返回空字符串)
     */
    public function showdatabase($sql){

    }
    /**
     * 关闭MySQL连接
     * @return bool
     */
    public function close()
    {
        return mysql_close($this->link);
    }

}
