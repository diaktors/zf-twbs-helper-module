<?php

namespace TwbsHelper\Form\View\Helper;

class FormAddOn extends \Laminas\Form\View\Helper\AbstractHelper
{
    use \TwbsHelper\View\Helper\HtmlTrait;

    const POSITION_APPEND = 'append';
    const POSITION_PREPEND = 'prepend';

    /**
     * @var \Laminas\Form\Factory
     */
    protected $formFactory;

    /**
     * @param \Laminas\Form\ElementInterface $oElement
     * @return \TwbsHelper\Form\View\Helper\FormAddOn|string
     */
    public function __invoke(\Laminas\Form\ElementInterface $oElement = null, string $sContent = '')
    {
        return $oElement ? $this->render($oElement, $sContent) : $this;
    }

    public function render(\Laminas\Form\ElementInterface $oElement = null, string $sContent = ''): string
    {
        // Addon
        $bHasAddOn = false;
        $sAddOnId = $oElement->getAttribute('aria-describedby');

        foreach ([self::POSITION_APPEND, self::POSITION_PREPEND] as $sAddOnPosition) {
            if ($aAddOnOptions = $oElement->getOption('add_on_' . $sAddOnPosition)) {
                $bHasAddOn = true;

                $aAttributes = ['class' => 'input-group-' . $sAddOnPosition];

                // Define global add-on id based on element's aria-describedby
                if ($sAddOnId && \Laminas\Stdlib\ArrayUtils::isList($aAddOnOptions)) {
                    $aAttributes['id'] = $sAddOnId;
                }

                $sAddOnContent = $this->htmlElement(
                    'div',
                    $aAttributes,
                    $this->renderAddOn($aAddOnOptions, $oElement, $sAddOnId)
                );

                if ($sAddOnPosition === self::POSITION_APPEND) {
                    $sContent .= ($sContent ? PHP_EOL : '') . $sAddOnContent;
                } else {
                    $sContent = $sAddOnContent . ($sContent ? PHP_EOL . $sContent : '');
                }
            }
        }

        if (!$bHasAddOn) {
            return $sContent;
        }

        $aInputGroupClasses = ['input-group'];

        // Input group size
        if ($sSize = $oElement->getOption('size')) {
            $aInputGroupClasses[] = $this->getSizeClass($sSize, 'input-group');
        }

        return $this->htmlElement(
            'div',
            $this->setClassesToAttributes(
                [
                    'class' => $oElement->getOption('input_group_class'),
                ],
                $aInputGroupClasses
            ),
            $sContent
        );
    }

    /**
     * Render add-on markup
     *
     * @param \Laminas\Form\ElementInterface|array|string $aAddOnOptions
     * @param  string $sPosition
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @return string
     */
    protected function renderAddOn(
        $aAddOnOptions,
        \Laminas\Form\ElementInterface $oElement,
        string $sAddOnId = null
    ): string {
        if ($aAddOnOptions instanceof \Laminas\Form\ElementInterface) {
            $aAddOnOptions = ['element' => $aAddOnOptions];
        } elseif (is_scalar($aAddOnOptions)) {
            $aAddOnOptions = ['text' => $aAddOnOptions];
        } elseif (!is_array($aAddOnOptions)) {
            throw new \InvalidArgumentException(sprintf(
                'Addon options expects an array or a scalar value, "%s" given',
                is_object($aAddOnOptions) ? get_class($aAddOnOptions) : gettype($aAddOnOptions)
            ));
        }

        if (\Laminas\Stdlib\ArrayUtils::isList($aAddOnOptions)) {
            $sContent = '';
            foreach ($aAddOnOptions as $aAddOnOptionsTmp) {
                $sContent .= ($sContent ? PHP_EOL : '') . $this->renderAddOn(
                    $aAddOnOptionsTmp,
                    $oElement
                );
            }
            return $sContent;
        }

        // Define add-on id based on element's aria-describedby
        if ($sAddOnId && !isset($aAddOnOptions['attributes']['id'])) {
            $aAddOnOptions['attributes']['id'] = $sAddOnId;
        }

        return $this->renderContent($aAddOnOptions, $oElement);
    }

