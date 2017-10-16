<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace raklib\server;

use raklib\protocol\EncapsulatedPacket;

interface ServerInstance{

    /**
     * @param string     $identifier
     * @param string     $address
     * @param int        $port
     * @param string|int $clientID
     */
    public function openSession($identifier, $address, $port, $clientID);

    /**
     * @param string $identifier
     * @param string $reason
     */
    public function closeSession($identifier, $reason);

    /**
     * @param string             $identifier
     * @param EncapsulatedPacket $packet
     * @param int                $flags
     */
    public function handleEncapsulated($identifier, $buffer);

    /**
     * @param string $address
     * @param int    $port
     * @param string $payload
     */
    public function handleRaw($address, $port, $payload);
    
    /**
     * @param string $option
     * @param string $value
     */
    public function handleOption($option, $value);
    
}
