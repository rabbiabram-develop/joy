<?php

/* (C) 2009 Netology Joy Web Framework, All rights reserved.
 *
 * Author(s):
 *   Hasan Ozgan (meddah@netology.org)
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import("joy.web.View");
import("joy.web.ui.renders.IRender");

class joy_web_ui_renders_Json extends joy_web_View implements joy_web_ui_renders_IRender
{
    private $serializer;
    private $page;

    protected function Init()
    {
        parent::Init();
        $this->setContentType("application/javascript");

        $loader = new joy_vendor_Loader("misc");
        $loader->Import("json/FastJSON.class.php5");
    }

    public function Fetch()
    {
        return FastJSON::encode($this->getData());
    }

    public function Display()
    {
        echo $this->Fetch();
    }
}

?>
