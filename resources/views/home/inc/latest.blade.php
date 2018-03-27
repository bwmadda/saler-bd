<?php
if (!isset($cacheExpiration)) {
    $cacheExpiration = (int)config('settings.other.cache_expiration');
}
if (config('settings.listing.display_mode') == '.compact-view') {
	$colDescBox = 'col-sm-9';
	$colPriceBox = 'col-sm-3';
} else {
	$colDescBox = 'col-sm-7';
	$colPriceBox = 'col-sm-3';
}
?>
<link href="/assets/slider/css/stylee.css" rel='stylesheet' type='text/css' />
@if (isset($posts) and count($posts) > 0)
	@include('home.inc.spacer')

@endif


<hr style="border: 1px solid;">

<div class="col-sm-3 page-sidebar mobile-filter-sidebar" style="padding-bottom: 20px;">
	<aside>

		<div class="inner-box enable-long-words">


            <!-- Category -->
			<div id="catsList" class="categories-list list-filter" <?php echo (isset($style)) ? $style : ''; ?>>
				<h5 class="list-title">
                    <strong><a href="#">{{ t('All Categories') }}</a></strong>
                </h5>
				<ul class="list-unstyled">
					@foreach ($cats->groupBy('parent_id')->get(0) as $iCat)
						<li onmouseover="showSubCategories({{ $iCat->id }})" onmouseout="hideSubCategories({{ $iCat->id }})">
							<a href="{{ lurl(trans('routes.v-search-cat', ['countryCode' => $country->get('icode'), 'catSlug' => $iCat->slug])) }}" title="{{ $iCat->name }}">
								<span class="title">{{ $iCat->name }}</span>
								<span class="count"></span>
							</a>
							<ul class="list-unstyled" id="{{ $iCat->id }}" style="display: none">
                                @foreach ($iCat->children as $iCatSub)
                                    <li>
                                    	<a href="{{ lurl(trans('routes.v-search-cat', ['countryCode' => $country->get('icode'), 'catSlug' => $iCat->slug.'/'.$iCatSub->slug])) }}" title="{{ $iCatSub->name }}">
                                            <span class="title">{{ $iCatSub->name }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
						</li>
					@endforeach
				</ul>
			</div>

			<!-- City -->
            <div class="locations-list list-filter">
                <h5 class="list-title"><strong><a href="#">{{ t('Locations') }}</a></strong></h5>
                <ul class="browse-list list-unstyled long-list">
                    @if (isset($cities) and $cities->count() > 0)
                        @foreach ($cities as $city)
                            <?php
                            $fullUrlLocation = lurl(trans('routes.v-search', ['countryCode' => $country->get('icode')]));
                            $locationParams = [
                                'l'  => $city->id,
                                'r'  => '',
                                'c'  => (isset($cat)) ? $cat->tid : '',
                                'sc' => (isset($subCat)) ? $subCat->tid : '',
                            ];
                            ?>
                            <li>
                                @if ((isset($uriPathCityId) and $uriPathCityId == $city->id) or (Request::input('l')==$city->id))
                                    <strong>
                                        <a href="{!! qsurl($fullUrlLocation, array_merge(Request::except(array_keys($locationParams)), $locationParams)) !!}" title="{{ $city->name }}">
                                            {{ $city->name }}
                                        </a>
                                    </strong>
                                @else
                                    <a href="{!! qsurl($fullUrlLocation, array_merge(Request::except(array_keys($locationParams)), $locationParams)) !!}" title="{{ $city->name }}">
                                        {{ $city->name }}
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>

			<div style="clear:both"></div>
		</div>
	</aside>
</div>




<div class="col-sm-9 " style="padding-bottom: 20px;">

  <div class="col-lg-12 content-box layout-section">
    <div class="row row-featured row-featured-category">
      <div class="col-lg-12 box-title no-border">
        <div class="inner">
          <h2>
            <span class="title-3">{!! t('Home - Latest Ads') !!}</span>
            <a href="{{ lurl(trans('routes.v-search', ['countryCode' => $country->get('icode')])) }}" class="sell-your-item">
              {{ t('View more') }} <i class="icon-th-list"></i>
            </a>
          </h2>
        </div>
      </div>
      <div class="noSideBar">
        <div class="mid_slider_info">
          <div class="inner_sec_info_w3ls_agile">
            <div id="myCarousel" class="carousel slide" data-ride="carousel">
              <div class="carousel-inner" role="listbox">
                <div class="item active">
                  <div class="row">
                <?php
                $count = 1;
                foreach($posts as $key => $post):
                if (empty($countries) or !$countries->has($post->country_code)) continue;

                // Get Pack Info
                $package = null;
                if ($post->featured == 1) {
                  $cacheId = 'package.' . $post->py_package_id . '.' . config('app.locale');
                  $package = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {
                    $package = \App\Models\Package::transById($post->py_package_id);
                    return $package;
                  });
                }

                // Get PostType Info
                $cacheId = 'postType.' . $post->post_type_id . '.' . config('app.locale');
                $postType = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {
                  $postType = \App\Models\PostType::transById($post->post_type_id);
                  return $postType;
                });
                if (empty($postType)) continue;

                // Get Post's Pictures
                $pictures = \App\Models\Picture::where('post_id', $post->id)->orderBy('position')->orderBy('id');
                if ($pictures->count() > 0) {
                  $postImg = resize($pictures->first()->filename, 'medium');
                } else {
                  $postImg = resize(config('larapen.core.picture.default'));
                }

                // Get the Post's City
                $cacheId = config('country.code') . '.city.' . $post->city_id;
                $city = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {
                  $city = \App\Models\City::find($post->city_id);
                  return $city;
                });
                if (empty($city)) continue;

                // Convert the created_at date to Carbon object
                $post->created_at = \Date::parse($post->created_at)->timezone(config('timezone.id'));
                $post->created_at = $post->created_at->ago();

                // Category
                $cacheId = 'category.' . $post->category_id . '.' . config('app.locale');
                $liveCat = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {
                  $liveCat = \App\Models\Category::transById($post->category_id);
                  return $liveCat;
                });

                // Check parent
                if (empty($liveCat->parent_id)) {
                  $liveCatParentId = $liveCat->id;
                  $liveCatType = $liveCat->type;
                } else {
                  $liveCatParentId = $liveCat->parent_id;

                  $cacheId = 'category.' . $liveCat->parent_id . '.' . config('app.locale');
                  $liveParentCat = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($liveCat) {
                    $liveParentCat = \App\Models\Category::transById($liveCat->parent_id);
                    return $liveParentCat;
                  });
                  $liveCatType = (!empty($liveParentCat)) ? $liveParentCat->type : 'classified';
                }

                // Check translation
                $liveCatName = $liveCat->name;
                ?>
                <?php if($count%5==0) { ?>
                  </div>
                </div>
                <div class="item">
                  <div class="row">
                <?php } ?>
                    <div class="col-md-3 col-sm-3 col-xs-3 slidering">
                      <div class="thumbnail">
                        <span class="photo-count" style="right: 20px; !important;"><i class="fa fa-camera"></i> {{ $pictures->count() }} </span>
                        <a href="{{ lurl($post->uri) }}">
                          <img src="{{ $postImg }}" alt="Image" style="max-width:100%;">
                        </a>
                      </div>

                      <h3 style="padding-bottom: 3px !important;"><a href="{{ lurl($post->uri) }}">{{ mb_ucfirst(str_limit($post->title, 70)) }} </a></h3>
                      <div class="pi-price">
                        @if (isset($liveCatType) and !in_array($liveCatType, ['not-salable']))
                          @if ($post->price > 0)
                            {!! \App\Helpers\Number::money($post->price) !!}
                          @else
                            {!! \App\Helpers\Number::money('--') !!}
                          @endif
                        @else
                          {{ '--' }}
                        @endif
                      </div>
                      <span class="info-row">
                        <span class="add-type business-ads tooltipHere" data-toggle="tooltip" data-placement="right" title="{{ $postType->name }}">
                          {{ strtoupper(mb_substr($postType->name, 0, 1)) }}
                        </span>&nbsp;
                        <span class="date"><i class="icon-clock"> </i> {{ $post->created_at }} </span>\
                        @if (isset($liveCatParentId) and isset($liveCatName))
                        <!--
                          <span class="category">
                            - <a href="{!! qsurl(config('app.locale').'/'.trans('routes.v-search', ['countryCode' => $country->get('icode')]), array_merge(Request::except('c'), ['c'=>$liveCatParentId])) !!}" class="info-link">{{ $liveCatName }}</a>
                          </span>
                        -->
                        @endif
                        - <span class="item-location"><i class="fa fa-map-marker"></i>&nbsp;
                          <a href="{!! qsurl(config('app.locale').'/'.trans('routes.v-search', ['countryCode' => $country->get('icode')]), array_merge(Request::except(['l', 'location']), ['l'=>$post->city_id])) !!}" class="info-link">{{ $city->name }}</a> {{ (isset($post->distance)) ? '- ' . round(lengthPrecision($post->distance), 2) . unitOfLength() : '' }}
                        </span>
                      </span>
                    </div>
                <?php $count++; endforeach; ?>
                  </div>
                </div>
              </div>
              <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                <span class="fa fa-chevron-left" aria-hidden="true"></span>

                <span class="sr-only">Previous</span>
              </a>
              <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                <span class="fa fa-chevron-right" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
              </a>
              <!-- The Modal -->
            </div>
          </div>
        </div>




        <div style="clear: both"></div>
        @if (isset($latestOptions) and isset($latestOptions['show_show_more_btn']) and $latestOptions['show_show_more_btn'] == '1')

        <!-- Hide As Clients Requirements 
        <div class="mb20" style="text-align: center;">
          <a href="{{ lurl(trans('routes.v-search', ['countryCode' => $country->get('icode')])) }}" class="btn btn-default mt10">
            <i class="fa fa-arrow-circle-right"></i> {{ t('View more') }}
          </a>
        </div>
      -->

        @endif
      </div>
    </div>
  </div>








	@foreach ($cats->groupBy('parent_id')->get(0) as $iCat)
		<?php
		$isPostAvailable = false;
		foreach($categoryPost as $key => $post)
		{
            $cacheId = 'category.' . $post->category_id . '.' . config('app.locale');
            $liveCat = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {
                $liveCat = \App\Models\Category::transById($post->category_id);
                return $liveCat;
            });

            if($iCat->id==$liveCat->parent_id)
			{
                $isPostAvailable = true;
			}
		}
		if($isPostAvailable==true)
		{
		?>

		<p style="font-weight: bold; font-size: 20px;">
			<a href="{{ lurl(trans('routes.v-search-cat', ['countryCode' => $country->get('icode'), 'catSlug' => $iCat->slug])) }}" title="{{ $iCat->name }}">
				{{ $iCat->name }}
			</a>
		</p>
		<div class="mid_slider_info">
			<div class="inner_sec_info_w3ls_agile">
				<div id="myCarousel{{ $iCat->id }}" class="carousel slide" data-ride="carousel">
					<div class="carousel-inner" role="listbox">
						<div class="item active">
							<div class="row">
                                <?php
                                $count = 1;
                                foreach($categoryPost as $key => $post):
                                if (empty($countries) or !$countries->has($post->country_code)) continue;

                                // Get Pack Info
                                $package = null;
                                if ($post->featured == 1) {
                                    $cacheId = 'package.' . $post->py_package_id . '.' . config('app.locale');
                                    $package = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {
                                        $package = \App\Models\Package::transById($post->py_package_id);
                                        return $package;
                                    });
                                }

                                // Get PostType Info
                                $cacheId = 'postType.' . $post->post_type_id . '.' . config('app.locale');
                                $postType = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {
                                    $postType = \App\Models\PostType::transById($post->post_type_id);
                                    return $postType;
                                });
                                if (empty($postType)) continue;

                                // Get Post's Pictures
                                $pictures = \App\Models\Picture::where('post_id', $post->id)->orderBy('position')->orderBy('id');
                                if ($pictures->count() > 0) {
                                    $postImg = resize($pictures->first()->filename, 'medium');
                                } else {
                                    $postImg = resize(config('larapen.core.picture.default'));
                                }

                                // Get the Post's City
                                $cacheId = config('country.code') . '.city.' . $post->city_id;
                                $city = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {
                                    $city = \App\Models\City::find($post->city_id);
                                    return $city;
                                });
                                if (empty($city)) continue;

                                // Convert the created_at date to Carbon object
                                $post->created_at = \Date::parse($post->created_at)->timezone(config('timezone.id'));
                                $post->created_at = $post->created_at->ago();

                                // Category
                                $cacheId = 'category.' . $post->category_id . '.' . config('app.locale');
                                $liveCat = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {
                                    $liveCat = \App\Models\Category::transById($post->category_id);
                                    return $liveCat;
                                });

                                // Check parent
                                if (empty($liveCat->parent_id)) {
                                    $liveCatParentId = $liveCat->id;
                                    $liveCatType = $liveCat->type;
                                } else {
                                    $liveCatParentId = $liveCat->parent_id;

                                    $cacheId = 'category.' . $liveCat->parent_id . '.' . config('app.locale');
                                    $liveParentCat = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($liveCat) {
                                        $liveParentCat = \App\Models\Category::transById($liveCat->parent_id);
                                        return $liveParentCat;
                                    });
                                    $liveCatType = (!empty($liveParentCat)) ? $liveParentCat->type : 'classified';
                                }

                                // Check translation
                                $liveCatName = $liveCat->name;

                                if($iCat->id==$liveCat->parent_id)
                                {

                                if($count%5==0) { ?>
							</div>
						</div>
						<div class="item">
							<div class="row">
                                <?php } ?>
								<div class="col-md-3 col-sm-3 col-xs-3 slidering">
									<div class="thumbnail">
										<span class="photo-count" style="right: 20px; !important;"><i class="fa fa-camera"></i> {{ $pictures->count() }} </span>
										<a href="{{ lurl($post->uri) }}">
											<img src="{{ $postImg }}" alt="Image" style="max-width:100%;">
										</a>
									</div>

									<h3 style="padding-bottom: 3px !important;"><a href="{{ lurl($post->uri) }}">{{ mb_ucfirst(str_limit($post->title, 70)) }} </a></h3>
									<div class="pi-price">
										@if (isset($liveCatType) and !in_array($liveCatType, ['not-salable']))
											@if ($post->price > 0)
												{!! \App\Helpers\Number::money($post->price) !!}
											@else
												{!! \App\Helpers\Number::money('--') !!}
											@endif
										@else
											{{ '--' }}
										@endif
									</div>
									<span class="info-row">
													<span class="add-type business-ads tooltipHere" data-toggle="tooltip" data-placement="right" title="{{ $postType->name }}">
														{{ strtoupper(mb_substr($postType->name, 0, 1)) }}
													</span>&nbsp;
													<span class="date"><i class="icon-clock"> </i> {{ $post->created_at }} </span>\
										@if (isset($liveCatParentId) and isset($liveCatName))
											<span class="category">
															- <a href="{!! qsurl(config('app.locale').'/'.trans('routes.v-search', ['countryCode' => $country->get('icode')]), array_merge(Request::except('c'), ['c'=>$liveCatParentId])) !!}" class="info-link">{{ $liveCatName }}</a>
														</span>
										@endif
										- <span class="item-location"><i class="fa fa-map-marker"></i>&nbsp;
														<a href="{!! qsurl(config('app.locale').'/'.trans('routes.v-search', ['countryCode' => $country->get('icode')]), array_merge(Request::except(['l', 'location']), ['l'=>$post->city_id])) !!}" class="info-link">{{ $city->name }}</a> {{ (isset($post->distance)) ? '- ' . round(lengthPrecision($post->distance), 2) . unitOfLength() : '' }}
													</span>
												</span>
								</div>

                                <?php $count++;
                                }

								endforeach; ?>
							</div>
						</div>
					</div>
					<a class="left carousel-control" href="#myCarousel{{ $iCat->id }}" role="button" data-slide="prev">
						<span class="fa fa-chevron-left" aria-hidden="true"></span>

						<span class="sr-only">Previous</span>
					</a>
					<a class="right carousel-control" href="#myCarousel{{ $iCat->id }}" role="button" data-slide="next">
						<span class="fa fa-chevron-right" aria-hidden="true"></span>
						<span class="sr-only">Next</span>
					</a>
					<!-- The Modal -->
				</div>
			</div>
		</div>
		<hr style="border: 1px solid;">
		<?php } ?>

	@endforeach

