<?php

namespace Pim\Bundle\TranslationBundle\Form\Type;

use Symfony\Component\Form\Exception\FormException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\ValidatorInterface;
use Pim\Bundle\ConfigBundle\Manager\LocaleManager;
use Pim\Bundle\TranslationBundle\Form\Subscriber\AddTranslatableFieldSubscriber;
use Pim\Bundle\TranslationBundle\Factory\TranslationFactory;

/**
 * Translatable field type for translation entities
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2012 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */
class TranslatableFieldType extends AbstractType
{

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var LocaleManager
     */
    protected $localeManager;

    /**
     * @var string
     */
    protected $defaultLocale;

    /**
     * @param ValidatorInterface $validator
     * @param LocaleManager      $localeManager
     * @param string             $defaultLocale
     */
    public function __construct(ValidatorInterface $validator, LocaleManager $localeManager, $defaultLocale)
    {
        $this->validator     = $validator;
        $this->localeManager = $localeManager;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!class_exists($options['entity_class'])) {
            throw new FormException('unable to find entity class');
        }

        if (!class_exists($options['translation_class'])) {
            throw new FormException('unable to find translation class');
        }

        if (!$options['field']) {
            throw new FormException('must provide a field');
        }

        if (!is_array($options['required_locale'])) {
            throw new FormException('required locale(s) must be an array');
        }

        $subscriber = new AddTranslatableFieldSubscriber(
            $builder->getFormFactory(),
            $this->validator,
            $options
        );
        $builder->addEventSubscriber($subscriber);
    }

    /**
     * {@inheritdoc}
     *
     * - translation_class    : FQCN of the translation class
     * - entity_class         : FQCN of the based entity class
     * - locales              : Locales you wish to edit
     * - default_locale       : Name of the locale for the default translation
     * - required_locale      : Fields are required or not
     * - field                : Field name
     * - widget               : Widget used by translations fields
     * - only_default         : Render only default translation
     */
    public function getDefaultOptions(array $options = array())
    {
        $options['translation_class'] = false;
        $options['entity_class']      = false;
        $options['field']             = false;
        $options['locales']           = $this->getActiveLocales();
        $options['default_locale']    = $this->defaultLocale;
        $options['required_locale']   = array($this->defaultLocale);
        $options['widget']            = 'text';
        $options['only_default']      = false;

        return $options;
    }

    /**
     * Get active locales
     *
     * @return multitype:string
     */
    protected function getActiveLocales()
    {
        $locales = $this->localeManager->getActiveCodes();
        array_unshift($locales, $this->defaultLocale);

        return $locales;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pim_translatable_field';
    }
}
