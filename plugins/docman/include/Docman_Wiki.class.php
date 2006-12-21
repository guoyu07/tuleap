<?php
/**
 * Copyright (c) STMicroelectronics, 2006. All Rights Reserved.
 *
 * Originally written by Nicolas Terray, 2006
 *
 * This file is a part of CodeX.
 *
 * CodeX is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * CodeX is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CodeX; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * $Id$
 */
require_once('Docman_Document.class.php');

/**
 * Wiki is a transport object (aka container) used to share data between
 * Model/Controler and View layer of the application
 */
class Docman_Wiki extends Docman_Document {
    
    function Docman_Wiki($data = null) {
        parent::Docman_Document($data);
    }
    
    var $pagename;
    function getPagename() { 
        return $this->pagename; 
    }
    function setPagename($pagename) { 
        $this->pagename = $pagename;
    }
    
    function initFromRow($row) {
        parent::initFromRow($row);
        $this->setPagename($row['wiki_page']);
    }
    function toRow() {
        $row = parent::toRow();
        $row['wiki_page'] = $this->getPagename();
        $row['item_type'] = PLUGIN_DOCMAN_ITEM_TYPE_WIKI;
        return $row;
    }

    function accept(&$visitor, $params = array()) {
        return $visitor->visitWiki($this, $params);
    }
}

?>