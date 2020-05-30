<?php
namespace olafnorge\Html;

use Collective\Html\HtmlServiceProvider as BaseHtmlServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

class HtmlServiceProvider extends BaseHtmlServiceProvider implements DeferrableProvider {


    /**
     * Register the service provider.
     */
    public function register(): void {
        parent::register();

        // override base aliases
        $this->app->alias('html', HtmlBuilder::class);
        $this->app->alias('form', FormBuilder::class);
    }


    /**
     * Register the HTML builder instance.
     *
     * @return void
     */
    protected function registerHtmlBuilder() {
        $this->app->singleton('html', function ($app) {
            return new HtmlBuilder($app['url'], $app['view']);
        });
    }


    /**
     * Register the form builder instance.
     *
     * @return void
     */
    protected function registerFormBuilder() {
        $this->app->singleton('form', function ($app) {
            $form = new FormBuilder($app['html'], $app['url'], $app['view'], $app['session.store']->token(), $app['request']);

            return $form->setSessionStore($app['session.store']);
        });
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array {
        return ['html', 'form', HtmlBuilder::class, FormBuilder::class];
    }
}
