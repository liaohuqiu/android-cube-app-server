<?php
class MApis_GetImage extends MApps_AppBase_BaseApiApp
{
    protected function main()
    {
        $pic = 'http://img5.duitang.com/uploads/item/201408/09/20140809210610_iTSJx.thumb.jpeg';

        $now = date('Y-m-d H:i:s T');
        $data['time'] = $now;
        $data['pic'] = $pic;

        $this->setData($data);
    }
}
