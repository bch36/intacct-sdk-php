<?php

/**
 * Copyright 2017 Intacct Corporation.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"). You may not
 * use this file except in compliance with the License. You may obtain a copy
 * of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * or in the "LICENSE" file accompanying this file. This file is distributed on
 * an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

namespace Intacct;

use Intacct\Credentials\LoginCredentials;
use Intacct\Credentials\SenderCredentials;
use Intacct\Credentials\SessionCredentials;
use Intacct\Functions\FunctionInterface;
use Intacct\Xml\AbstractResponse;

abstract class AbstractClient
{

    /**
     * Profile environment name
     *
     * @var string
     */
    const PROFILE_ENV_NAME = 'INTACCT_PROFILE';

    /** @var ClientConfig */
    private $config;

    /**
     * AbstractClient constructor.
     *
     * @param ClientConfig $config
     */
    public function __construct(ClientConfig $config = null)
    {
        if (!$config) {
            $config = new ClientConfig();
        }

        if (!$config->getProfileName()) {
            $config->setProfileName(getenv(static::PROFILE_ENV_NAME));
        }

        if ($config->getCredentials() instanceof SessionCredentials
            || $config->getCredentials() instanceof LoginCredentials) {
            // Do not try and load credentials if they are already set in config
        } elseif ($config->getSessionId()) {
            // Load the session credentials
            $config->setCredentials(new SessionCredentials($config, new SenderCredentials($config)));
        } else {
            // Load the login credentials
            $config->setCredentials(new LoginCredentials($config, new SenderCredentials($config)));
        }
        $this->setConfig($config);
    }

    /**
     * @return ClientConfig
     */
    public function getConfig(): ClientConfig
    {
        return $this->config;
    }

    /**
     * @param ClientConfig $config
     */
    public function setConfig(ClientConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param FunctionInterface[] $content
     * @param RequestConfig $requestConfig
     * @return AbstractResponse
     */
    abstract public function execute(array $content, RequestConfig $requestConfig);
}