<?php namespace Larapen\LaravelLocalization\Middleware;

use Illuminate\Http\RedirectResponse;
use Closure;

class LocaleSessionRedirect extends LaravelLocalizationMiddlewareBase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // If the URL of the request is in exceptions.
        if ($this->shouldIgnore($request)) {
            return $next($request);
        }

        $params = explode('/', $request->path());
        $locale = session('locale', false);

        if (count($params) > 0 && $locale = app('laravellocalization')->checkLocaleInSupportedLocales($params[0])) {
            session(['locale' => $params[0]]);

            return $next($request);
        }

        if ($locale && app('laravellocalization')->checkLocaleInSupportedLocales($locale) && !(app('laravellocalization')->getDefaultLocale() === $locale && app('laravellocalization')->hideDefaultLocaleInURL())) {
            app('session')->reflash();
            $redirection = app('laravellocalization')->getLocalizedURL($locale);

            return new RedirectResponse($redirection, 302, ['Vary' => 'Accept-Language']);
        }

        return $next($request);
    }
}
