<?php 

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;

class PostType extends AbstractType{
	public function buildForm(FormBuilderInterface $builder, array $option){
		$builder
			->add('title')
            ->add('content')
            ->add('image', Type\FileType::class, ['required' => false])
            ->add('submit', Type\SubmitType::class );
	}
}