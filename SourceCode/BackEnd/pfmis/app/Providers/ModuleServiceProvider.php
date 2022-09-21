<?php
namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;

class ModuleServiceProvider extends RouteServiceProvider{

    protected $namespace = '';

    protected $mapWhat = '';


    public function register(){}

    public function boot(){

        $this->setNamespace();
        parent::boot();
    }

    /**
     * Override the map() function of Illuminate\Foundation\Support\Providers\RouteServiceProvider
     * it will be call by loadRoutes() function
     *
     * @return void
     */
    public function map(){
        $modules = config('modules');
        switch($this->mapWhat){
            case $modules['task']['folder']:
                $this->mapApi($modules['task']);
                break;

            case $modules['sysadmin']['folder']:
                if(request()->is($modules['sysadmin']['prefix_url'] . '/api/*')){
                    $this->mapApi($modules['sysadminapi']);
                }else{
                    $this->mapModules($modules['sysadmin']);
                }
                break;

            case $modules['listing']['folder']:
                $this->mapApi($modules['listing']);
                break;

            case $modules['customer']['folder']:
                $this->mapApi($modules['customer']);
                break;

            case $modules['contract']['folder']:
                $this->mapApi($modules['contract']);
                break;

            case $modules['doc']['folder']:
                $this->mapApi($modules['doc']);
                break;

            case $modules['extensions']['folder']:
                $this->mapApi($modules['extensions']);
                break;
            case $modules['report']['folder']:
                $this->mapApi($modules['report']);
            case $modules['accounting']['folder']:
                $this->mapApi($modules['accounting']);
                break;
            case $modules['dashboard']['folder']:
                $this->mapApi($modules['dashboard']);
                break;
            case $modules['state-budget-planning']['folder']:
                $this->mapApi($modules['state-budget-planning']);
                break;
            case $modules['state-budget-revenues']['folder']:
                $this->mapApi($modules['state-budget-revenues']);
                break;
            case $modules['state-budget-expenditures']['folder']:
                $this->mapApi($modules['state-budget-expenditures']);
                break;
            case $modules['state-budget-settlement']['folder']:
                $this->mapApi($modules['state-budget-settlement']);
                break;

            default:
        }
    }

