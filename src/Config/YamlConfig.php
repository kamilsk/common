<?php

namespace OctoLab\Common\Config;

use OctoLab\Common\Config\Loader\YamlFileLoader;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class YamlConfig extends SimpleConfig
{
    /** @var YamlFileLoader */
    private $fileLoader;

    /**
     * @param YamlFileLoader $fileLoader
     *
     * @api
     */
    public function __construct(YamlFileLoader $fileLoader)
    {
        parent::__construct();
        $this->fileLoader = $fileLoader;
    }

    /**
     * @param string $resource
     * @param bool $check
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\Config\Exception\FileLoaderLoadException
     * @throws \Symfony\Component\Config\Exception\FileLoaderImportCircularReferenceException
     * @throws \DomainException
     *
     * @api
     */
    public function load($resource, $check = false)
    {
        if ($check && !$this->fileLoader->supports($resource)) {
            throw new \DomainException(sprintf('File "%s" is not supported.', $resource));
        }
        $this->fileLoader->load($resource);
        foreach (array_reverse($this->fileLoader->getContent()) as $data) {
            $this->config = $this->merge($this->config, $data);
        }
        return $this;
    }
}