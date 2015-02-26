<?php
class MApis_SliderBanner extends MApps_AppBase_BaseApiApp
{
    protected function main()
    {
        $pic_list = array(
            'http://img5.duitang.com/uploads/blog/201406/30/20140630150534_EWUVY.thumb.jpeg',
            'http://img5.duitang.com/uploads/item/201406/25/20140625121626_ZmT5n.thumb.jpeg',
            'http://img4.duitang.com/uploads/blog/201307/31/20130731231806_4yGxV.thumb.jpeg',
            'http://img5.duitang.com/uploads/item/201406/28/20140628122218_fLQyP.thumb.jpeg',
            'http://img5.duitang.com/uploads/blog/201406/26/20140626131831_MrdKP.thumb.jpeg',
            'http://img5.duitang.com/uploads/blog/201406/16/20140616165201_nuKWj.thumb.jpeg',
            'http://img5.duitang.com/uploads/item/201406/25/20140625140308_KP4rn.thumb.jpeg',
            'http://img5.duitang.com/uploads/item/201406/25/20140625121604_2auuA.thumb.jpeg',
            'http://img4.duitang.com/uploads/item/201406/25/20140625131625_LmmLZ.thumb.jpeg',
            'http://img5.duitang.com/uploads/item/201406/25/20140625132851_mPmKY.thumb.jpeg',
            'http://img5.duitang.com/uploads/item/201406/25/20140625133312_ZtmW4.thumb.jpeg',
            'http://img5.duitang.com/uploads/item/201406/25/20140625164858_AuafS.thumb.jpeg',
        );

        $this->setData($pic_list);
    }
}
