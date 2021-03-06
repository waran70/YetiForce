<?php
/*+***********************************************************************************************************************************
 * The contents of this file are subject to the YetiForce Public License Version 1.1 (the "License"); you may not use this file except
 * in compliance with the License.
 * Software distributed under the License is distributed on an "AS IS" basis, WITHOUT WARRANTY OF ANY KIND, either express or implied.
 * See the License for the specific language governing rights and limitations under the License.
 * The Original Code is YetiForce.
 * The Initial Developer of the Original Code is YetiForce. Portions created by YetiForce are Copyright (C) www.yetiforce.com. 
 * All Rights Reserved.
 *************************************************************************************************************************************/
$permitted_modules = array('OSSInvoiceCost');

$variables_list = array(
    'enable_ordinal_column' => 'Enable column with positions no. :',
    'enable_productname_column' => 'Enable column with product name :',
    'enable_amount_column' => 'Enable column with product`s amount :',
    'enable_listprice_column' => 'Enable column with product`s price :',
    'enable_netprice_column' => 'Enable column with product`s net price :',
    'enable_discount_column' => 'Enable column with product`s discount :',
    'enable_vatpercentage_column' => 'Enable column with product`s vat[%] :',
    'enable_vatamount_column' => 'Enable column with product`s vat amount :',
    'enable_gross_column' => 'Enable column with product`s gross',
    'enable_taxes_inPLN' => 'Enable column with taxes in PLN',
    'enable_summary_shipping' => 'Document summary: show shipping costs :',
    'enable_summary_shippingtax' => 'Document summary: show shipping tax :',
    'enable_summary_correction' => 'Document summary: show correction :',
    'enable_summary_topay' => 'Document summary: show amount to pay :',
    'enable_summary_rebate' => 'Document summary: show rebate :',
    'enable_summary_taxpercentage' => 'Document summary: show tax [%] :',
    'enable_summary_taxamount' => 'Document summary: show tax amount :',
    'displays_a_summary_of_invoices' => 'Show document summary :',
);

