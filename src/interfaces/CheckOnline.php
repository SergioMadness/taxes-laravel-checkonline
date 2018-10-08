<?php namespace professionalweb\taxes\chekonline\interfaces;

use professionalweb\taxes\interfaces\TaxService;

interface CheckOnline extends TaxService
{
    public const DRIVER_CHECKONLINE = 'checkonline';

    public const DEFAULT_DEVICE = 'auto';

    public const FULL_PAY = 1;

    public const PART_PAY = 2;

    /**
     * @param string $device
     *
     * @return self
     */
    public function setDevice(string $device): self;
}