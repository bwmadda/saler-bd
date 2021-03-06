<?php
/**
 * LaraClassified - Geo Classified Ads CMS
 * Copyright (c) BedigitCom. All Rights Reserved
 *
 * Website: http://www.bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from Codecanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

namespace App\Http\Controllers\Admin;

use Larapen\Admin\app\Http\Controllers\PanelController;
use App\Http\Requests\Admin\CurrencyRequest as StoreRequest;
use App\Http\Requests\Admin\CurrencyRequest as UpdateRequest;

class CurrencyController extends PanelController
{
	public function setup()
	{
		/*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
		$this->xPanel->setModel('App\Models\Currency');
		$this->xPanel->setRoute(config('larapen.admin.route_prefix', 'admin') . '/currency');
		$this->xPanel->setEntityNameStrings(__t('currency'), __t('currencies'));
		
		/*
		|--------------------------------------------------------------------------
		| COLUMNS AND FIELDS
		|--------------------------------------------------------------------------
		*/
		// COLUMNS
		$this->xPanel->addColumn([
			'name'  => 'code',
			'label' => __t("Code"),
		]);
		$this->xPanel->addColumn([
			'name'  => 'name',
			'label' => __t("Name"),
		]);
		$this->xPanel->addColumn([
			'name'  => 'html_entity',
			'label' => __t("Html Entity"),
		]);
		$this->xPanel->addColumn([
			'name'          => 'in_left',
			'label'         => __t("Symbol in left"),
			'type'          => 'model_function',
			'function_name' => 'getPositionHtml',
		]);
		
		// FIELDS
		$this->xPanel->addField([
			'name'       => 'code',
			'label'      => __t("Code"),
			'type'       => 'text',
			'attributes' => [
				'placeholder' => __t('Enter the currency code (ISO Code)'),
			],
		]);
		$this->xPanel->addField([
			'name'              => 'name',
			'label'             => __t("Name"),
			'type'              => 'text',
			'attributes'        => [
				'placeholder' => __t("Name"),
			],
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'html_entity',
			'label'             => __t("Html Entity"),
			'type'              => 'text',
			'attributes'        => [
				'placeholder' => __t('Enter the html entity code'),
			],
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'font_arial',
			'label'             => __t("Font Arial"),
			'type'              => 'text',
			'attributes'        => [
				'placeholder' => __t('Enter the font arial code'),
			],
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'font_code2000',
			'label'             => __t("Font Code2000"),
			'type'              => 'text',
			'attributes'        => [
				'placeholder' => __t('Enter the font code2000 code'),
			],
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'unicode_decimal',
			'label'             => __t("Unicode Decimal"),
			'type'              => 'text',
			'attributes'        => [
				'placeholder' => __t('Enter the unicode decimal code'),
			],
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'unicode_hex',
			'label'             => __t("Unicode Hex"),
			'type'              => 'text',
			'attributes'        => [
				'placeholder' => __t('Enter the unicode hex code'),
			],
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'in_left',
			'label'             => __t("Symbol in left"),
			'type'              => 'checkbox',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
				'style' => 'margin-top: 20px;',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'decimal_places',
			'label'             => __t("Decimal Places"),
			'type'              => 'text',
			'attributes'        => [
				'placeholder' => __t('Enter the decimal places'),
			],
			'hint'              => __t('Number after decimal. Ex: 2 => 150.00 [or] 3 => 150.000'),
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'decimal_separator',
			'label'             => __t("Decimal Separator"),
			'type'              => 'text',
			'attributes'        => [
				'placeholder' => __t('Enter the decimal separator'),
				'maxlength'   => 1,
			],
			'hint'              => __t('Ex: "." => 150.00 [or] "," => 150,00'),
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'thousand_separator',
			'label'             => __t("Thousand Separator"),
			'type'              => 'text',
			'attributes'        => [
				'placeholder' => __t('Enter the thousand separator'),
				'maxlength'   => 1,
			],
			'hint'              => __t('Ex: "," => 150,000.00 [or] whitespace => 150 000.000'),
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
	}
	
	public function store(StoreRequest $request)
	{
		return parent::storeCrud();
	}
	
	public function update(UpdateRequest $request)
	{
		return parent::updateCrud();
	}
}
