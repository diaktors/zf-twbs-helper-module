<?php

// Documentation test config file for "Components / Forms / Layout / Form grid / Form row" part
return [
    'title' => 'Form row',
    'url' => '%bootstrap-url%/components/forms/#form-row',

    'rendering' => function (\Zend\View\Renderer\PhpRenderer $oView) {
        $oFactory = new \Zend\Form\Factory();

        echo $oView->form($oFactory->create([
            'type' => 'form',
            'options' => ['row_class' => 'form-row'],
            'elements' => [
                [
                    'spec' => [
                        'name' => 'firstName',
                        'options' => [
                            'column' => true,
                        ],
                        'attributes' => [
                            'type' => 'text',
                            'placeholder' => 'First name',
                        ],
                    ],
                ],
                [
                    'spec' => [
                        'name' => 'lastName',
                        'options' => [
                            'column' => true,
                        ],
                        'attributes' => [
                            'type' => 'text',
                            'placeholder' => 'Last name',
                        ],
                    ],
                ],
            ]
        ]));
    },
    'expected' => '<form method="POST" name="form" role="form" id="form">' . PHP_EOL .
        '    <div class="form-row">' . PHP_EOL .
        '        <div class="col">' . PHP_EOL .
        '            <input name="firstName" type="text" placeholder="First&#x20;name" ' .
        'class="form-control" value="">' . PHP_EOL .
        '        </div>' . PHP_EOL .
        '        <div class="col">' . PHP_EOL .
        '            <input name="lastName" type="text" placeholder="Last&#x20;name" ' .
        'class="form-control" value="">' . PHP_EOL .
        '        </div>' . PHP_EOL .
        '    </div>' . PHP_EOL .
        '</form>',
];
