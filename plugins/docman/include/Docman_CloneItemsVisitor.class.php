<?php
/**
 * Copyright (c) STMicroelectronics, 2006. All Rights Reserved.
 *
 * Originally written by Manuel Vacelet, 2006
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

require_once('Docman_ItemFactory.class.php');
require_once('Docman_PermissionsManager.class.php');

class Docman_CloneItemsVisitor {
    var $dstGroupId;

    function Docman_CloneItemsVisitor($dstGroupId) {
        $this->dstGroupId = $dstGroupId;
    }

    function visitFolder($item, $params = array()) {
        // Clone folder
        $newItemId = $this->_cloneItem($item, $params);
        if($newItemId > 0) {
            $params['parentId'] = $newItemId;
            
            // Recurse
            $items =& $item->getAllItems();
            if($items) {
                $nb = $items->size();
                if($nb) {
                    $iter =& $items->iterator();
                    $iter->rewind();
                    while($iter->valid()) {
                        $child =& $iter->current();
                        $child->accept($this, $params);
                        $iter->next();
                    }
                }
            }
        }
    }

    function visitDocument(&$item, $params = array()) {
        die('never happen');
    }

    function visitWiki(&$item, $params = array()) {
        $this->_cloneItem($item, $params);
    }

    function visitLink(&$item, $params = array()) {
        $this->_cloneItem($item, $params);
    }

    function visitFile(&$item, $params = array()) {
        $this->_cloneFile($item, $params);
    }

    function visitEmbeddedFile(&$item, $params = array()) {
        $this->_cloneFile($item, $params);
    }

    function _cloneFile($item, $params) {
        $newItemId = $this->_cloneItem($item, $params);
        if($newItemId > 0) {
            // Clone physical file of the last version in the template item
            $srcVersion = $item->getCurrentVersion();
            $srcPath = $srcVersion->getPath();
            $dstName = basename($srcPath);
            //print $srcPath.'-'.$dstName."-<br>";
            $fs = new Docman_FileStorage($params['data_root']);
            $dstPath = $fs->clone($srcPath,
                                      $dstName, $this->dstGroupId, $newItemId, 0);

            // Register a new file
            $versionFactory = new Docman_VersionFactory();
            $user = $params['user'];
            $label = $GLOBALS['Language']->getText('plugin_docman', 'clone_file_label');
            $project = group_get_object($item->getGroupId());
            $changelog = $GLOBALS['Language']->getText('plugin_docman', 'clone_file_changelog', array($item->getTitle(),
                                                                                                      $project->getPublicName(),
                                                                                                      $srcVersion->getNumber()));
            $newVersionArray = array('item_id'   => $newItemId,
                                     'number'    => 0,
                                     'user_id'   => $user->getId(),
                                     'label'     => $label,
                                     'changelog' => $changelog,
                                     'filename'  => $srcVersion->getFilename(),
                                     'filesize'  => $srcVersion->getFilesize(),
                                     'filetype'  => $srcVersion->getFiletype(),
                                     'path'      => $dstPath);
            
            $versionId = $versionFactory->create($newVersionArray);
            
        }
    }

    function _cloneItem($item, $params) {
        $parentId = $params['parentId'];
        $metadataMapping = $params['metadataMapping'];
        $ugroupsMapping = $params['ugroupsMapping'];

        // Clone Item
        $itemFactory = new Docman_ItemFactory();
        $newItem = $item;
        $newItem->setGroupId($this->dstGroupId);
        $newItem->setParentId($parentId);
        $newItemId = $itemFactory->rawCreate($newItem);
        if($newItemId > 0) {
            // Clone Permissions
            $this->_clonePermissions($item, $newItemId, $ugroupsMapping);

            // Clone Metadata values
            $this->_cloneMetadataValues($item, $newItemId, $metadataMapping);
        }
        return $newItemId;
    }

    function _clonePermissions($item, $newItemId, $ugroupsMapping) {
        $dpm =& Docman_PermissionsManager::instance($item->getGroupId());
        if($ugroupsMapping === false) {
            // ugroups mapping is not available.
            // use default values.
            $dpm->setDefaultItemPermissions($newItemId);
        }
        else {
            $dpm->cloneItemPermissions($item->getId(), $newItemId, $this->dstGroupId);
        }
    }

    function _cloneMetadataValues($item, $newItemId, $metadataMapping) {
        // List for current item all its metadata and
        // * change the itemId
        // * change the fieldId (use mapping between template metadata and
        //   project metadata)
        // * for list of values change the values (use mapping as behind).
        $newMdvFactory = new Docman_MetadataValueFactory($this->dstGroupId);
        $oldMdvFactory = new Docman_MetadataValueFactory($item->getGroupId());
        
        $oldMdFactory = new Docman_MetadataFactory($item->getGroupId());
        $oldMdFactory->appendItemMetadataList($item);
        
        $oldMdIter = $item->getMetadataIterator();
        $oldMdIter->rewind();
        while($oldMdIter->valid()) {
            $oldMd = $oldMdIter->current();
            
            if($oldMdFactory->isRealMetadata($oldMd->getLabel())) {
                $oldMdv = $oldMdvFactory->getMetadataValue($item, $oldMd);

                $newMdv = $oldMdv;
                $newMdv->setItemId($newItemId);
                $newMdv->setFieldId($metadataMapping['md'][$oldMd->getId()]);
                if($oldMd->getType() == PLUGIN_DOCMAN_METADATA_TYPE_LIST) {
                    $ea = array();
                    $eIter = $oldMdv->getValue();
                    $eIter->rewind();
                    while($eIter->valid()) {
                        $e = $eIter->current();
                        
                        $newE = $e;
                        // no maping for value `100` (shared by all lists).
                        if($e->getId() != 100) {
                            $newE->setId($metadataMapping['love'][$e->getId()]);
                        }
                        $ea[] = $newE;
                        
                        $eIter->next();
                    }
                    $newMdv->setValue($ea);
                }
                
                $newMdvFactory->create($newMdv);
            }

            $oldMdIter->next();
        }
    }

}

?>
