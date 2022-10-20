<?php
namespace WhatsappBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return array(
            new TwigFilter('customdate', array($this, 'formatDate')),
        );
    }

    public function formatDate($date)
    {
         
        if(!$date)
            return "Tiempo indeterminado";
        
        $now = new \DateTime("now");
        
        $diff = $now->diff($date);        
        $dias = $diff->days;
//        dump($date);die;
        if($dias > 0){
            return "hace ".$dias." días";
        }
        $hours = $diff->h;
        if($hours > 0){
            return "hace ".$hours." horas";
        }
        $minutes = $diff->i;
        if($minutes > 0){
            return "hace ".$minutes." miutos";
        }
        $seconds = $diff->s;
        if($seconds > 0){
            return "hace ".$seconds." segundos";
        }
        return "hace 0 segundos";
    }
}