/// MARKER CALLING THIS FUNCTION => #special_function#example#end_special_function#
/// function must have the same name as the FILE
function replaceProductTableInvoiceCost($pdftype, $id, $templateid,$content, $tcpdf) {

    /* ------------------- COLUMN CONFIGURATION -------------------

      In this part, you can kofigurować which columns you want to appear
                       and which are not in the table of positions and invoice summary table

                       If the variable responsible for this column has the value TRUE
                       will be displayed and if FALSE it will not be displayed

     */

    // The columns for the table from the list of items on the invoice

    $kolumnyDlaPozycjiFaktur = array();
    // column ordinal
    $enable_ordinal_column = TRUE;
    // Column name of the product
    $enable_productname_column = TRUE;
    // column number
    $enable_amount_column = TRUE;
    // unit price column
    $enable_listprice_column = TRUE;
    // net price column
    $enable_netprice_column = TRUE;
    // discount column
    $enable_discount_column = TRUE;
    // Vat column (%)
    $enable_vatpercentage_column = TRUE;
    // Vat column (currency)
    $enable_vatamount_column = TRUE;
    // Gross column
    $enable_gross_column = TRUE;
    // Taxes in PLN
    $enable_taxes_inPLN = TRUE;


    // Column for a summary in the gray box
    $wyswietlaj = array(
        $enable_ordinal_column,
        $enable_productname_column,
        $enable_amount_column,
        $enable_listprice_column,
        $enable_netprice_column,
        $enable_discount_column,
        $enable_vatpercentage_column,
        $enable_vatamount_column,
        $enable_gross_column,
        $enable_taxes_inPLN
    );
    $kolumnyDlaPodsumowaniaFaktur = array();

    // shipping costs
    $enable_summary_shipping = TRUE;
    // tax on shipping costs				
    $enable_summary_shippingtax = TRUE;
    // correction
    $enable_summary_correction = TRUE;
    // to pay
    $enable_summary_topay = TRUE;
    // total rebate
    $enable_summary_rebate = TRUE;
    // the percentage of tax column
    $enable_summary_taxpercentage = TRUE;
    // tax amount column
    $enable_summary_taxamount = TRUE;


    //----------------------------------------------------

    $adb = PearDatabase::getInstance();
    $current_language = vglobal('current_language');
    $currentModule = vglobal('currentModule');
    require_once( 'include/utils/utils.php' );

    include_once("languages/pl_pl/OSSPdf.php");
    $tab_pl = $languageStrings;

    include_once("languages/en_us/OSSPdf.php");
    $tab_us = $languageStrings;
    
    require_once( 'include/utils/CommonUtils.php' );
    require_once( 'include/fields/CurrencyField.php' );
    require_once( 'modules/' . $pdftype . '/utils.php' );
    $displays_a_summary_of_invoices = TRUE;

    require_once( 'modules/' . $pdftype . '/' . $pdftype . '.php' );

    $focus = new $pdftype();
    $focus->retrieve_entity_info($id, $pdftype);
    
    $currency_id = $focus->column_fields['currency_id'];
    $pobierz = $adb->query("select currency_symbol, currency_code from vtiger_currency_info where id = '$currency_id'", true);
    $symbol_waluty = $adb->query_result($pobierz, 0, "currency_symbol");
    $kod_aktualnej_waluty = $adb->query_result($pobierz, 0, "currency_code");

    //////////////////////////////
    // check if the module is OSSCurrencyUpdate exist

    $czyDrukowacKolumneZPrzeliczonymPodatkiem = FALSE;

    $moduleFilePath = "modules/OSSCurrencyUpdate/OSSCurrencyUpdate.php";
    $moduleFilePathTpl = "Smarty/templates/modules/OSSCurrencyUpdate/index.tpl";
    $checkInDatabaseSql = "SELECT * FROM vtiger_tab WHERE name = 'OSSCurrencyUpdate'";

    $pobierzGluwnaWaluteSql = $adb->query("select currency_code from vtiger_currency_info where id = '1'", true);
    $kod_waluty = $adb->query_result($pobierzGluwnaWaluteSql, 0, "currency_code");

    $checkInDatabaseResult = $adb->query($checkInDatabaseSql, true);
    $numDB = $adb->num_rows($checkInDatabaseResult);

    if (file_exists($moduleFilePath) && file_exists($moduleFilePathTpl) && ($numDB > 0) && $kod_waluty == 'PLN' && $kod_aktualnej_waluty != 'PLN' && vtlib_isModuleActive('OSSCurrencyUpdate')) {
        $czyDrukowacKolumneZPrzeliczonymPodatkiem = TRUE;
    }
    if ($czyDrukowacKolumneZPrzeliczonymPodatkiem == TRUE) {
        require_once($moduleFilePath);
    }
    $focus->id = $focus->column_fields["record_id"];
    $associated_products = OSSgetAssociatedProducts($pdftype, $focus);
    $num_products = count($associated_products);


    $vat = array();
    $sales = array();
    $service = array();

    $total_discount = 0.0;

    $suma_netto = 0.0;
    $suma_brutto = $associated_products[1]['final_details']['hdnSubTotal'];
    $rabat_calkowity = $associated_products[1]['final_details']['discount_amount_final'];
    $suma_vat = 0;


    /////////////////////////////
    /// Create a table summary of VAT
    if ($focus->column_fields['hdnTaxType'] == 'group') {
        for ($i = 1; $i <= count($associated_products); $i++) {
            $suma_netto += $associated_products[$i]['totalAfterDiscount' . $i];
            $total_discount += $associated_products[$i]['discountTotal' . $i];
        }
    } else {
        for ($i = 1; $i <= count($associated_products); $i++) {
            $suma_netto += $associated_products[$i]['totalAfterDiscount' . $i];

            $TotalAfterDiscount = $associated_products[$i]['totalAfterDiscount' . $i];
            foreach ($associated_products[$i]['taxes'] as $podatek) {
                if ($podatek['taxlabel'] == 'VAT') {
                    $vat[$podatek['percentage']] += $TotalAfterDiscount * ($podatek['percentage'] / 100.0);
                    $suma_vat += $TotalAfterDiscount * ($podatek['percentage'] / 100.0);
                }
                if ($podatek['taxlabel'] == 'Sales') {
                    $sales[$podatek['percentage']] += $TotalAfterDiscount * ($podatek['percentage'] / 100.0);
                }
                if ($podatek['taxlabel'] == 'Service') {
                    $service[$podatek['percentage']] += $TotalAfterDiscount * ($podatek['percentage'] / 100.0);
                }
            }

            // Calculating the total discount
            $total_discount += $associated_products[$i]['discountTotal' . $i];
        }
    }

    ///////////////////////////////////////////////////////////
    //This $final_details array will contain the final total, discount, Group Tax, S&H charge, S&H taxes
    $final_details = $associated_products[1]['final_details'];

    //getting the Net Total
    $price_subtotal = number_format($final_details["hdnSubTotal"], 2, '.', ',');

    //Final discount amount/percentage
    $discount_amount = $final_details["discount_amount_final"];
    $discount_percent = $final_details["discount_percentage_final"];

    if ($discount_amount != "") {
        $price_discount = number_format($discount_amount, 2, '.', ',');
        $price_disc = $discount_amount;
    } else if ($discount_percent != "") {
        //This will be displayed near Discount label - used in include/fpdf/templates/body.php
        $final_price_discount_percent = "(" . number_format($discount_percent, 2, '.', ',') . " %)";
        $price_discount = number_format((($discount_percent * $final_details["hdnSubTotal"]) / 100), 2, '.', ',');
        $price_disc = $discount_percent * $final_details["hdnSubTotal"];
    }
    else
        $price_discount = "0.00";

    //Grand Total
    $price_total = number_format($final_details["grandTotal"], 2, '.', ',');


    //To calculate the group tax amount
    if ($final_details['taxtype'] == 'group') {
        $group_tax_total = $final_details['tax_totalamount'];
        $price_salestax = number_format($group_tax_total, 2, '.', ',');

        $group_total_tax_percent = '0.00';
        $group_tax_details = $final_details['taxes'];
        for ($i = 0; $i < count($group_tax_details); $i++) {
            $group_total_tax_percent = $group_total_tax_percent + $group_tax_details[$i]['percentage'];
        }
    }
    $podatek_grupowy = ( $final_details["hdnSubTotal"] - $price_disc ) * ( $group_total_tax_percent / 100.0 );

    $prod_line = array();
    $lines = 0;

    //This is to get all prodcut details as row basis
    for ($i = 1, $j = $i - 1; $i <= $num_products; $i++, $j++) {
        $product_name[$i] = $associated_products[$i]['productName' . $i];
        $subproduct_name[$i] = split("<br>", $associated_products[$i]['subprod_names' . $i]);
        $comment[$i] = $associated_products[$i]['comment' . $i];
        $product_id[$i] = $associated_products[$i]['hdnProductId' . $i];
        $qty[$i] = $associated_products[$i]['qty' . $i];
        $unit_price[$i] = number_format($associated_products[$i]['unitPrice' . $i], 2, '.', ',');
        $list_price[$i] = $associated_products[$i]['listPrice' . $i]; // number_format($associated_products[$i]['listPrice'.$i],2,'.',',');
        $list_pricet[$i] = $associated_products[$i]['listPrice' . $i];
        $discount_total[$i] = $associated_products[$i]['discountTotal' . $i];
        $product_code[$i] = $associated_products[$i]['hdnProductcode' . $i];
        $taxable_total = $qty[$i] * $list_pricet[$i] - $discount_total[$i];
        $producttotal = $taxable_total;
        $total_taxes = '0.00';
        if ($focus->column_fields["hdnTaxType"] == "individual") {
            $total_tax_percent = '0.00';
            //This loop is to get all tax percentage and then calculate the total of all taxes
            for ($tax_count = 0; $tax_count < count($associated_products[$i]['taxes']); $tax_count++) {
                $tax_percent = $associated_products[$i]['taxes'][$tax_count]['percentage'];
                $total_tax_percent = $total_tax_percent + $tax_percent;
                $tax_amount = (($taxable_total * $tax_percent) / 100);
                $total_taxes = $total_taxes + $tax_amount;
            }
            $producttotal = $taxable_total + $total_taxes;
            $product_line[$j]["tax_percentage"] = $total_tax_percent;
            $product_line[$j]["Tax"] = $total_taxes;
            $price_salestax += $total_taxes;
        }
        $prod_total[$i] = $producttotal; // number_format($producttotal,2,'.',',');
        $product_line[$j]["Product Code"] = $product_code[$i];
        $product_line[$j]["Qty"] = $qty[$i];
        $product_line[$j]["Price"] = $list_price[$i];
        $product_line[$j]["Discount"] = $discount_total[$i];
        $product_line[$j]["Total"] = $prod_total[$i];
        $lines++;
        $product_line[$j]["Product Name"] = '<b>' . decode_html($product_name[$i]) . '</b>';

        $prod_line[$j] = 1;
        for ($count = 0; $count < count($subproduct_name[$i]); $count++) {
            if ($lines % 12 != 0) {
                $product_line[$j]["Product Name"] .= "\n <br />" . '<font color="grey" size="-1"><em>' . decode_html($subproduct_name[$i][$count]) . '</em></font>';
                $prod_line[$j]++;
            } else {
                $j++;
                $product_line[$j]["Product Name"] = decode_html($product_name[$i]);
                $product_line[$j]["Product Name"] .= "\n <br />" . decode_html($subproduct_name[$i][$count]);
                $prod_line[$j] = 2;
                $lines++;
            }
            $lines++;
        }

        if ($comment[$i] != '') {
            $product_line[$j]["Product Name"] .= "\n" . '' . decode_html($comment[$i]);
            $prod_line[$j]++;
            $lines++;
        }
    }

    $tax_pl = 0;
    $suma_podatku_w_pln = 0.00;
    if ($czyDrukowacKolumneZPrzeliczonymPodatkiem == TRUE) {
        $invoice_date_sql = "SELECT invoicedate FROM vtiger_ossinvoicecost WHERE ossinvoicecostid = $id";
        $invoice_date_result = $adb->query($invoice_date_sql, true);
        $invoice_date = $adb->query_result($invoice_date_result, 0, 'invoicedate');

        $date = $invoice_date;
        $newdate = strtotime('-1 day', strtotime($date));
        $newdate = date('Y-m-d', $newdate);

        $CurrencyUpdate = Vtiger_Record_Model::getCleanInstance( 'OSSCurrencyUpdate' );
        $kurs_waluty_result = $CurrencyUpdate->getCurrencyRate($newdate, $currency_id);
        $num_rate = $adb->num_rows($kurs_waluty_result);

        if ($num_rate == 0) {
            $CurrencyUpdate->getCurrency($newdate);
            $kurs_waluty_result = $CurrencyUpdate->getCurrencyRate($newdate, $currency_id);
            $num_rate = $adb->num_rows($kurs_waluty_result);
            if ($num_rate != 0) {
                $kurs_waluty = (float) $adb->query_result($kurs_waluty_result, 0, 'kurs');
            }
        } else {
            $kurs_waluty = (float) $adb->query_result($kurs_waluty_result, 0, 'kurs');
            $newdate = date('Y-m-d', strtotime($adb->query_result($kurs_waluty_result, 0, "data_faktyczna_kursu")));
        }
    }


    $price_salestax = number_format($price_salestax, 2, '.', ',');
    if ($final_details['taxtype'] == 'group') {
        $header = Array();

        if ($enable_ordinal_column) {
            $header[0] = $mod_strings['LBL_nr'];
        }

        if ($enable_productname_column) {
            $header[1] = $mod_strings['LBL_productname'];
        }

        if ($enable_amount_column) {
            $header[2] = $mod_strings['LBL_Quantity'];
        }

        if ($enable_listprice_column) {
            $header[3] = $mod_strings['LBL_price'];
        }

        if ($enable_discount_column) {
            $header[4] = $mod_strings['LBL_rabat'];
        }

        if ($enable_netprice_column) {
            $header[5] = $mod_strings['LBL_netto'];
        }

        if ($enable_gross_column) {
            $header[6] = $mod_strings['LBL_brutto'];
        }
    } else {
        $header = Array();

        if ($enable_ordinal_column) {
            $header[0] = $mod_strings['LBL_nr'];
        }

        if ($enable_productname_column) {
            $header[1] = $mod_strings['LBL_productname'];
        }

        if ($enable_amount_column) {
            $header[2] = $mod_strings['LBL_Quantity'];
        }

        if ($enable_listprice_column) {
            $header[3] = $mod_strings['LBL_price'];
        }

        if ($enable_discount_column) {
            $header[4] = $mod_strings['LBL_rabat'];
        }

        if ($enable_netprice_column) {
            $header[5] = $mod_strings['LBL_netto'];
        }

        if ($enable_vatpercentage_column) {
            $header[6] = $mod_strings['LBL_vat'];
        }

        if ($enable_vatamount_column) {
            $header[7] = $mod_strings['LBL_vat_waluta'] . " (" . $symbol_waluty . ")";
        }

        if ($enable_gross_column) {
            $header[8] = $mod_strings['LBL_brutto'];
        }

        if ($enable_taxes_inPLN && $czyDrukowacKolumneZPrzeliczonymPodatkiem == TRUE) {
            $header[9] = $mod_strings['TAXES_IN_PLN'];
        }
    }

    $data = Array();
    $i = 1;
    foreach ($product_line as $item) {
        $currfield = new CurrencyField($item["tax_percentage"]);
        $tax_percentage = $currfield->getDisplayValue();


        if ($final_details['taxtype'] == 'group') {
            $netto = $item['Price'] * $item['Qty'] - $item['Discount'];
            $data[$i] = Array($i, $item['Product Name'], $item['Qty'], $item['Price'], $item['Discount'], $netto, $item['Total']);
        } else {
            $tax_pln = $item['Tax'] * $kurs_waluty;
            $suma_podatku_w_pln += $tax_pln;
            $currfield = new CurrencyField($tax_pln);
            $tax_pln = $currfield->getDisplayValue();

            $netto = $item['Price'] * $item['Qty'] - $item['Discount'];
            $data[$i] = Array($i, $item['Product Name'], $item['Qty'], $item['Price'], $item['Discount'], $netto, $tax_percentage, $item['Tax'], $item['Total'], $tax_pln);
        }


        $i++;
    }

    if ($final_details['taxtype'] == 'group') {

        $width = array(30, 290, 35, 45, 45, 45, 45,);
        $align = array("center", "center", "center", "center", "center", "center", "center");
        $format = array(0, "s", 0, 2, 2, 2, 2);
    } else {
        $width = array(30, 200, 35, 40, 40, 40, 40, 40, 40, 40);
        $align = array("center", "center", "center", "center", "center", "center", "center", "center", "center", "center");
        $format = array(0, "s", 0, 2, 2, 2, 2, 2, 2);
    }

    $group_sep = ' ';
    $separatorSql = "SELECT currency_decimal_separator FROM vtiger_users WHERE id = '$current_user->id'";
    $separatorResult = $adb->query($separatorSql, true);
    $dec_sep = $adb->query_result($separatorResult, 0, 'currency_decimal_separator');

    if ($dec_sep == '') {
        $dec_sep = ' ';
    }

    if ($group_sep == $dec_sep) {
        $dec_sep = '.';
    }



    $product_table = '<table border="1" cellpadding="2">';
    $product_table .= '<tr valign="middle">';

    foreach ($header as $key => $val) {
        $product_table .= '<td width="' . $width[$key] . '" height="20" align="' . $align[$key] . '"><b><small>' . $val . '</small></b></td>';
    }
    $product_table .= '</tr>';

    $align = array("center", "left", "center", "center", "center", "center", "center", "center", "center", "center");

    foreach ($data as $row) {
        $product_table .= '<tr>';
        $i = 0;
        $j = 1;
        foreach ($row as $key => $item) {
            $sum[$i] += (float) $item;
            if ($format[$i] != 's') {
                $currfield = new CurrencyField($item);
                $item = $currfield->getDisplayValue();
            } else {
                $itarr = explode("\n\n", $item);
                $item = $itarr[0] . ' ' . $itarr[1];
            }
            if ($wyswietlaj[$key]) {
                if ($key == 9) {
                    if ($czyDrukowacKolumneZPrzeliczonymPodatkiem == TRUE) {
                        $currfield = new CurrencyField(0);
                        $zero = $currfield->getDisplayValue();

                        if (( $item != 0 ) && $kurs_waluty) {
                            $product_table .= '<td width="' . $width[$key] . '" align="' . $align[$key] . '"><small>' . number_format((float) $item, 2, $dec_sep, $group_sep) . '</small></td>';
                        } else {

                            $product_table .= '<td width="' . $width[$key] . '" align="' . $align[$key] . '"><small>np</small></td>';
                        }
                    }
                } else {
                    if ($key != 0 && $key != 1) {

                        $product_table .= '<td width="' . $width[$key] . '" align="' . $align[$key] . '"><small>' . number_format((float) $item, 2, $dec_sep, $group_sep) . '</small></td>';
                    } else {
                        $product_table .= '<td width="' . $width[$key] . '" align="' . $align[$key] . '"><small>' . $item . '</small></td>';
                    }
                }
            }
            $i++;
        }
        $product_table .= '</tr>';
    }
    
    $product_table .= "</table>";
    $i = 0;

    $product_table .= '<table cellpadding="0" cellspacing="0"><tr border="0">';

    if ($enable_ordinal_column) {
        $product_table .= '<td width="' . $width[0] . '"></td>';
    }

    if ($enable_productname_column) {
        $product_table .= '<td width="' . $width[1] . '"></td>';
    }

    if ($enable_amount_column) {
        $product_table .= '<td width="' . $width[2] . '"></td>';
    }

    if ($enable_listprice_column) {
        $product_table .= '<td width="' . $width[3] . '"></td>';
    }

    if ($enable_discount_column) {
        $product_table .= '<td width="' . $width[4] . '"><table border="1"><tr><td width="40" cellpadding="0" align="center" valign="middle"><small><b>' . number_format($total_discount, 2, $dec_sep, $group_sep) . '</b></small></td></tr></table></td>';
    }

    if ($enable_netprice_column) {
        $product_table .= '<td width="' . $width[5] . '"><table border="1"><tr><td width="40" cellpadding="0" align="center" valign="middle"><small><b>' . number_format($suma_netto, 2, $dec_sep, $group_sep) . '</b></small></td></tr></table></td>';
    }

    if ($enable_vatpercentage_column && $final_details['taxtype'] != 'group') {
        $product_table .= '<td width="' . $width[6] . '"></td>';
    }

    if ($enable_vatamount_column && $final_details['taxtype'] != 'group') {
        $product_table .= '<td width="' . $width[7] . '"><table border="1"><tr><td width="40" cellpadding="0" align="center" valign="middle"><small><b>' . number_format($suma_vat, 2, $dec_sep, $group_sep) . '</b></small></td></tr></table></td>';
    }

    if ($enable_gross_column) {
        $product_table .= '<td width="' . $width[8] . '"><table border="1"><tr><td width="40" cellpadding="0" align="center" valign="middle"><small><b>' . number_format($suma_brutto, 2, $dec_sep, $group_sep) . '</b></small></td></tr></table></td>';
    }
    if ($enable_taxes_inPLN == TRUE && $czyDrukowacKolumneZPrzeliczonymPodatkiem == TRUE) {

        if ($suma_podatku_w_pln != 0) {

            $product_table .= '<td width="' . $width[9] . '"><table border="1"><tr><td width="40" cellpadding="0" align="center" valign="middle"><small><b>' . number_format($suma_podatku_w_pln, 2, $dec_sep, $group_sep) . '</b></small></td></tr></table></td>';
        } else {
            $product_table .= '<td width="' . $width[9] . '"><table border="1"><tr><td width="40" cellpadding="0" align="center" valign="middle"><small><b>np</b></small></td></tr></table></td>';
        }
    }

    $product_table .= '</tr></table>';

    if ($final_details['taxtype'] == 'group') {
        $grup_tax = 0.00;
        for ($i = 0; $i < 3; $i++) {
			if( $associated_products[1]['final_details']['taxes'] == $associated_products[1]['final_details']['taxes'][$i]['taxname'] ){
				$grup_tax += (float) $associated_products[1]['final_details']['taxes'][$i]['amount'];
				$grup_tax_percent += (float) $associated_products[1]['final_details']['taxes'][$i]['percentage'];
			}
        }
        $currfield = new CurrencyField($grup_tax);
        $grup_tax = $currfield->getDisplayValue();
        $currfield = new CurrencyField($grup_tax_percent);
        $grup_tax_percent = $currfield->getDisplayValue();
		
    }

    $mod = strtolower($pdftype);
    if ($mod == 'quotes') {
        $idcol = "quoteid";
    } else {
        $idcol = $mod . "id";
    }
    $sql = "SELECT discount_percent, discount_amount, subtotal, total FROM vtiger_$mod WHERE $idcol = " . $id;
    $result = $adb->query($sql, true);
    $grand_total = $adb->query_result($result, 0, 'total');
    $subtotal = $adb->query_result($result, 0, "subtotal");
    $discount_percent = $adb->query_result($result, 0, 'discount_percent');
    $discount_amount = $adb->query_result($result, 0, 'discount_amount');

    if ($discount_percent != 0) {
        $discount = $subtotal * ( $discount_percent / 100.0 );
    } else {
        $discount = $discount_amount;
    }

    if (count($vat) > 0 || count($sales) > 0 || count($service) > 0) {

        $product_table .= '<br /><table width="535px" align="right" cellpadding="2">';
        $product_table .= '<tr height="10">';
        $product_table .= '<td width="87px" height="10"></td>';

        $product_table .= '<td align="right" valign="middle">';

        if (count($vat) > 1) {
            $product_table .= '<table width="135px" cellpadding="2">';
            foreach ($vat as $podatek => $suma) {
                $currfield = new CurrencyField($suma);
                $suma = $currfield->getDisplayValue();
                $wartosc_vat = number_format($podatek, 2, '.', ',');

                $currfield = new CurrencyField($suma);
                $suma = $currfield->getDisplayValue();
                $product_table .= '<tr height="10"><td align="left"><small><b>' . $app_strings['LBL_SALES'] . '(' . number_format($podatek, 2, '.', ',') . '%) :</b> ' . $suma . ' ' . $symbol_waluty . '</small></td></tr>';
            }
            $product_table .= '</table>';
        }

        $product_table .= '</td>';
        $product_table .= '</tr>';

        $product_table .= '</table>';

        if (count($sales) > 0) {
            $product_table .= '<table width="535px" border="1" cellpadding="2">';
            foreach ($sales as $podatek => $suma) {
                $currfield = new CurrencyField($suma);
                $suma = $currfield->getDisplayValue();
                $product_table .= '<tr height="10"><td align="right" valign="middle"><small><b>' . $app_strings['LBL_SALES'] . '(' . number_format($podatek, 2, '.', ',') . '%) :</b> ' . $suma . ' ' . $symbol_waluty . '</small></td></tr>';
            }

            $product_table .= '</table>';
        }
        if (count($service) > 0) {
            $product_table .= '<table width="535px" border="1" cellpadding="2">';
            foreach ($service as $podatek => $suma) {
                $currfield = new CurrencyField($suma);
                $suma = $currfield->getDisplayValue();
                $product_table .= '<tr height="10"><td align="right" valign="middle"><small><b>' . $app_strings['LBL_SERVICE'] . '(' . number_format($podatek, 2, '.', ',') . '%) :</b> ' . $suma . ' ' . $symbol_waluty . '</small></td></tr>';
            }
            $product_table .= '</table>';
        }
    }

    if ($displays_a_summary_of_invoices == TRUE) {
        $product_table .= '<table width="535px" border="0" cellpadding="2">
                            <tr>
                            <td width="85px"></td>
                            <td width="270px"><br />
                            <table bgcolor="#C0C0C0">';

        $product_table .= '<tr valign="middle"><td align="right"><small><b>' . $tab_pl["SUMMARY"] . ' / ' . $tab_us["SUMMARY"] . '</b></small></td></tr>';

        if ($rabat_calkowity != 0.00 && $enable_summary_rebate) {
            $product_table .= '<tr valign="middle"><td align="right"><small><b>' . $tab_pl["Discount Amount"] . ' / ' . $tab_us["Discount Amount"] . '</b> :</small></td></tr>';
        }

        if ($enable_summary_taxpercentage && $final_details['taxtype'] == 'group') {
            $product_table .= '<tr valign="middle"><td align="right"><small><b>' . $tab_pl["The percentage of tax"] . ' / ' . $tab_us["The percentage of tax"] . '</b> :</small></td></tr>';
        }

        if ($enable_summary_taxamount && $final_details['taxtype'] == 'group') {
            $product_table .= '<tr valign="middle"><td align="right"><small><b>' . $tab_pl["The amount of tax"] . ' / ' . $tab_us["The amount of tax"] . '</b> :</small></td></tr>';
        }

        if ($enable_summary_topay) {
            $product_table .= '<tr valign="middle"><td align="right"><small><b>' . $tab_pl["Grand Total"] . ' / ' . $tab_us["Grand Total"] . '</b> :</small></td></tr>';
        }

        $product_table .= '</table>
                    </td>
                    <td width="160px" ><br />
                            <table>';
        $product_table .= '<tr valign="middle"><td align="left"></td></tr>';

        if ($rabat_calkowity != 0.00 && $enable_summary_rebate) {
            $product_table .= '<tr valign="middle"><td align="left"><small>' . number_format($rabat_calkowity, 2, $dec_sep, $group_sep) . '</small></td></tr>';
        }

        if ($enable_summary_taxpercentage && $final_details['taxtype'] == 'group') {
            $product_table .= '<tr valign="middle"><td align="left"><small>' . number_format($grup_tax_percent, 2, $dec_sep, $group_sep) . ' (%)</small></td></tr>';
        }

        if ($enable_summary_taxpercentage && $final_details['taxtype'] == 'group') {
            $product_table .= '<tr valign="middle"><td align="left"><small>' . number_format($grup_tax, 2, $dec_sep, $group_sep) . ' (' . $symbol_waluty . ')</small></td></tr>';
        }

        if ($enable_summary_topay) {
            $product_table .= '<tr valign="middle"><td align="left"><small>' . number_format($grand_total, 2, $dec_sep, $group_sep) . ' (' . $symbol_waluty . ')</small></td></tr>';
        }

        $product_table .= '         
                            </table>
                    </td>
                    </tr></table>
                    <br/>';
    }
    
    return $product_table;
}

?>