<?php

namespace Frigg\FlightBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FlightType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('remote')
            ->add('code')
            ->add('dom_int')
            ->add('schedule_time')
            ->add('created_at')
            ->add('modified_at')
            ->add('arr_dep')
            ->add('check_in')
            ->add('gate')
            ->add('is_delayed')
            ->add('flight_status_time')
            ->add('airline')
            ->add('airport')
            ->add('other_airport')
            ->add('flight_status')
            ->add('via_airports')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frigg\FlightBundle\Entity\Flight'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frigg_flightbundle_flight';
    }
}
