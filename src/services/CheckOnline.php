<?php namespace professionalweb\chekonline\services;

use professionalweb\taxes\interfaces\Receipt;
use professionalweb\chekonline\interfaces\CheckOnline as ICheckOnline;

/**
 * Wrapper for checkonline service
 * @package professionalweb\chekonline\services
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
    private $requestId;

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
     * @param string $requestId
     *
     * @return self
     */
    public function setRequestId(string $requestId): ICheckOnline
    {
        $this->requestId = $requestId;

        return $this;
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
        $params = [
            'Device'       => $this->device,
            'RequestId'    => $this->requestId,
            'Lines'        => $this->lines,
            'NonCash'      => [$this->nonCash],
            'TaxMode'      => $this->taxMode,
            'PhoneOrEmail' => $this->phoneOrEmail,
        ];

        $products[] = [
            'Qty'          => $qty * 1000,
            'Price'        => $price * 100,
            'PayAttribute' => $payAttribute,
            'TaxId'        => $taxId,
            'Description'  => $description,
        ];

        $total += $price * $qty * 100;

        $json = json_encode($params);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        curl_setopt($curl, CURLOPT_URL, $this->url);
        // Сертификат
        curl_setopt($curl, CURLOPT_SSLCERT, $this->cert);
        // Закрытый ключ
        curl_setopt($curl, CURLOPT_SSLKEY, $this->key);
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
}