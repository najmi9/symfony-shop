<?php

declare(strict_types=1);

namespace App\Service\Paypal;

class Payment
{
    private string $id;

    private float $total;

    public string $firstname = '';

    public string $lastname = '';

    public string $address = '';

    public string $city = '';

    public string $postalCode = '';

    public string $countryCode = 'FR';

    public float $vat = 0;

    public float $fee = 0;

    private string $currency = '$';

    private array $items = [];

    private string $description = '';

    private float $shippingPrice;

    private float $handlingPrice;

    private float $subTotal;

    private array $images = [];

    /**
     * Get the value of total
     */ 
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set the value of total
     *
     * @return  self
     */ 
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get the value of address
     */ 
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set the value of address
     *
     * @return  self
     */ 
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get the value of countryCode
     */ 
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Set the value of countryCode
     *
     * @return  self
     */ 
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * Get the value of currency
     */ 
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set the value of currency
     *
     * @return  self
     */ 
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get the value of items
     */ 
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set the value of items
     *
     * @return  self
     */ 
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Get the value of description
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */ 
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of shippingPrice
     */ 
    public function getShippingPrice()
    {
        return $this->shippingPrice;
    }

    /**
     * Set the value of shippingPrice
     *
     * @return  self
     */ 
    public function setShippingPrice($shippingPrice)
    {
        $this->shippingPrice = $shippingPrice;

        return $this;
    }

    /**
     * Get the value of handlingPrice
     */ 
    public function getHandlingPrice()
    {
        return $this->handlingPrice;
    }

    /**
     * Set the value of handlingPrice
     *
     * @return  self
     */ 
    public function setHandlingPrice($handlingPrice)
    {
        $this->handlingPrice = $handlingPrice;

        return $this;
    }

    /**
     * Get the value of subTotal
     */ 
    public function getSubTotal()
    {
        return $this->subTotal;
    }

    /**
     * Set the value of subTotal
     *
     * @return  self
     */ 
    public function setSubTotal($subTotal)
    {
        $this->subTotal = $subTotal;

        return $this;
    }

    /**
     * Get the value of images
     */ 
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set the value of images
     *
     * @return  self
     */ 
    public function setImages($images)
    {
        $this->images = $images;

        return $this;
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}