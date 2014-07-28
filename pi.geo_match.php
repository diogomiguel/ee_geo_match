<?php

$plugin_info = array(
    'pi_name' => 'Geo Match',
    'pi_version' => '0.0.1',
    'pi_author' => 'Diogo Silva',
    'pi_author_url' => 'https://github.com/diogomiguel/ee_geo_match',
    'pi_description' => 'Use GeoPlugin to find users location and/or match it against provided country(ies)',
    'pi_usage' => Geo_match::usage()
);

class Geo_match
{
    
    public $return_data = '';
    private $ip_address;
    
    
    /** 
     * Constructor
     *
     * Evaluates case values and extracts the content of the 
     * first case that matches the variable parameter
     *
     * @access public
     * @return void
     */
    public function Geo_match()
    {
        $this->EE =& get_instance();
        
        // Force session
        if (session_id() == "") {
            session_start();
        }
        
        
        
        // If no session set
        if (!isset($_SESSION['exp_geo_located'])) {
        
            // Get IP address
            $this->ip_address = $this->get_ip_address();
            
            // Locate and assign to session var - If IP not valid use Geoplugin default detector.
            $geo_locate = unserialize(file_get_contents('http://www.geoplugin.net/php.gp' . ($this->ip_address !== false ? '?ip=' . $this->ip_address : '')));
            
            $_SESSION['exp_geo_located'] = strtolower(trim($geo_locate['geoplugin_countryCode']));

        }
        
        
        
        // Has country variable ?
        $countries = $this->EE->TMPL->fetch_param('countries') ? explode(',', $this->EE->TMPL->fetch_param('countries')) : FALSE;
        
        // If countries is a comparison
        if ($countries) {
            if (in_array($_SESSION['exp_geo_located'], $countries)) {
                $this->return_data = 1;
            } else {
                $this->return_data = 0;
            }
        } else {
            $this->return_data = $_SESSION['exp_geo_located'];
        }
        
    }
    
    // Functions courtesy from @cballou (https://gist.github.com/cballou/2201933)
    
    
    private function get_ip_address()
    {
        $ip_keys = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        );
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    // trim for safety measures
                    $ip = trim($ip);
                    // attempt to validate IP
                    if ($this->validate_ip($ip)) {
                        return $ip;
                    }
                }
            }
        }
        return isset($_SERVER['REMOTE_ADDR']) ? ($this->validate_ip($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false) : false;
    }
    
    
    /**
     * Ensures an ip address is both a valid IP and does not fall within
     * a private network range.
     */
    private function validate_ip($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return false;
        }
        return true;
    }
    
    
    
    
    
    // usage instructions
    public function usage()
    {
        ob_start();
?>
-------------------
HOW TO USE
-------------------
{exp:geo_match countries="gb,fr,de" /}

If countries param is empty, it will return the user country code.
If not it will return 1 (true) if it matches with any of the provided country coded or 0 (false) if it doesn't.

Countries code list here: http://www.geoplugin.com/iso3166
    <?php
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }
}