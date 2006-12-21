<?php
/**
 * Copyright � STMicroelectronics, 2006. All Rights Reserved.
 * 
 * Originally written by Manuel VACELET, 2006.
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with CodeX; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * 
 * $Id$
 */

require_once('common/dao/include/DataAccessObject.class.php');

class Docman_MetadataValueDao extends DataAccessObject {

    function Docman_MetadataValueDao(&$da) {
        DataAccessObject::DataAccessObject($da);
    }
    
    function searchById($fieldId, $itemId) {
        $sql = sprintf('SELECT *'.
                       ' FROM plugin_docman_metadata_value'.
                       ' WHERE field_id = %d'.
                       ' AND item_id = %d',
                       $fieldId,
                       $itemId);
        return $this->retrieve($sql);
    }

    function create($itemId, $fieldId, $type, $value) {
        $fields = array('field_id', 'item_id');
        $types  = array('%d', '%d');

        $val     = null;
        $badtype = false;
        $field   = 'value';
        switch($type) {
        case PLUGIN_DOCMAN_METADATA_TYPE_TEXT:
            $field .= 'Text';
            $dtype   = '%s';
            $val    = $this->da->quoteSmart($value);
            break;

        case PLUGIN_DOCMAN_METADATA_TYPE_STRING:
            $field .= 'String';
            $dtype   = '%s';
            $val    = $this->da->quoteSmart($value);
            break;

        case PLUGIN_DOCMAN_METADATA_TYPE_DATE:
            $field .= 'Date';
            $dtype   = '%d';
            $val    = $value;
            break;

        case PLUGIN_DOCMAN_METADATA_TYPE_LIST:
            $field .= 'Int';
            $dtype   = '%d';
            $val    = $value;
            break;

        default:
            $badtype = true;
        }

        if(!$badtype) {
            $fields[] = $field;
            $types[]  = $dtype;
            
            $sql = sprintf('INSERT INTO plugin_docman_metadata_value'.
                           ' ('.implode(',', $fields).')'.
                           ' VALUES ('.implode(',', $types).')',
                           $fieldId,
                           $itemId,
                           $val);

            return $this->_createAndReturnId($sql);
        }
        else {
            return false;
        }
    }

    function _createAndReturnId($sql) {
        $inserted = $this->update($sql);
        if ($inserted) {
            $dar = $this->retrieve("SELECT LAST_INSERT_ID() AS id");
            if ($row = $dar->getRow()) {
                $inserted = $row['id'];
            } else {
                $inserted = $dar->isError();
            }
        }
        return $inserted;
    }

    function updateValue($itemId, $fieldId, $type, $value) {
        $badtype = false;
        $field   = 'value';
        switch($type) {
        case PLUGIN_DOCMAN_METADATA_TYPE_TEXT:
            $field .= 'Text';
            $dtype   = '%s';
            $val    = $this->da->quoteSmart($value);
            break;

        case PLUGIN_DOCMAN_METADATA_TYPE_STRING:
            $field .= 'String';
            $dtype   = '%s';
            $val    = $this->da->quoteSmart($value);
            break;

        case PLUGIN_DOCMAN_METADATA_TYPE_DATE:
            $field .= 'Date';
            $dtype   = '%d';
            $val    = $value;
            break;

        case PLUGIN_DOCMAN_METADATA_TYPE_LIST:
            $field .= 'Int';
            $dtype   = '%d';
            $val    = $value;
            break;

        default:
            $badtype = true;
        }

        if(!$badtype) {
            $sql = sprintf('UPDATE plugin_docman_metadata_value'.
                           ' SET '.$field.' = '.$dtype.
                           ' WHERE field_id = %d'.
                           ' AND item_id = %d',
                           $val,
                           $fieldId,
                           $itemId);

            return $this->update($sql);
        }
        else {
            return false;
        }
    }

    function exist($itemId, $fieldId) {
        $sql = sprintf('SELECT count(*) AS nb'.
                       ' FROM plugin_docman_metadata_value'.
                       ' WHERE item_id = %d'.
                       ' AND field_id = %d',
                       $itemId,
                       $fieldId);
        return $this->retrieve($sql);
    }

    function updateToListOfValueElementDefault($fieldId, $previousValue, $newValue) {
        $sql = sprintf('UPDATE plugin_docman_metadata_value'.
                       ' SET valueInt = %d'.
                       ' WHERE field_id = %d'.
                       '  AND valueInt = %d',
                       $newValue,
                       $fieldId,
                       $previousValue);
        return $this->update($sql);
    }
}

?>
