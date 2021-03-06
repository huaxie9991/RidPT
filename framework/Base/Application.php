<?php

namespace Rid\Base;

use DI\Container;
use Rid\Helpers\ContainerHelper;

class Application
{
    // 初始化回调
    public array $config = [];

    public array $initialize = [];

    protected ?Container $container;

    public function __construct(array $config)
    {
        $this->config = $config;

        // 执行初始化回调
        $this->initialize = $this->config['initialize'] ?? [];
        foreach ($this->initialize as $callback) {
            call_user_func($callback);
        }

        $this->buildContainer();
    }

    protected function buildContainer()
    {
        $builder = new \DI\ContainerBuilder();
        $builder->addDefinitions($this->config['components']);

        $container = $builder->build();
        ContainerHelper::setContainer($container);
        $this->container = $container;
    }

    /**
     * @return Container|null
     */
    public function getContainer(): ?Container
    {
        return $this->container;
    }
}
