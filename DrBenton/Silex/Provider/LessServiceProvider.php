<?php

namespace DrBenton\Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

use DrBenton\Component\LessCompiler;

class LessServiceProvider implements ServiceProviderInterface
{
    
    
    public function register(Application $app)
    {
        
        if (! isset($app['less.node_path'])) {
            $app['less.node_path'] = '/usr/bin/node';
        }
        
        $compiler = new LessCompiler();
        $compiler->debug = $app['debug'];

        if (isset($app['monolog'])) {
            $compiler->setLogger($app['monolog']);
        }
        if (isset($app['less.compress'])) {
            $compiler->compress = $app['less.compress'];
        }
        if (isset($app['less.node_path'])) {
            $compiler->nodePath = $app['less.node_path'];
        }
        if (isset($app['less.node_less_module_path'])) {
            $compiler->lessModulePath = $app['less.node_less_module_path'];
        }
        if (isset($app['less.tmp_folder'])) {
            $compiler->tmpFolder = $app['less.tmp_folder'];
        }

        if (isset($app['twig'])) {

            $app->before( function() use ($app, $compiler) {

                $twigExtensionCompilationClosure = function() use ($app, $compiler) {
                    call_user_func_array( array($compiler, 'compile'), func_get_args() );
                    return '';
                };
                $twigExtension = new \DrBenton\Twig\Extension\LessCompilerExtension ($twigExtensionCompilationClosure);
                $app['twig']->addExtension($twigExtension);
                
                if (isset($app['less.web_files_foler_path'])) {
                    $twigExtension->setWebFilesFolderPath($app['less.web_files_foler_path']);
                }

            });

        }
        

        $app['less'] = $app->share( function() use($compiler) {
            
            return $compiler;
            
        });
            
    }
    
    
}