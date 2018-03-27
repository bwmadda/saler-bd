
@extends('layouts.master')

@section('search')
	@parent
@endsection

@section('content')
	<div class="main-container" id="homepage">

		@if (Session::has('flash_notification'))
			@include('common.spacer')
			<?php $paddingTopExists = true; ?>
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						@include('flash::message')
					</div>
				</div>
			</div>
		@endif

		@if (isset($sections) and $sections->count() > 0)
			@foreach($sections as $section)
				@if (view()->exists($section->view))
					@include($section->view, ['firstSection' => $loop->first])
				@endif
			@endforeach
		@endif

	</div>
@endsection

@section('after_scripts')
@endsection
