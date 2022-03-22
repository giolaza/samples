<?php


namespace App\Orders\Traits;


trait Client
{
    /**
     * Search client info in DB
     *
     * @return mixed
     */
    public function getClientInfo()
    {
        $this->checkOrder();
        $this->checkDB();

        if (isset($this->order['__ClientInfo'])) {
            //this method was already called
            return $this->order['__ClientInfo'];
        }

        if ($this->order['client_id']) {
            $q = 'SELECT * from `clients` WHERE `id`=' . $this->order['client_id'] . ' LIMIT 1';
            $this->order['__ClientInfo'] = $this->db->do_one($q);
        } else {
            $this->order['__ClientInfo'] = [];
        }

        return $this->order['__ClientInfo'];
    }

    /**
     * @return array|mixed
     */
    public function getClientRegionInfo()
    {
        if (isset($this->order['__ClientRegionInfo'])) {
            return $this->order['__ClientRegionInfo'];
        }

        $Client = $this->getClientInfo();

        if (!$Client) {
            return [];
        }

        $q = 'SELECT * from `site_url` WHERE `id`=' . intval($Client['region']);
        $this->order['__ClientRegionInfo'] = $this->db->do_one($q);

        if (!$this->order['__ClientRegionInfo']) {
            $currency = $this->order['pay_valuta'];
            if ($currency == 2) {
                $q = 'SELECT * from `site_url` WHERE `id` = 2';
            } elseif ($currency == 3) {
                $q = 'SELECT * from `site_url` WHERE `id` = 7';
            } else {
                $q = 'SELECT * from `site_url` WHERE `id` = 1';
            }
            $this->order['__ClientRegionInfo'] = $this->db->do_one($q);
        }

        return $this->order['__ClientRegionInfo'];
    }

    public function getClienRegionId()
    {
        $Region = $this->getClientRegionInfo();
        $RegionId = 0;
        if ($Region['countryId']) {
            $RegionId = $Region['countryId'];
        } elseif ($this->data('pay_valuta') == 2) {
            $RegionId = 2;
        } elseif ($this->data('pay_valuta') == 3) {
            $RegionId = 3;
        } else {
            $RegionId = 1;
        }

        return $RegionId;
    }

    public function getCountryInfo()
    {
        if (isset($this->order['__CountryInfo'])) {
            return $this->order['__CountryInfo'];
        }

        $region = $this->getClientRegionInfo();
        if (!$region || !$region['countryId']) {
            if ($this->order['pay_valuta'] == 2) {
                $q = 'SELECT * from `sys_country` WHERE `country_id` = 2';
            } elseif ($this->order['pay_valuta'] == 3) {
                $q = 'SELECT * from `sys_country` WHERE `country_id` = 3';
            } else {
                $q = 'SELECT * from `sys_country` WHERE `country_id` = 1';
            }
        } else {
            $q = 'SELECT * from `sys_country` WHERE `country_id` = ' . $region['countryId'];
        }
        $this->order['__CountryInfo'] = $this->db->do_one($q);
        return $this->order['__CountryInfo'];
    }
}
