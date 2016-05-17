<?php

declare(strict_types = 1);

namespace OctoLab\Common\Monolog\Service;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class ComponentBuilder
{
    /** @var string[] */
    private static $dict = [
        'chrome_php' => 'ChromePHP',
        'mongo_db' => 'MongoDB',
        'mongodb' => 'MongoDB',
        'couch_db' => 'CouchDB',
        'couchdb' => 'CouchDB',
        'doctrine_couch_db' => 'DoctrineCouchDB',
        'doctrine_couchdb' => 'DoctrineCouchDB',
        'fire_php' => 'FirePHP',
        'ifttt' => 'IFTTT',
        'php_console' => 'PHPConsole',
    ];

    /** @var null|string */
    private $class;
    /** @var null|string */
    private $namespace;
    /** @var null|string */
    private $suffix;
    /** @var string[] */
    private $dependencies = [];

    /**
     * @param string $key
     * @param string $method
     *
     * @return ComponentBuilder
     */
    public function addDependency(string $key, string $method): ComponentBuilder
    {
        $this->dependencies[$key] = $method;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * @param string $class
     *
     * @return ComponentBuilder
     */
    public function setClass(string $class): ComponentBuilder
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @param string $namespace
     *
     * @return ComponentBuilder
     */
    public function setNamespace(string $namespace): ComponentBuilder
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * @param string $suffix
     *
     * @return ComponentBuilder
     */
    public function setSuffix(string $suffix): ComponentBuilder
    {
        $this->suffix = $suffix;
        return $this;
    }

    /**
     * @param null|string $class
     * @param null|string $type
     * @param array $args
     *
     * @return \Monolog\Logger|\Monolog\Handler\HandlerInterface|\Monolog\Formatter\FormatterInterface|callable
     *
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    public function make(string $class = null, string $type = null, array $args = [])
    {
        $class = $class ?? $this->class ?? $this->resolveClassName($type);
        $reflection = new \ReflectionClass($class);
        if ($args !== [] && !is_int(key($args))) {
            $args = $this->resolveConstructorArguments($args, $reflection);
        }
        return $reflection->newInstanceArgs($args);
    }

    /**
     * @param null|string $type
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    private function resolveClassName(string $type = null): string
    {
        if ($type === null) {
            throw new \InvalidArgumentException('Component type is not provided.');
        }
        if (isset(self::$dict[$type])) {
            $class = self::$dict[$type];
        } else {
            $class = implode('', explode(' ', ucwords(str_replace('_', ' ', $type))));
        }
        return $this->namespace . '\\' . $class . $this->suffix;
    }

    /**
     * @param array $args
     * @param \ReflectionClass $reflection
     *
     * @return array
     */
    private function resolveConstructorArguments(array $args, \ReflectionClass $reflection): array
    {
        $params = [];
        foreach ($reflection->getConstructor()->getParameters() as $param) {
            $params[$param->getName()] = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
        }
        return array_merge($params, array_intersect_key($args, $params));
    }
}