<?php
/**
 *   �������
 *
 * @author      huqiu
 */
class MCore_Cli_Condition
{
    private $_str;
    private $_type;
    private $_subConditions = array();

    public static function parse($str)
    {
        return new MCore_Cli_Condition($str);
    }

    public function __construct($str)
    {
        $this->_str = $str;

        if (!$this->_splitBracePair())
        {
            $this->_porcessNoBrace();
        }
    }

    /**
     * ����û�����ŵ����
     */
    private function _porcessNoBrace()
    {
        $pos1 = strpos($this->_str, "&");
        $pos2 = strpos($this->_str, "|");

        //û��λ������ˣ�����Ϊ��С��Ԫ
        if ($pos1 === false && $pos2 === false)
        {
            $this->_type = "cell";
        }
        else
        {
            //�������ͣ��ָ�Ϊ��С��Ԫ
            if ($pos1 !== false)
            {
                $this->_type = "and";
                $delemiter = "&";
            }
            else
            {
                $this->_type = "or";
                $delemiter = "|";
            }
            $subStrs = explode($delemiter, $this->_str);
            foreach ($subStrs as $subStr)
            {
                $this->_subConditions[] = MCore_Cli_Condition::parse($subStr);
            }
        }
    }

    /**
     * �������ţ������Էָ�����
     */
    private function _splitBracePair()
    {
        //1.    ���ҳɶ����ŵ�λ�ã�������Ų����ڣ�������򵥵�Ԫ
        //2.    ��ͼ�������������Ŷ�
        $len = 0;
        $pos1 = 0;
        $pos2 = 0;
        while(1)
        {
            $str = $this->_str;
            $len = strlen($str);
            $pos1 = strpos($str,"(");
            $pos2 = strrpos($str,")");

            //����������
            if ($pos1 === false || $pos2 === false)
            {
                return false;
            }

            //Ѱ�������˵������ţ��ɶԵ����ŵ�λ��
            $count1 = 0;
            $count2 = 0;
            for ($i = $pos1; $i < $len; $i++)
            {
                $char = $str[$i];
                $char == "(" && $count1 ++;
                $char == ")" && $count2 ++;
                if($count1 !=0 && $count1 == $count2)
                {
                    $pos2 = $i;
                    break;
                }
            }

            //���������ˣ�������������һ�ֲ���
            if ($pos1 == 0 && $pos2 == $len -1)
            {
                $this->_str = substr($str, 1, -1);
            }
            else
            {
                break;
            }
        }

        //��������
        if ($pos1 == 0)
        {
            $kindStr = substr($str, $pos2 + 1, 1);
        }
        else
        {
            $kindStr = substr($str, $pos1 -1 ,1);
        }
        $this->_type = ($kindStr == "|") ? "or" : "and";

        //�ָ�����˲������ź��߼�������ŵ�����
        if($pos1 == 0)
        {
            $pre = "";
        }
        else
        {
            $firstLen = $pos1 - 1;   //��ȥλ�������������
            $pre = substr($str, 0, $firstLen);
        }

        $minPartLen = ($pos2 + 1) - $pos1;
        $mid = substr($str, $pos1, $minPartLen);
        $mid = substr($mid, 1, -1);

        $lastLen = $len - ($pos2 + 1);    //��󲿷ֵĳ���
        if ($lastLen == 0)
        {
            $last = '';
        }
        else
        {
            $lastStart = $pos2 + 1 + 1;  //��ȥλ�������������
            $last = substr($str, $pos2 + 1 + 1);
        }

        $pre && $this->_subConditions[] = MCore_Cli_Condition::parse($pre);
        $mid && $this->_subConditions[] = MCore_Cli_Condition::parse($mid);
        $last && $this->_subConditions[] = MCore_Cli_Condition::parse($last);

        return true;
    }

    public function isOk()
    {
        if ($this->_type == "or")
        {
            foreach ($this->_subConditions as $subCondition)
            {
                if($subCondition->isOk())
                {
                    return true;
                }
            }
            return false;
        }
        else
        {
            foreach ($this->_subConditions as $subCondition)
            {
                if ($subCondition->isOk())
                {
                    return false;
                }
            }
            return true;
        }
    }

    public function getType()
    {
        return $this->_type;
    }

    public function getContent()
    {
        return $this->_str;
    }

    public function isCell()
    {
        return count($this->_subConditions) == 0;
    }

    public function getSubConditions()
    {
        return $this->_subConditions;
    }
}
?>
