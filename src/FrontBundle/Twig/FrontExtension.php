<?php

namespace FrontBundle\Twig;

class FrontExtension extends \Twig_Extension
{

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('type_color', array($this, 'typeColor')),
        );
    }

    /**
     * @param $type
     * @return string
     */
    public function typeColor($type)
    {
        switch($type) {

            case 'Musique':
                $color = 'label-danger';
                break;

            case 'Animation':
            case 'Audio':
            case 'Bds':
            case 'Comics':
            case 'Livres':
                $color = 'label-warning';
                break;

            case 'Windows':
            case 'Linux':
            case 'Sony':
            case 'Nintendo':
                $color = 'label-success';
                break;

            default:
                $color = 'label-default';
                break;
        }
        return $color;
    }
}