    /**
     * Set the corresponding namspace based on the prefix url
     *
     * @return void
     */
    private function setNamespace(){

        $modules = config('modules');

        if(request()->is($modules['task']['prefix_url']) || request()->is($modules['task']['prefix_url'] . '/*')){
            $this->namespace = join('\\', ['Module', $modules['task']['folder'], 'Controllers']);
            $this->mapWhat = $modules['task']['folder'];
        }
        elseif(request()->is($modules['sysadmin']['prefix_url']) || request()->is($modules['sysadmin']['prefix_url'] . '/*')){
            if(request()->is($modules['sysadmin']['prefix_url'] . '/api')){
                $this->namespace = join('\\', ['Module', $modules['sysadmin']['folder'], 'Controllers']);
                $this->mapWhat = $modules['sysadmin']['folder'];
            }else{
                $this->namespace = join('\\', ['Module', $modules['sysadmin']['folder'], 'Controllers']);
                $this->mapWhat = $modules['sysadmin']['folder'];
            }
        }
        elseif(request()->is($modules['listing']['prefix_url']) || request()->is($modules['listing']['prefix_url'] . '/*')){
            $this->namespace = join('\\', ['Module', $modules['listing']['folder'], 'Controllers']);
            $this->mapWhat = $modules['listing']['folder'];
        }elseif(request()->is($modules['extensions']['prefix_url']) || request()->is($modules['extensions']['prefix_url'] . '/*')){
            $this->namespace = join('\\', ['Module', $modules['extensions']['folder'], 'Controllers']);
            $this->mapWhat = $modules['extensions']['folder'];
        }elseif(request()->is($modules['accounting']['prefix_url']) || request()->is($modules['accounting']['prefix_url'] . '/*')){
            $this->namespace = join('\\', ['Module', $modules['accounting']['folder'], 'Controllers']);
            $this->mapWhat = $modules['accounting']['folder'];
        }elseif(request()->is($modules['dashboard']['prefix_url']) || request()->is($modules['dashboard']['prefix_url'] . '/*')){
            $this->namespace = join('\\', ['Module', $modules['dashboard']['folder'], 'Controllers']);
            $this->mapWhat = $modules['dashboard']['folder'];
        }
        elseif(request()->is($modules['customer']['prefix_url']) || request()->is($modules['customer']['prefix_url'] . '/*')){
            $this->namespace = join('\\', ['Module', $modules['customer']['folder'], 'Controllers']);
            $this->mapWhat = $modules['customer']['folder'];
        }
        elseif(request()->is($modules['contract']['prefix_url']) || request()->is($modules['contract']['prefix_url'] . '/*')){
            $this->namespace = join('\\', ['Module', $modules['contract']['folder'], 'Controllers']);
            $this->mapWhat = $modules['contract']['folder'];
        }
        elseif(request()->is($modules['doc']['prefix_url']) || request()->is($modules['doc']['prefix_url'] . '/*')){
            $this->namespace = join('\\', ['Module', $modules['doc']['folder'], 'Controllers']);
            $this->mapWhat = $modules['doc']['folder'];
        }elseif(request()->is($modules['report']['prefix_url']) || request()->is($modules['report']['prefix_url'] . '/*')){
            $this->namespace = join('\\', ['Module', $modules['report']['folder'], 'Controllers']);
            $this->mapWhat = $modules['report']['folder'];
        }elseif(request()->is($modules['state-budget-planning']['prefix_url']) || request()->is($modules['state-budget-planning']['prefix_url'] . '/*')){
            $this->namespace = join('\\', ['Module', $modules['state-budget-planning']['folder'], 'Controllers']);
            $this->mapWhat = $modules['state-budget-planning']['folder'];
        }elseif(request()->is($modules['state-budget-revenues']['prefix_url']) || request()->is($modules['state-budget-revenues']['prefix_url'] . '/*')){
            $this->namespace = join('\\', ['Module', $modules['state-budget-revenues']['folder'], 'Controllers']);
            $this->mapWhat = $modules['state-budget-revenues']['folder'];
        }elseif(request()->is($modules['state-budget-expenditures']['prefix_url']) || request()->is($modules['state-budget-expenditures']['prefix_url'] . '/*')){
            $this->namespace = join('\\', ['Module', $modules['state-budget-expenditures']['folder'], 'Controllers']);
            $this->mapWhat = $modules['state-budget-expenditures']['folder'];
        }elseif(request()->is($modules['state-budget-settlement']['prefix_url']) || request()->is($modules['state-budget-settlement']['prefix_url'] . '/*')){
            $this->namespace = join('\\', ['Module', $modules['state-budget-settlement']['folder'], 'Controllers']);
            $this->mapWhat = $modules['state-budget-settlement']['folder'];
        }
    }


    /**
     * Mapping module routes and views
     *
     * @param array $mod
     * @return void
     */
    protected function mapModules($mod){
        $view_dir = implode(DIRECTORY_SEPARATOR, [dirname(__DIR__, 2),  'Modules', $mod['folder'], 'Views']);
        $route_file = implode(DIRECTORY_SEPARATOR, [dirname(__DIR__, 2),  'Modules',  $mod['folder'], 'Routes', 'web.php']);
        $middleware = ['web'];
        if(is_array($mod['group_middleware']) && !empty($mod['group_middleware'])){
            $middleware = array_merge($middleware, $mod['group_middleware']);
        }
        Route::middleware($middleware)
            ->prefix($mod['prefix_url'])
            ->namespace($this->namespace)
            ->group($route_file);

        if(is_dir($view_dir)){
            $this->loadViewsFrom($view_dir, $mod['folder']);
        }

    }

    protected function mapApi(array $mod){
        $route_dir = implode(DIRECTORY_SEPARATOR, [dirname(__DIR__, 2), 'Modules', $mod['folder'], 'Routes']);
        $entries = scandir($route_dir);
        foreach($entries as $f){
            if($f == '.' || $f == '..')
                continue;

            $route_file = implode(DIRECTORY_SEPARATOR, [dirname(__DIR__, 2), 'Modules', $mod['folder'], 'Routes', $f]);
            $b = explode('.', $f);
            $middleware = [];
            if(is_array($mod['group_middleware']) && !empty($mod['group_middleware'])){
                $middleware = array_merge($middleware, $mod['group_middleware']);
            }
            Route::middleware($middleware)
                ->prefix($mod['prefix_url'])
                ->namespace($this->namespace)
                ->group($route_file);
        }
    }
}
