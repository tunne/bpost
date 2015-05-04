<?php

namespace TijsVerkoyen\Bpost\Bpost\Order\Box\Option;

use TijsVerkoyen\Bpost\Exception;

/**
 * bPost Insurance class.
 *
 * @author    Tijs Verkoyen <php-bpost@verkoyen.eu>
 *
 * @version   3.0.0
 *
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class Insured extends Option
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $value;

    /**
     * @return array
     */
    public static function getPossibleTypeValues()
    {
        return array(
            'basicInsurance' => 'common:basicInsurance',
            'additionalInsurance' => 'common:additionalInsurance',
        );
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        if (!in_array($type, array_keys(self::getPossibleTypeValues()))) {
            throw new Exception(
                sprintf(
                    'Invalid value, possible values are: %1$s.',
                    implode(', ', array_keys(self::getPossibleTypeValues()))
                )
            );
        }
        $types = self::getPossibleTypeValues();
        $this->type = $types[$type];
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        if (!in_array($value, self::getPossibleValueValues())) {
            throw new Exception(
                sprintf(
                    'Invalid value, possible values are: %1$s.',
                    implode(', ', self::getPossibleValueValues())
                )
            );
        }

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public static function getPossibleValueValues()
    {
        return array(
            1,
            2,
            3,
            4,
            5,
            6,
            7,
            8,
            9,
            10,
            11,
        );
    }

    /**
     * @param string      $type
     * @param string\null $value
     */
    public function __construct($type, $value = null)
    {
        $this->setType($type);
        if ($value !== null) {
            $this->setValue($value);
        }
    }

    /**
     * Return the object as an array for usage in the XML.
     *
     * @param \DomDocument $document
     * @param string       $prefix
     *
     * @return \DomElement
     */
    public function toXML(\DOMDocument $document, $prefix = null)
    {
        $tagName = 'common:insured';
        if ($prefix !== null) {
            $tagName = $prefix.':'.$tagName;
        }
        $insured = $document->createElement($tagName);

        $tagName = $this->getType();
        if ($prefix !== null) {
            $tagName = $prefix.':'.$tagName;
        }
        $insurance = $document->createElement($tagName);
        $insured->appendChild($insurance);

        if ($this->getValue() !== null) {
            $insurance->setAttribute('value', $this->getValue());
        }

        return $insured;
    }

    /**
     * Return the object created from xml.
     *
     * @param \SimpleXMLElement $xml
     *
     * @return \TijsVerkoyen\Bpost\Bpost\Order\Box\Option\Insured|null
     */
    public static function createFromXML(\SimpleXMLElement $xml)
    {
        $insurance = $xml->children('http://schema.post.be/shm/deepintegration/v3/common');
        if (isset($insurance->additionalInsurance)) {
            return new self('additionalInsurance', (int) $insurance->attributes()['value']);
        }
        if (isset($insurance->basicInsurance)) {
            return new self('basicInsurance'); // should never happen (bpost returns additional with value 1)
        }

        return;
    }
}
