@if (
		(isset($country) and $country->has('lang') and config('app.locale') != $country->get('lang')->get('abbr')) or
		(config('settings.seo.no_index_all') == true) or
		(str_contains(\Route::currentRouteAction(), 'Search\CategoryController') and config('settings.seo.no_index_categories') == true) or
		(str_contains(\Route::currentRouteAction(), 'Search\TagController') and config('settings.seo.no_index_tags') == true) or
		(str_contains(\Route::currentRouteAction(), 'Search\CityController') and config('settings.seo.no_index_cities') == true) or
		(str_contains(\Route::currentRouteAction(), 'Search\UserController') and config('settings.seo.no_index_users') == true)
	)
	<meta name="robots" content="noindex,nofollow">
	<meta name="googlebot" content="noindex">
@endif