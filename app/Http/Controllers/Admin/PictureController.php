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

use Illuminate\Support\Facades\Input;
use Larapen\Admin\app\Http\Controllers\PanelController;
use Larapen\Admin\app\Http\Requests\Request as StoreRequest;
use Larapen\Admin\app\Http\Requests\Request as UpdateRequest;

class PictureController extends PanelController
{
	public function setup()
	{
		/*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
		$this->xPanel->setModel('App\Models\Picture');
		$this->xPanel->setRoute(config('larapen.admin.route_prefix', 'admin') . '/picture');
		$this->xPanel->setEntityNameStrings(__t('picture'), __t('pictures'));
		$this->xPanel->removeButton('create');
		if (!request()->input('order')) {
			$this->xPanel->orderBy('created_at', 'DESC');
		}
		
		// Filters
		// -----------------------
		$this->xPanel->addFilter([
			'name'  => 'post_id',
			'type'  => 'text',
			'label' => __t('Ad') . ' (ID)',
		],
		false,
		function ($value) {
			$this->xPanel->addClause('where', 'post_id', '=', $value);
		});
		// -----------------------
		$this->xPanel->addFilter([
			'name'  => 'status',
			'type'  => 'dropdown',
			'label' => __t('Status'),
		], [
			1 => __t('Unactivated'),
			2 => __t('Activated'),
		], function ($value) {
			if ($value == 1) {
				$this->xPanel->addClause('where', 'active', '=', 0);
			}
			if ($value == 2) {
				$this->xPanel->addClause('where', 'active', '=', 1);
			}
		});
		
		
		/*
		|--------------------------------------------------------------------------
		| COLUMNS AND FIELDS
		|--------------------------------------------------------------------------
		*/
		// COLUMNS
		$this->xPanel->addColumn([
			'name'  => 'id',
			'label' => "ID",
		]);
		$this->xPanel->addColumn([
			'name'          => 'filename',
			'label'         => __t("Filename"),
			'type'          => 'model_function',
			'function_name' => 'getFilenameHtml',
		]);
		$this->xPanel->addColumn([
			'name'          => 'post_id',
			'label'         => __t("Ad"),
			'type'          => 'model_function',
			'function_name' => 'getPostTitleHtml',
		]);
		$this->xPanel->addColumn([
			'name'          => 'active',
			'label'         => __t("Active"),
			'type'          => 'model_function',
			'function_name' => 'getActiveHtml',
		]);
		
		// FIELDS
		$this->xPanel->addField([
			'name'  => 'post_id',
			'type'  => 'hidden',
			'value' => Input::get('post_id'),
		], 'create');
		$this->xPanel->addField([
			'name'   => 'filename',
			'label'  => __t("Picture"),
			'type'   => 'image',
			'upload' => true,
			'disk'   => 'uploads',
		]);
		$this->xPanel->addField([
			'name'  => 'active',
			'label' => __t("Active"),
			'type'  => 'checkbox',
			'value' => 1,
		]);
	}
	
	public function store(StoreRequest $request)
	{
		return parent::storeCrud($request);
	}
	
	public function update(UpdateRequest $request)
	{
		return parent::updateCrud($request);
	}
}