    protected function renderContent(array $aAddOnOptions, \Laminas\Form\ElementInterface $oElement): string
    {
        $aAttributes = $aAddOnOptions['attributes'] ?? [];

        switch (true) {
            case isset($aAddOnOptions['text']):
                if (!is_string($aAddOnOptions['text'])) {
                    throw new \InvalidArgumentException(sprintf(
                        '"text" option expects a string, "%s" given',
                        is_object($aAddOnOptions['text'])
                            ? get_class($aAddOnOptions['text'])
                            : gettype($aAddOnOptions['text'])
                    ));
                }
                return $this->renderText(
                    $aAddOnOptions['text'],
                    $aAttributes
                );

            case isset($aAddOnOptions['label']):
                if (!is_string($aAddOnOptions['label'])) {
                    throw new \InvalidArgumentException(sprintf(
                        '"label" option expects a string, "%s" given',
                        is_object($aAddOnOptions['label'])
                            ? get_class($aAddOnOptions['label'])
                            : gettype($aAddOnOptions['label'])
                    ));
                }

                return $this->renderLabel(
                    $aAddOnOptions['label'],
                    $aAttributes,
                    $oElement
                );

            case isset($aAddOnOptions['element']):
                if (
                    is_array($aAddOnOptions['element'])
                    && !($aAddOnOptions['element'] instanceof \Laminas\Form\ElementInterface)
                ) {
                    $aAddOnOptions['element'] = $this->createFormElement($aAddOnOptions['element']);
                } elseif (!($aAddOnOptions['element'] instanceof \Laminas\Form\ElementInterface)) {
                    throw new \InvalidArgumentException(sprintf(
                        '"element" option expects an instanceof \Laminas\Form\ElementInterface, "%s" given',
                        is_object($aAddOnOptions['element'])
                            ? get_class($aAddOnOptions['element'])
                            : gettype($aAddOnOptions['element'])
                    ));
                }
                return $this->renderElement(
                    $aAddOnOptions['element'],
                    $aAttributes
                );

            default:
                throw new \InvalidArgumentException('Addon options expects a text or an element to render, none given');
        }
    }

    protected function renderText(string $sAddonText, array $aAttributes): string
    {
        $oTranslator = $this->getTranslator();
        if ($oTranslator) {
            $sAddonText =  $oTranslator->translate(
                $sAddonText,
                $this->getTranslatorTextDomain()
            );
        }
        return $this->renderAddOnElement($sAddonText, $aAttributes);
    }

    protected function renderLabel(
        string $sAddonLabel,
        array $aAttributes,
        \Laminas\Form\ElementInterface $oElement
    ): string {
        return $this->getView()->plugin('formLabel')->__invoke($this->createFormElement([
            'name' => $oElement->getName(),
            'options' => [
                'label' => $sAddonLabel,
                'label_attributes' => $this->setClassesToAttributes(
                    $aAttributes,
                    ['input-group-text']
                ),
            ],
            'attributes' => ['id' => $oElement->getAttribute('id')],
        ]));
    }

    protected function renderElement(\Laminas\Form\ElementInterface $oElement, array $aAttributes): string
    {
        // Set options to improve rendering
        if ($aDropdownOptions = $oElement->getOption('dropdown')) {
            if (\Laminas\Stdlib\ArrayUtils::isList($aDropdownOptions)) {
                $oElement->setOption('dropdown', [
                    'items' => $aDropdownOptions,
                    'disable_container' => true,
                ]);
            } elseif (!isset($aDropdownOptions['disable_container'])) {
                $aDropdownOptions['disable_container'] = true;
                $oElement->setOption('dropdown', $aDropdownOptions);
            }
        }

        $oElement->setAttributes(\Laminas\Stdlib\ArrayUtils::merge(
            $aAttributes,
            $oElement->getAttributes()
        ));

        $oHelper = $this->getView()->plugin('formElement');

        if (
            $oElement instanceof \Laminas\Form\Element\Checkbox
            || $oElement instanceof \Laminas\Form\Element\Radio
        ) {
            $oElement->setOption('disable_twbs', true);
            return $this->renderAddOnElement(
                $oHelper->render($oElement),
                $aAttributes
            );
        }
        return $oHelper->render($oElement);
    }

    protected function renderAddOnElement(string $sAddonText, array $aAttributes = []): string
    {
        return $this->htmlElement(
            'div',
            $this->setClassesToAttributes($aAttributes, ['input-group-text']),
            $sAddonText
        );
    }

    protected function createFormElement(array $aElement): \Laminas\Form\ElementInterface
    {
        if (!$this->formFactory) {
            $this->formFactory = new \Laminas\Form\Factory();
        }
        return $this->formFactory->createElement($aElement);
    }
}
