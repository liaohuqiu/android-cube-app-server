<?php
/**
 * ���͹淶�������ݽӿ�
 *
 * ���Ͳ���˵����
 *	'noclean'  - ��������
 *	'int'      - ת����integer
 *	'unit'     - ת�����޷���integer
 *  'num'      - ת����number
 *	'str'      - ת����string����ȥ�����ߵĿո�
 *	'notrim'   - ת����string�������ո�
 *	'file'     - ת����file����֧�������ύ
 *	'json'     - JSON
 *
 * @author http://www.liaohuqiu.net
 */
class MCore_Tool_Input
{
    private static $globalSources = array (
        'g' => '_GET',
        'p' => '_POST',
        'c' => '_COOKIE',
        'r' => '_REQUEST',
        'f' => '_FILES'
    );

    /**
     * ��ȡ����
     */
    public static function clean($source, $varname, $type = 'noclean', $default = null)
    {
        self::processMagicQuotes();

        $container = $GLOBALS[self::$globalSources[$source]];
        if (!isset($container[$varname]))
        {
            if ($default != null)
            {
                return $default;
            }
            $var = '';
        }
        else
        {
            $var = $container[$varname];
        }

        return self::cast($var, $type);
    }

    private static function processMagicQuotes()
    {
        static $hasProcessed = false;

        if (!$hasProcessed && get_magic_quotes_gpc())
        {
            $_GET = self::stripslashesDeep($_GET);
            $_POST = self::stripslashesDeep($_POST);
            $_COOKIE = self::stripslashesDeep($_COOKIE);
            $_REQUEST = self::stripslashesDeep($_REQUEST);
            $hasProcessed = true;
        }
    }

    private static function &cast($data, $type)
    {
        switch ($type)
        {
        case 'noclean':
            break;
        case 'int':
            $data = intval($data);
            break;
        case 'uint':
            $data = max(0, intval($data));
            break;
        case 'num':
            $data = $data + 0;
            break;
        case 'str':
            $data = trim(self::getStr($data));
            break;
        case 'notrim':
            $data = self::getStr($data);
            break;
        case 'file':
            if (!is_array($data))
            {
                $data = array(
                    'name'     => '',
                    'type'     => '',
                    'size'     => 0,
                    'tmp_name' => '',
                    'error'    => UPLOAD_ERR_NO_FILE,
                );
            }
            break;
        case 'json':
            $data = trim(self::getStr($data));
            if($data)
            {
                $data = json_decode($data,true);
            }
            else
            {
                $data = array();
            }
            break;
        default:
            throw new Exception('Unsupport type');
        }
        return $data;
    }

    private static function getStr($data)
    {
        return $data;
    }

    /**
     * �ݹ� stripslashes
     */
    private static function stripslashesDeep($value)
    {
        if (is_array($value))
        {
            foreach ($value as $sKey => $vVal)
            {
                $value[$sKey] = self::stripslashesDeep($vVal);
            }
        }
        else if (is_string($value))
        {
            return stripslashes($value);
        }
        return $value;
    }
}
