<?php

namespace App\Manager;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class RealTimeManager
{
   
  
    public function Walker($data , $hub){
        $update = new Update(
            'https://walkeryessine.com/walker/1',
            json_encode([$data])
        );

        $hub->publish($update);
    }

}












?>