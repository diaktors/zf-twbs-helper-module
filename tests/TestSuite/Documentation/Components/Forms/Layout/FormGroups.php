<?php

// Documentation test config file for "Components / Forms / Layout / Form groups" part
return [
    'title' => 'Form groups',
    'url' => '%bootstrap-url%/components/forms/#form-groups',
    'rendering' => function (\Zend\View\Renderer\PhpRenderer $oView) {
        $oFactory = new \Zend\Form\Factory();

        echo $oView->form($oFactory->create([
            'type' => 'form',
            'elements' => [
                [
                    'spec' => [
                        'name' => 'exampleInput',
                        'options' => [
                            'label' => 'Example label',
                        ],
                        'attributes' => [
                            'type' => 'text',
                            'id' => 'formGroupExampleInput',
                            'placeholder' => 'Example input',
                        ],
                    ],
                ],
                [
                    'spec' => [
                        'name' => 'exampleInput2',
                        'options' => [
                            'label' => 'Another label',
                        ],
                        'attributes' => [
                            'type' => 'text',
                            'id' => 'formGroupExampleInput2',
                            'placeholder' => 'Another input',
                        ],
                    ],
                ],
            ]
        ]));
    },
    'expected' => '<form method="POST" name="form" role="form" id="form">' . PHP_EOL .
        '    <div class="form-group">' . PHP_EOL .
        '        <label for="formGroupExampleInput">Example label</label>' . PHP_EOL .
        '        <input name="exampleInput" type="text" id="formGroupExampleInput" ' .
        'placeholder="Example&#x20;input" class="form-control" value="">' . PHP_EOL .
        '    </div>' . PHP_EOL .
        '    <div class="form-group">' . PHP_EOL .
        '        <label for="formGroupExampleInput2">Another label</label>' . PHP_EOL .
        '        <input name="exampleInput2" type="text" id="formGroupExampleInput2" ' .
        'placeholder="Another&#x20;input" class="form-control" value="">' . PHP_EOL .
        '    </div>' . PHP_EOL .
        '</form>',
];
