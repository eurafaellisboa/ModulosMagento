<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blueskytechco\MenuBuilder\Plugin;

use Blueskytechco\MenuBuilder\Block\Widget\Menus as MenusWidget;
use Blueskytechco\MenuBuilder\ViewModel\MenuBuilder as MenuBuilderViewModel;

class AddViewModelMenusWidget
{
    /**
     * @var MenuBuilderViewModel
     */
    private $viewModel;

    public function __construct(
        MenuBuilderViewModel $viewModel
    ) {
        $this->viewModel = $viewModel;
    }

    /**
     * @param ExampleBlock $exampleBlock
     * @return array
     */
    public function beforeToHtml(MenusWidget $menusWidget)
    {
        $menusWidget->assign('viewModel', $this->viewModel);
        return [];
    }
}