</div>


@section('after_scripts')
    @parent
	@foreach ($cats->groupBy('parent_id')->get(0) as $iCat)
		<script>
            $('#myCarousel<?php echo $iCat->id; ?>').carousel({
                interval: false
            });
		</script>
	@endforeach

    <script>
        /* Default view (See in /js/script.js) */
		@if (isset($posts) and count($posts) > 0)
			@if (config('settings.listing.display_mode') == '.grid-view')
				gridView('.grid-view');
			@elseif (config('settings.listing.display_mode') == '.list-view')
				listView('.list-view');
			@elseif (config('settings.listing.display_mode') == '.compact-view')
				compactView('.compact-view');
			@else
				gridView('.grid-view');
			@endif
		@else
			listView('.list-view');
		@endif
		/* Save the Search page display mode */
		var listingDisplayMode = readCookie('listing_display_mode');
		if (!listingDisplayMode) {
			createCookie('listing_display_mode', '{{ config('settings.listing.display_mode', '.grid-view') }}', 7);
		}

		/* Favorites Translation */
		var lang = {
			labelSavePostSave: "{!! t('Save ad') !!}",
			labelSavePostRemove: "{!! t('Remove favorite') !!}",
			loginToSavePost: "{!! t('Please log in to save the Ads.') !!}",
			loginToSaveSearch: "{!! t('Please log in to save your search.') !!}",
			confirmationSavePost: "{!! t('Post saved in favorites successfully !') !!}",
			confirmationRemoveSavePost: "{!! t('Post deleted from favorites successfully !') !!}",
			confirmationSaveSearch: "{!! t('Search saved successfully !') !!}",
			confirmationRemoveSaveSearch: "{!! t('Search deleted successfully !') !!}"
		};

		function showSubCategories($id) {
		    $("#"+$id).css({ display: "block" });
		}

		function hideSubCategories($id) {
            $("#"+$id).css({ display: "none" });
        }
    </script>
@endsection
