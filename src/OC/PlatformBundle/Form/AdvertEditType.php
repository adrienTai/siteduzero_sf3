<?php

namespace OC\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use OC\PlatformBundle\Form\Type\ImageType;

class AdvertEditType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$pattern = 'D%';
        $builder->remove('date');
        $builder->remove('published');
    }
    
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'oc_platformbundle_advertedit';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return AdvertType::class;
    }


}
