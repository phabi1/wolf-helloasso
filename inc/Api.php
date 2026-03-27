<?php

namespace Wolf\HelloAsso;

use Wolf\Core\Di\ContainerAwareInterface;
use Wolf\Core\Di\ContainerAwareTrait;
use Wolf\Core\Plugin;

class Api implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function setup()
    {
        add_action('rest_api_init', function () {
            $this->registerMappingRoutes();
        });
    }

    protected function getContainer()
    {
        if ($this->container === null) {
            $this->setContainer(Plugin::getContainer());
        }
        return $this->container;
    }

    protected function getController($controllerName)
    {
        return $this->getContainer()->get($controllerName);
    }

    protected function registerMappingRoutes()
    {
        $controller = $this->getController('wolf-helloasso.controller.mapping');
        register_rest_route('wolf-helloasso/v1', 'mapping/synchronize', [
            'methods' => 'POST',
            'callback' => [$controller, 'synchronize'],
            'permission_callback' => '__return_true'
        ]);
    }
}