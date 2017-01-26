<?php
namespace OC\PlatformBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class DatePickerType extends AbstractType
{
    
    public function configureOptions(OptionsResolver $resolver)
    {
    
    	//pas besoin de préciser DateType::class ??
        $resolver->setDefaults(array(
            'widget' => 'single_text',

			// do not render as type="date", to avoid HTML5 date pickers
			'html5' => false,

			'attr' => ['class' => 'js-datepicker'],
		));
    }

    public function getParent()
    {
        return DateType::class;
    }
    
}
