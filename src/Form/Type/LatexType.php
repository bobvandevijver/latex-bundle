<?php

namespace Bobv\LatexBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * You will want to use this type when using the html->latex conversion
 *
 * @author BobV
 */
class LatexType extends AbstractType
{

  /**
   * Returns the default config used so you can use it as base for your own config
   */
  public static function getDefaultConfig(): array
  {
    return [
        'config' => [
            'entities'         => false,
            'basicEntities'    => false,
            'entities_greek'   => false,
            'entities_latin'   => false,
            'toolbar'          => [
                [
                    'name'   => 'basicstyles',
                    'groups' => ['basicstyles', 'cleanup'],
                    'items'  => ['Bold', 'Italic', 'Underline', 'Subscript', 'Superscript', '-', 'RemoveFormat'],
                ],
                [
                    'name'   => 'paragraph',
                    'groups' => ['list', 'indent', 'blocks', 'align'],
                    'items'  => ['NumberedList', 'BulletedList'
                      /**
                       * @todo Add support for the floats
                       * , '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'
                       */
                    ],
                ],
                [
                    'name'  => 'links',
                    'items' => ['Link', 'Unlink'],
                ],
                [
                    'name'   => 'clipboard',
                    'groups' => ['clipboard', 'undo'],
                    'items'  => ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'],
                ],
                /**
                 * @todo: add support for tables/special chars
                 * array(
                 * 'name'  => 'insert',
                 * 'items' => array('Table', 'SpecialChar'),
                 * ),
                 */
                [
                    'name'   => 'document',
                    'groups' => ['mode', 'document', 'doctools'],
                    'items'  => ['Source'],
                ],
                [
                    'name'  => 'tools',
                    'items' => ['Maximize'],
                ],
            ],
            'extraPlugins'     => '',
            'removeDialogTabs' => 'link:upload;image:Upload;image:advanced;link:advanced',
        ]
    ];
  }

  public function buildView(FormView $view, FormInterface $form, array $options): void
  {
    if (!is_array($view->vars['attr'])) {
      $view->vars['attr'] = [];
    }
    $view->vars['attr']['data-bobv-latex'] = null;
  }

  public function getName(): string
  {
    return $this->getBlockPrefix();
  }

  public function getBlockPrefix() : string
  {
    return 'bobv_latex';
  }

  public function getParent(): ?string
  {
    $ivory_form_class = 'Ivory\CKEditorBundle\Form\Type\CKEditorType';
    $fos_form_class   = 'FOS\CKEditorBundle\Form\Type\CKEditorType';
    $form_class       = class_exists($ivory_form_class) ? $ivory_form_class : $fos_form_class;

    return method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix') ? $form_class : 'ckeditor';
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults(self::getDefaultConfig());
  }
}
