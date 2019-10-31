<?php

namespace point\web;

class Utility_SimpleXMLExtended extends \SimpleXMLElement
{
    /**
     * CDATA Text fix
     *
     * @param $cdata_text
     */
    public function addCData($cdata_text) {
        $node = dom_import_simplexml($this);
        $no   = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($cdata_text));
    }
}
