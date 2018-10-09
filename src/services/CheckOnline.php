<?php namespace professionalweb\taxes\checkonline\services;

use professionalweb\taxes\interfaces\Receipt;
use professionalweb\taxes\interfaces\ReceiptItem;
use professionalweb\taxes\models\TaxServiceOption;
use professionalweb\taxes\checkonline\interfaces\CheckOnline as ICheckOnline;
use professionalweb\taxes\interfaces\TaxServiceOption as ITaxServiceOption;

/**
 * Wrapper for checkonline service
 * @package professionalweb\taxes\checkonline\services
 */
class CheckOnline implements ICheckOnline
{
    /**
     * @var string
     */
    private $device;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $cert;

    /**
     * @var string
     */
    private $key;

    public function __construct(string $url = '', string $cert = '', string $key = '', string $device = '')
    {
        $this->setUrl($url)->setCert($cert)->setKey($key)->setDevice($device);
    }

    /**
     * Send receipt
     *
     * @param Receipt $receipt
     *
     * @return mixed
     * @throws \Exception
     */
    public function sendReceipt(Receipt $receipt)
    {
        $products = [];
        $totalSum = 0;
        foreach ($receipt->getItems() as $item) {
            /** @var ReceiptItem $item */
            $products[] = [
                'Qty'          => $item->getQty() * 1000,
                'Price'        => $item->getPrice() * 100,
                'PayAttribute' => self::FULL_PAY,
                'TaxId'        => $item->getTax(),
                'Description'  => $item->getName(),
            ];
            $totalSum += $item->getPrice() * 100;
        }

        $params = [
            'Device'       => $this->getDevice(),
            'RequestId'    => md5(time() . str_random()),
            'Lines'        => $products,
            'NonCash'      => [$totalSum],
            'TaxMode'      => $receipt->getTaxSystem(),
            'PhoneOrEmail' => $receipt->getContact(),
        ];

        $json = json_encode($params);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_SSLCERT, $this->getCert());
        curl_setopt($curl, CURLOPT_SSLKEY, $this->getKey());
        $response = curl_exec($curl);
        $error = curl_error($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($error) {
            throw new \Exception($error);
        }
        if ($status >= 300) {
            throw new \Exception($status);
        }
        $response = json_decode($response, true);

        if (isset($response['Response']['Error']) && $response['Response']['Error'] > 0) {
            throw new \Exception($response['Response']['Error']);
        }

        return $response;
    }

    /**
     * Get tax service options
     *
     * @return array
     */
    public function getOptions(): array
    {
        return [
            (new TaxServiceOption())->setAlias('url')->setLabel('Url')->setType(ITaxServiceOption::TYPE_STRING),
            (new TaxServiceOption())->setAlias('cert')->setLabel('Certificate')->setType(ITaxServiceOption::TYPE_FILE),
            (new TaxServiceOption())->setAlias('key')->setLabel('Key')->setType(ITaxServiceOption::TYPE_FILE),
        ];
    }

    //<editor-fold desc="Getters and setters">

    /**
     * @param string $device
     *
     * @return self
     */
    public function setDevice(string $device): ICheckOnline
    {
        $this->device = $device;

        return $this;
    }

    /**
     * @return string
     */
    public function getDevice(): string
    {
        return $this->device;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getCert(): string
    {
        return $this->cert;
    }

    /**
     * @param string $cert
     *
     * @return $this
     */
    public function setCert(string $cert): self
    {
        $this->cert = $cert;

        return $this;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }
    //</editor-fold>
}