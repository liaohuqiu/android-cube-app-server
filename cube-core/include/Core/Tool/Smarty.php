<?php
/**
 * Smarty
 *
 * @author huqiu
 */
if (!defined('SMARTY_WRITALE_DIR'))
{
    throw new Exception('SMARTY_WRITALE_DIR undifiend');
}
if (!defined('SMARTY_CLASS_PATH'))
{
    throw new Exception('SMARTY_CLASS_PATH undifiend');
}
include SMARTY_CLASS_PATH;
class MCore_Tool_Smarty implements MCore_Web_IViewDisplayer
{
    private $smarty;
    private $_inCharSet;
    private $_outCharSet;
    public static $disableCheckDir = false;

    public function __construct($templateDir, $inCharset = '', $outCharset = '')
    {
        $this->_inCharSet = $inCharset;
        $this->_outCharSet = $outCharset;
        $this->smarty = new Smarty();
        $this->smarty->left_delimiter = '{{';
        $this->smarty->right_delimiter = '}}';
        $this->smarty->compile_check = true;
        $this->smarty->caching = false;

        $this->smarty->template_dir = $templateDir;

        $this->_checkWriteDir();

        $this->smarty->loadFilter('variable', 'htmlspecialchars');

        $this->smarty->registerFilter('output', array($this, 'smarty_iconv'));
    }

    /**
     * ����Ŀ¼������д��Ȩ��
     */
    private function _checkWriteDir()
    {
        $rootPath = SMARTY_WRITALE_DIR;
        $compileDir = $rootPath . '/compile/';
        $cacheDir = $rootPath . '/cache/';
        $configDir = $rootPath . '/config/';

        if (!self::$disableCheckDir)
        {
            if (!$this->_makeDir($compileDir))
            {
                throw new Exception('�޷�����ģ�����Ŀ¼��' . $compileDir);
            }
            if (!$this->_makeDir($cacheDir))
            {
                throw new Exception('�޷�����cacheĿ¼��' . $cacheDir);
            }
            if (!$this->_makeDir($configDir))
            {
                throw new Exception('�޷�����configĿ¼��' . $configDir);
            }
        }
        else
        {
            $this->smarty->compile_locking = false;
        }

        $this->smarty->compile_dir = $compileDir;
        $this->smarty->cache_dir = $cacheDir;
        $this->smarty->config_dir = $configDir;
    }

    /**
     * �ж��ǲ��������dir�����û���Զ�������
     */
    private function _makeDir($dir)
    {
        return is_dir($dir) || mkdir($dir, 0777, true);
    }

    public function smarty_iconv($output, $smarty)
    {
        if (strlen($output) && $this->_inCharSet != $this->_outCharSet)
        {
            $output = self::processOutput($this->_inCharSet, $this->_outCharSet, $output);
        }
        return $output;
    }

    public static function processOutput($inCharset, $outCharset, $content)
    {
        if (strlen($content))
        {
            return iconv($content, $inCharset, $outCharset);
        }
        else
        {
            return $content;
        }
    }

    /**
     * �����ı� �򵥵���ΪJS����
     */
    public function assignEscapeSlash($var, $value)
    {
        self::processValueDeep($value, array(self, 'addSlashes'));
        $this->smarty->assign($var, $value);
    }

    /**
     * �����ı� ��ΪJS�����������������ҳ����ʾ
     */
    public function assignEscapeHtmlSlash($var, $value)
    {
        self::processValueDeep($value, array(self, 'addSlashesHtml'));
        $this->smarty->assign($var, $value);
    }

    /**
     * �����ı� ��ʾ
     */
    public function assignMultiline($var, $value)
    {
        self::processValueDeep($value, array(self, 'multiline'));
        $this->smarty->assign($var, $value);
    }

    /**
     * JSON����֧����������
     */
    public function assignJson($name, $value)
    {
        $this->smarty->assign($name, json_encode($value));
    }

    /**
     * ��HTML��Ϊ��ͨ�ı����õ��༭���У���֧���������� �� html�ı� �༭���༭
     */
    public function assignEditor($name, $value, $sTextType='html')
    {
        if($sTextType == 'plain')
        {
            $value = str_replace(array('&quot;', '&lt;', '&gt;', '&amp;'), array('\"', '<', '>', '&'),
                str_replace(array('<br />', '<br/>'), array('', ''), $value));
        }
        $value = str_replace(array("\n", "\r"), array("\\n", ''), self::addSlashes($value));
        $this->smarty->assign($name, $value);
    }

    /**
     * assign raw
     */
    public function setData($var, $value)
    {
        $this->smarty->assign($var, $value);
    }

    /**
     * ���ģ��
     */
    public function display($filePath, $clear = true)
    {
        try
        {
            $this->smarty->display($filePath);
        }
        catch(Exception $e)
        {
            throw $e;
        }
        if($clear)
        {
            $this->smarty->clearAllAssign();
        }
    }

    /**
     * ����ģ���滻������
     *
     * @return string
     */
    public function render($filePath, $clear=true)
    {
        try
        {
            $content = $this->smarty->fetch($filePath);
        }
        catch(Exception $e)
        {
            // smarty��������Ѿ����������
            $preContent = ob_get_clean();
            throw $e;
        }
        if($clear)
        {
            $this->smarty->clearAllAssign();
        }
        return $content;
    }

    /**
     * ���smarty��assign�ı���ֵ
     */
    public function getTemplateVars($var)
    {
        try
        {
            $value = $this->smarty->getTemplateVars($var);
        }
        catch(Exception $e)
        {
            $value = $e->getMessage();
        }
        return $value;
    }

    /**
     * ��fn�����ݹ鴦��var����
     */
    public function processValueDeep(&$var, $func)
    {
        if (is_array($var))
        {
            foreach ($var as $key => &$item)
            {
                self::processValueDeep($item, $func);
            }
        }
        else
        {
            if (!isset($var))
            {
                $var = '';
            }
            $var = call_user_func_array($func, $var);
        }
    }

    /**
     * ��װ addslashes �� forbidScript ����
     *
     * @return string
     */
    private static function addSlashes($input)
    {
        return addslashes(self::forbidScript($input));
    }

    /**
     * ��װ addslashes �� forbidScript �� htmlspecialchars ����
     *
     * @return string
     */
    private static function addSlashesHtml($input)
    {
        return addslashes(self::forbidScript(htmlspecialchars($input)));
    }

    private static function forbidScript($sText)
    {
        $sText = str_replace("\r", '', $sText);
        return preg_replace("/script/i", ' script ', $sText);
    }

    /**
     * ��װ nl2br �� htmlspecialchars ����
     *
     * @return string
     */
    private static function multiline($input)
    {
        return nl2br(htmlspecialchars($input));
    }

    /**
     * Does not support this funciton in smarty
     */
    public function addDir($path)
    {
    }
}
?>
