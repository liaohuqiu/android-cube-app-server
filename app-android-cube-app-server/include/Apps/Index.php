<?php
class MApps_Index extends MApps_AppBase_BasePageApp
{
    protected function getTitle()
    {
        return 'CUBE APP API';
    }

    protected function main()
    {
        $test_token = '1121052536_1411963213_51e14c490d87190fcacebfa9ef77fa5b';
        if (ENV_TAG == 'linode')
        {
            $test_token = '1121052536_1411970068_5bb6410aca5749a37ba4b5a27f9d62ec';
        }

        $base_params = array(
            'token' => array('des' => '', 'demo_vaule' => $test_token),
            'v' => array('des' => '版本号', 'demo_vaule' => '1.0.1'),
            'c' => array('des' => '客户端: android/ios', 'demo_vaule' => 'android'),
            'cv' => array('des' => '客户端系统版本: android apilevel / ios 5/6/7..', 'demo_vaule' => 'android'),
        );

        $map = array(
            'api/image-list' => array(
                'des' => '图片列表',
                'params' => array(
                    'start' => array('des' => '开始位置', 'demo_vaule' => '0'),
                    'num' => array('des' => '所取条数', 'demo_vaule' => '20'),
                ),
            ),
            'api/get-image' => array(
                'des' => 'fetch an image',
                'params' => array(
                ),
            ),
            'api/slider-banner' => array(
                'des' => 'fetch an image',
                'params' => array(
                ),
            ),
        );

        $host = $_SERVER['HTTP_HOST'];
        foreach ($map as $url => $item)
        {
            $params = $item['params'];
            if ($url == 'api/init')
            {
            }
            else
            {
                $item['params'] = $params;
                $params = array_merge($base_params, $params);
            }
            $data = MCore_Tool_Array::getFields($params, 'demo_vaule', true);
            $demo_url = MCore_Tool_Http::buildGetUrl($data, $url);
            $item['demo_url'] = 'http://' . $host . '/'. $demo_url;
            $map[$url] = $item;
        }
        $this->getView()->setPageData('list', $map);
    }

    protected function outputBody()
    {
        $this->getView()->display('api-list.html');
    }
}
