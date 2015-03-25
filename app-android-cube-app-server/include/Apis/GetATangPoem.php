<?php
class MApis_GetATangPoem extends MApps_AppBase_BaseApiApp
{
    protected function main()
    {
        $data = $this->getAPoem();
        $this->setData($data);
    }

    protected function getAPoem()
    {
        $data = file_get_contents(ROOT_DIR . '/data/Tang300.txt');
        $list = explode('---', $data);

        $authors = [];
        $poems = [];
        foreach ($list as $item)
        {
            $lines = explode("\n", $item);
            $str = array_pop($lines);
            if ($str)
            {
                $authors[] = substr($str, 3);
            }

            if (empty($lines))
            {
                continue;
            }
            $text = implode("\n", $lines);
            $poems[] = $text;
        }

        $i = rand(0, count($authors) - 1);

        $ret = [];
        $ret['author'] = $authors[$i];
        $ret['content'] = $poems[$i];
        return $ret;
    }
}
