<?php

namespace Clapp\OtpHu\Response;

use Omnipay\Common\Message\AbstractResponse;
use SimpleXMLElement;
use Omnipay\Common\Message\RequestInterface;
use Clapp\OtpHu\BadResponseException;
use Exception;

class GenerateTransactionIdResponse extends AbstractResponse
{
    protected $transactionId = null;

    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        try {
            $payload = base64_decode((new SimpleXMLElement($data))->xpath('//result')[0]->__toString());
            $transactionIdElement = (new SimpleXMLElement($payload))->xpath('//id');
            if (empty($transactionIdElement)) {
                throw new BadResponseException('missing transaction id from response');
            }
            $this->transactionId = $transactionIdElement[0]->__toString();
        } catch (BadResponseException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new BadResponseException($data);
        }
    }

    /**
     * @override
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    public function isSuccessful()
    {
        return $this->getTransactionId() !== null;
    }
}
