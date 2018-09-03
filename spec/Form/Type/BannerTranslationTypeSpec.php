<?php

namespace spec\Odiseo\SyliusBannerPlugin\Form\Type;

use Odiseo\SyliusBannerPlugin\Model\BannerTranslation;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Diego D'amico <diego@odiseo.com.ar>
 */
final class BannerTranslationTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(BannerTranslation::class, ['odiseo']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Odiseo\SyliusBannerPlugin\Form\Type\BannerTranslationType');
    }

    function it_should_be_abstract_resource_type_object()
    {
        $this->shouldHaveType(AbstractResourceType::class);
    }

    function it_build_form_with_proper_fields(
        FormBuilderInterface $builder,
        FormFactoryInterface $factory
    ) {
        $builder->getFormFactory()->willReturn($factory);

        $builder->add('imageFile', FileType::class, Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('url', TextType::class, Argument::any())->shouldBeCalled()->willReturn($builder);


        $this->buildForm($builder, []);
    }
}
