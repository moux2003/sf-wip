<?php
// src/Acme/TaskBundle/Form/Type/TaskType.php

namespace Lpo\CrudBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class ExempleType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('description', 'textarea', ['required' => false])
            ->add('save', 'submit', ['attr' => ['class' => 'save']])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
            $task = $event->getData();
            $form = $event->getForm();
            //Vérifie si l'id de la tache est renseigné
            //pour adapter le rendu du formulaire à la tache
            if ($task && property_exists($task, 'id')) {
                $form->add('id', 'hidden');
            }
        });
    }

    public function getName()
    {
        return 'exemple';
    }
}
