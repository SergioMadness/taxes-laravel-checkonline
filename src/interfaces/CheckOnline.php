<?php namespace professionalweb\chekonline\interfaces;

use professionalweb\taxes\interfaces\TaxService;

interface CheckOnline extends TaxService
{
    public const DRIVER_CHECKONLINE = 'checkonline';

    public const DEFAULT_DEVICE = 'auto';

    /**
     * @param string $device
     *
     * @return self
     */
    public function setDevice(string $device): self;

    /**
     * @param string $requestId
     *
     * @return self
     */
    public function setRequestId(string $requestId): self;
}