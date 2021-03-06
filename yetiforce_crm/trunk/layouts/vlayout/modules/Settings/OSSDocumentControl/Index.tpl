{*<!--
/*+***********************************************************************************************************************************
 * The contents of this file are subject to the YetiForce Public License Version 1.1 (the "License"); you may not use this file except
 * in compliance with the License.
 * Software distributed under the License is distributed on an "AS IS" basis, WITHOUT WARRANTY OF ANY KIND, either express or implied.
 * See the License for the specific language governing rights and limitations under the License.
 * The Original Code is YetiForce.
 * The Initial Developer of the Original Code is YetiForce. Portions created by YetiForce are Copyright (C) www.yetiforce.com. 
 * All Rights Reserved.
 *************************************************************************************************************************************/
-->*}
<div class="container-fluid" id="menuEditorContainer">
    <div class="widget_header row-fluid">
        <div class="span8"><h3>{vtranslate($MODULE_NAME, $MODULE_NAME)}</h3></div>
    </div>
    <hr>
    <div id="my-tab-content" class="tab-content" style="margin: 0 20px;" >
        <div class='editViewContainer' id="tpl" style="min-height:300px">
            <div class="row-fluid">
                <span class="span4 btn-toolbar">
                    <a class="btn addButton" href="index.php?module={$MODULE_NAME}&parent=Settings&view=Edit">
                        <strong>{vtranslate('LBL_NEW_TPL', $MODULE_NAME)}</strong>
                    </a>
                </span>
                <span class="span4 btn-toolbar" >
                    <select class="chzn-select" id="moduleFilter" >
                        <option value="">{vtranslate('LBL_ALL', $MODULE_NAME)}</option>
                        {foreach item=item key=key from=$SUPPORTED_MODULE_MODELS}
                            <option value="{$item}">{vtranslate($item, $item)}</option>
                        {/foreach}
                    </select>
                </span>
            </div>
            <br>
            <div class="row-fluid" id="list_doc">
                <table class="table table-bordered table-condensed listViewEntriesTable">
                    <thead>
                        <tr class="listViewHeaders" >
                            <th width="30%">{vtranslate('LBL_MODULE_NAME',$MODULE_NAME)}</th>
                            <th>{vtranslate('LBL_SUMMARY',$MODULE_NAME)}</th>
                            <th colspan="2"></th>
                        </tr>
                    </thead>
                    {if !empty($DOC_TPL_LIST)}

                    <tbody>
                        {foreach from=$DOC_TPL_LIST item=item key=key}
                        <tr class="listViewEntries" data-id="{$item.id}">
                                <td onclick="location.href = jQuery(this).data('url')" data-url="index.php?module={$MODULE_NAME}&parent=Settings&view=Edit&tpl_id={$item.id}">{vtranslate($item.module, $item.module)}</td>
                                <td onclick="location.href = jQuery(this).data('url')" data-url="index.php?module={$MODULE_NAME}&parent=Settings&view=Edit&tpl_id={$item.id}">{$item.summary}</td>
                                <td><a class="pull-right edit_tpl" href="index.php?module={$MODULE_NAME}&parent=Settings&view=Edit&tpl_id={$item.id}"><!--<i title="{vtranslate('LBL_EDIT')}" class="icon-pencil alignMiddle"></i>--></a>
                                    <a href='index.php?module={$MODULE_NAME}&parent=Settings&action=DeleteTemplate&tpl_id={$item.id}' class="pull-right marginRight10px">
                                        <i type="{vtranslate('REMOVE_TPL', $MODULE_NAME)}" class="icon-trash alignMiddle"></i></a>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
                {else}
                    <table class="emptyRecordsDiv">
                        <tbody>
                            <tr>
                                <td>
                                    {vtranslate('LBL_NO_PROJECT_TPL_ADDED',$MODULE_NAME)}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                {/if}
            </div>
        </div>
        <div class='editViewContainer tab-pane' id="help">
            <form class="form-horizontal recordEditView" id="EditView" name="EditView" method="" action="">
                <input type="hidden" name="mode" value="ticket" />            
                <p> </p>            
                <table class="table table-bordered blockContainer showInlineTable">
            <tr>
                <th class="blockHeader" colspan="4">{vtranslate('LBL_HELP', $MODULE_NAME)}</th>
            </tr>
            <tr>
				<td class="fieldLabel">
                    <label class="muted pull-right marginRight10px"> {vtranslate('Information', $MODULE_NAME)}</label>
                </td>
                 <td class="fieldValue" >
				<span class="span10">
                <a href="{vtranslate('LBL_UrlLink2', $MODULE_NAME)}" target="_blank">{vtranslate('LBL_UrlLink2', $MODULE_NAME)}
				</td>
            </tr>
            <tr>
                <td class="fieldLabel">
                    <label class="muted pull-right marginRight10px"> {vtranslate('LBL_Helpforthemodule', $MODULE_NAME)}</label>
                </td>
                <td class="fieldValue" >
                    <div class="row-fluid"><span class="span10">
                        <a href="mailto:{vtranslate('LBL_UrlHelp', $MODULE_NAME)}" target="_blank">{vtranslate('LBL_UrlHelp', $MODULE_NAME)},&nbsp </a>
						<a href="mailto:{vtranslate('LBL_UrlHelp2', $MODULE_NAME)}" target="_blank">{vtranslate('LBL_UrlHelp2', $MODULE_NAME)}</a>
						</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="fieldLabel">
                    <label class="muted pull-right marginRight10px"> {vtranslate('LBL_License', $MODULE_NAME)}</label>
                </td>
                <td class="fieldValue" >
                    <div class="row-fluid"><span class="span10">
                        {*
                        // Removal of this link violates the principles of License
                        // Usunięcie tego linku narusza zasady licencji *}
                        <a href="{vtranslate('LBL_UrlLicense', $MODULE_NAME)}" target="_blank">{vtranslate('LBL_UrlLicense', $MODULE_NAME)}</a></span>
                    </div>
                </td>
            </tr>
			<tr>
                <td class="fieldLabel">
                    <label class="muted pull-right marginRight10px"> {vtranslate('LBL_Company', $MODULE_NAME)}</label>
                </td>
                <td class="fieldValue" >
                    <div class="row-fluid"><span class="span10">
                        <a href="{vtranslate('LBL_UrlCompany', $MODULE_NAME)}" target="_blank">{vtranslate('LBL_UrlCompany', $MODULE_NAME)}</a></span>
                    </div>
                </td>
            </tr>
        </table>
            </form>
        </div>
        {* delete module form *}
        <div class='editViewContainer tab-pane' id="uninstall">
            <form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php?module=OSSDocumentControl&view=Uninstall&parent=Settings&block={$smarty.get.block}&fieldid={$smarty.get.fieldid}">
                <input type="hidden" name="uninstall" value="uninstall" />
                <input type="hidden" name="status" value="1" />
                <p> </p>            
                <table class="table table-bordered blockContainer showInlineTable">
                    <tr>
                        <th class="blockHeader" colspan="4">{vtranslate('Delete_panel', $MODULE_NAME)}{$MODULE_NAME}</th>
                    </tr>
                    <tr>
                        <td class="fieldLabel" colspan="4">
                            <span class="pull-right">
                                <button class="btn btn-danger btn-large" name="uninstall" type="submit"  data-toggle="modal" data-target="#myModal"><strong>{vtranslate('Uninstall', $MODULE_NAME)}</strong></button>
                                <a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('Cancel', $MODULE_NAME)}</a> 
                            </span>
                        </td>
                    </tr>            
                </table>            
            </form>
        </div>
    </div>
</div>
{* modal promtp for uninstall *}
<div id="myModal" class="modal hide fade" style="z-index: 9999999;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>{vtranslate('MSG_DEL_WARN1', $MODULE_NAME)}</h3>
    </div>
    <div class="modal-body">
        <p>{vtranslate('MSG_DEL_WARN2', $MODULE_NAME)}</p>
        <p><input id="status" name="status" type="checkbox" value="1" required="required" /> {vtranslate('Uninstall OSSDocumentControl module', $MODULE_NAME)}</p>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal">{vtranslate('No', $MODULE_NAME)}</a>
        <a class="btn btn-danger okay-button" href="#" id="confirm_unistall" type="button" name="uninstall" disabled="disabled">{vtranslate('Yes', $MODULE_NAME)}</a>
    </div>          
</div>

<script type="text/javascript" src="layouts/vlayout/modules/Settings/{$MODULE_NAME}/resources/Edit.js"></script>