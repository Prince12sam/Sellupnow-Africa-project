<?php


namespace plugins\PageBuilder\Traits;


trait AssetsPathOverride
{
    /**
     * this method will override the default assets path of page builder
     * */

    public function setAssetsFilePath(){
        return 'assets/backend/page-builder';
    }
}

