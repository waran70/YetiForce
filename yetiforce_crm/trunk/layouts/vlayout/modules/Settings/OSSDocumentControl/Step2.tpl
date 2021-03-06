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
<form name="condition" action="index.php" method="post" id="workflow_step2" class="form-horizontal" >
    <input type="hidden" name="module" value="{$MODULE_NAME}" />
    <input type="hidden" name="action" value="{if $TPL_ID}UpdateTpl{else}SaveTpl{/if}" />
    <input type="hidden" name="base_module" value="{$BASE_MODULE}" />
    <input type="hidden" name="summary" value="{$SUMMARY}" />
    <input type="hidden" name="parent" value="Settings" />
    
    {if $TPL_ID}
        <input type="hidden" name="tpl_id" value="{$TPL_ID}" />
    {/if}

    <div class="row-fluid padding1per contentsBackground" style="border:1px solid #ccc;box-shadow: 3px 3px 5px rgba(0, 0, 0, 0.5);">
        <div id="advanceFilterContainer" class="row-fluid padding1per contentsBackground span7">

            <div class="allConditionContainer conditionGroup contentsBackground well">
                <table style="width: 70%;">
                    <tr>
                        <td class="span6">
                            <label for="folder">{vtranslate('FOLDER_LIST', $MODULE_NAME)}:&nbsp;</label>
                        </td>
                        <td>
                            <select id="folder" class="chzn-select" name="doc_folder">
                                {foreach from=$FOLDER_LIST item=item key=key}
                                    <option value="{$item->getId()}" {if $BASE_INFO['doc_folder'] eq $item->getId()} selected {/if}
                                    >{$item->getName()}</option>
                                {/foreach}
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="span6"> {vtranslate('DOC_NAME', $MODULE_NAME)}<span class="redColor">*</span> </td>
                        <td>
                            <input type="text" name="doc_name" maxlength="50" value="{$BASE_INFO['doc_name']}" />
                        </td>
                    </tr>
                    <tr>
                        <td class="span6"> {vtranslate('DOC_ORDER', $MODULE_NAME)}<span class="redColor">*</span> </td>
                        <td>
                            <input type="text" name="doc_order" maxlength="50" value="{if $BASE_INFO['doc_order']}{$BASE_INFO['doc_order']}{else}1{/if}" />
                        </td>
                    </tr>
                    <tr>
                        <td class="span6">{vtranslate('DOC_REQUIRED', $MODULE_NAME)} </td>
                        <td><input type="checkbox" name="doc_request" value="1" {if $BASE_INFO['doc_request'] eq '1'} checked {/if}></td>
                    </tr>
                </table>
            </div>

            <h5 class="padding-bottom1per"><strong>{vtranslate('LBL_CHOOSE_FILTER_CONDITIONS',$MODULE)}</strong></h5>

            <div class="allConditionContainer conditionGroup contentsBackground well">

                <div class="header"><span><strong>{vtranslate('LBL_CONDITION_ALL', $MODULE_NAME)}</strong></span> - <span>{vtranslate('LBL_CONDITION_ALL_DSC', $MODULE_NAME)}</span></div>

                <div id="condition_all">
                    {if $TPL_ID}
{*                        <pre>*}
                        {foreach from=$REQUIRED_CONDITIONS key=cnd_key item=cnd_item name=field_select}
                            <div class="row-fluid conditionRow marginBottom10px" id="cnd_num_{$smarty.foreach.field_select.index}">
                                <span class="span4">
                                    <select data-num="{$smarty.foreach.field_select.index}" class="chzn-select row-fluid field-select field-name-select" data-placeholder="{vtranslate('LBL_SELECT_FIELD',$QUALIFIED_MODULE)}">
                                        {foreach key=FIELD_MODULE_NAME item=FIELD from=$FIELD_LIST}
                                            <optgroup label='{vtranslate($FIELD_MODULE_NAME, $FIELD_MODULE_NAME)}'>
                                                {foreach from=$FIELD key=key item=item}
                                                    <option data-module="{$FIELD_MODULE_NAME}" value="{$item['name']}" {if $cnd_item['fieldname'] eq $item['name']}selected{/if}
                                                            data-uitype="{$item['uitype']}" data-info="{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($item['info']))}"
                                                            >{vtranslate($item['label'], $BASE_MODULE)}</option>
                                                {/foreach}
                                            </optgroup>
                                        {/foreach}
                                    </select>
                                </span>
                                <span class="span3">
                                    <select data-num="{$smarty.foreach.field_select.index}" class="chzn-select row-fluid" name="comparator">
                                        {assign var=CONDITION_LIST value=Settings_OSSDocumentControl_Module_Model::getConditionByType($cnd_item['field_type'])}
                                        {foreach from=$CONDITION_LIST item=item key=key}
                                            <option value="{$item}" {if $cnd_item['comparator'] eq $item}selected{/if}>{$item}</option>
                                        {/foreach}
                                    </select>
                                </span>
                                <span class="span4 fieldUiHolder">
{*                                    {var_dump($cnd_item)}*}
                                    {if $cnd_item['field_type'] eq 'picklist'}
                                        <select name="val" data-value=value" class="row-fluid select2">
                                            {foreach from=$cnd_item['info']['picklistvalues'] key=pick_key item=pick_item}
                                                <option value="{$pick_key}" {if $cnd_item['val'] eq $pick_key}selected{/if}>{$pick_item}</option>
                                            {/foreach}
                                        </select>
                                    {else if $cnd_item['field_type'] eq 'multipicklist'}
                                        <select multiple="multiple" name="val" data-value="value" class="row-fluid select2">
                                            {foreach from=$cnd_item['info']['picklistvalues'] key=pick_key item=pick_item}
                                                <option value="{$pick_key}"  {if in_array($pick_key, $cnd_item['val'])} selected {/if}>{$pick_item}</option>
                                            {/foreach}
                                        </select>
                                    {else if $cnd_item['field_type'] eq 'time'}
                                        <div class="input-append time"><input type="text" data-format="24" value="{$cnd_item['val']}" class="timepicker-default input-small ui-timepicker-input" name="val" autocomplete="off"><span class="add-on cursorPointer"><i class="icon-time"></i></span></div>
                                    {else if $cnd_item['field_type'] eq 'date'}
                                        {if $cnd_item['comparator'] == 'between'}
                                            <div class="date"><input class="dateField bw row-fluid" data-calendar-type="range" name="val" data-date-format="yyyy-mm-dd" type="text" readonly="true" value="{$cnd_item['val']|escape}" data-value="value"></div>
                                            {else if in_array($cnd_item['comparator'], array("less than days ago", "more than days ago", "in less than", "in more than", "days ago", "days later"))}
                                            <input name="val" data-value="value" class="row-fluid" type="text" value="{$cnd_item['val']|escape}" />
                                        {else}
                                            <div class="input-append row-fluid"><input class="span9 dateField dateFieldNormal" value="{$cnd_item['val']|escape}" name="val" data-date-format="yyyy-mm-dd"><span class="add-on"><i class="icon-calendar"></i></span></div>
                                        {/if}
                                    {else}
                                    <input name="val" data-value="value" class="row-fluid" type="text" value="{$cnd_item['val']|escape}" />
                                    {/if}
                                </span>
                                <span class="span1">
                                    <i class="deleteCondition icon-trash alignMiddle" title="{vtranslate('LBL_DELETE', $QUALIFIED_MODULE)}" onclick="jQuery(this).parents('div#cnd_num_{$smarty.foreach.field_select.index}').remove()"></i>
                                </span>
                            </div>
                        {/foreach}
                    {/if}
                </div>

                <div class="addCondition"><button class="add_condition btn" data-type="condition_all" type="button"><strong>{vtranslate('ADD_CONDITIONS', $MODULE_NAME)}</strong></button></div>
            </div>

            <div class="allConditionContainer conditionGroup contentsBackground well">

                <div class="header"><span><strong>{vtranslate('LBL_CONDITION_OPTION', $MODULE_NAME)}</strong></span> - <span>{vtranslate('LBL_CONDITION_OPTION_DSC', $MODULE_NAME)}</span></div>

                <div id="condition_option">
                    {if $TPL_ID}
                        {foreach from=$OPTIONAL_CONDITIONS key=cnd_key item=cnd_item name=field_select}
                            <div class="row-fluid conditionRow marginBottom10px" id="cnd_num_{$smarty.foreach.field_select.index}">
                                <span class="span4">
                                    <select data-num="{$smarty.foreach.field_select.index}" class="chzn-select row-fluid field-select field-name-select" data-placeholder="{vtranslate('LBL_SELECT_FIELD',$QUALIFIED_MODULE)}">
                                        {foreach key=FIELD_MODULE_NAME item=FIELD from=$FIELD_LIST}
                                            <optgroup label='{vtranslate($FIELD_MODULE_NAME, $FIELD_MODULE_NAME)}'>
                                                {foreach from=$FIELD key=key item=item}
                                                    <option data-module="{$FIELD_MODULE_NAME}" value="{$item['name']}" {if $cnd_item['fieldname'] eq $item['name']}selected{/if}
                                                            data-uitype="{$item['uitype']}" data-info="{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($item['info']))}"
                                                            >{vtranslate($item['label'], $BASE_MODULE)}</option>
                                                {/foreach}
                                            </optgroup>
                                        {/foreach}
                                    </select>
                                </span>
                                <span class="span3">
                                    <select data-num="{$smarty.foreach.field_select.index}" class="chzn-select row-fluid" name="comparator">
                                        {assign var=CONDITION_LIST value=Settings_OSSDocumentControl_Module_Model::getConditionByType($cnd_item['field_type'])}
                                        {foreach from=$CONDITION_LIST item=item key=key}
                                            <option value="{$item}" {if $cnd_item['comparator'] eq $item}selected{/if}>{$item}</option>
                                        {/foreach}
                                    </select>
                                </span>
                                <span class="span4 fieldUiHolder">
{*                                    {var_dump($cnd_item)}*}
                                    {if $cnd_item['field_type'] eq 'picklist'}
                                        <select name="val" data-value=value" class="row-fluid select2">
                                            {foreach from=$cnd_item['info']['picklistvalues'] key=pick_key item=pick_item}
                                                <option value="{$pick_key}" {if $cnd_item['val'] eq $pick_key}selected{/if}>{$pick_item}</option>
                                            {/foreach}
                                        </select>
                                    {else if $cnd_item['field_type'] eq 'multipicklist'}
                                        <select multiple="multiple" name="val" data-value="value" class="row-fluid select2">
                                            {foreach from=$cnd_item['info']['picklistvalues'] key=pick_key item=pick_item}
                                                <option value="{$pick_key}"  {if in_array($pick_key, $cnd_item['val'])} selected {/if}>{$pick_item}</option>
                                            {/foreach}
                                        </select>
                                    {else if $cnd_item['field_type'] eq 'time'}
                                        <div class="input-append time"><input type="text" data-format="24" value="{$cnd_item['val']}" class="timepicker-default input-small ui-timepicker-input" name="val" autocomplete="off"><span class="add-on cursorPointer"><i class="icon-time"></i></span></div>
                                    {else if $cnd_item['field_type'] eq 'date'}
                                        {if $cnd_item['comparator'] == 'between'}
                                            <div class="date"><input class="dateField bw row-fluid" data-calendar-type="range" name="val" data-date-format="yyyy-mm-dd" type="text" readonly="true" value="{$cnd_item['val']|escape}" data-value="value"></div>
                                            {else if in_array($cnd_item['comparator'], array("less than days ago", "more than days ago", "in less than", "in more than", "days ago", "days later"))}
                                            <input name="val" data-value="value" class="row-fluid" type="text" value="{$cnd_item['val']|escape}" />
                                        {else}
                                            <div class="input-append row-fluid"><input class="span9 dateField dateFieldNormal" value="{$cnd_item['val']|escape}" name="val" data-date-format="yyyy-mm-dd"><span class="add-on"><i class="icon-calendar"></i></span></div>
                                        {/if}
                                    {else}
                                    <input name="val" data-value="value" class="row-fluid" type="text" value="{$cnd_item['val']|escape}" />
                                    {/if}
                                </span>
                                <span class="span1">
                                    <i class="deleteCondition icon-trash alignMiddle" title="{vtranslate('LBL_DELETE', $QUALIFIED_MODULE)}" onclick="jQuery(this).parents('div#cnd_num_{$smarty.foreach.field_select.index}').remove()"></i>
                                </span>
                            </div>
                        {/foreach}
                    {/if}
                </div>

                <div class="addCondition"><button class="add_condition btn" data-type="condition_option" type="button"><strong>{vtranslate('ADD_CONDITIONS', $MODULE_NAME)}</strong></button></div>
            </div>

        </div>
    </div>
    <br>
    <div class="pull-right">
        <button class="btn btn-danger backStep" type="button" onclick="javascript:window.history.back();"><strong>{vtranslate('BACK', $MODULE_NAME)}</strong></button>&nbsp;&nbsp;
        <button class="btn btn-success" type="submit"><strong>{vtranslate('NEXT', $MODULE_NAME)}</strong></button>
        <a class="cancelLink" href="index.php?module=OSSDocumentControl&parent=Settings&view=Index">{vtranslate('CANCEL', $MODULE_NAME)}</a>
    </div>
    <div class="clearfix"></div>

    <input type="hidden" name="condition_all_json" value="" />
    <input type="hidden" name="condition_option_json" value="" />
</form>
    
        <div id="condition_list" style="display: none;">{ZEND_JSON::encode($CONDITION_BY_TYPE)}</div>
